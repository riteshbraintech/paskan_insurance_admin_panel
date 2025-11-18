<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryFieldFormTranslation;
use App\Models\Categoryformfield;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class CategoryFormFieldController extends Controller
{
    public function index(Request $request)
    {
        $current_page = $request->page ?? 1;
        $search = $request->search ?? "";
        $perPage = $request->perPage ?? 50;
        $isAjax = $request->method;

        $records = Categoryformfield::with('translations');
        if($search){
            $records = $records->whereHas('translations', function($q) use($search){
                $q->where('label', 'like', '%'.$search.'%');
            }); 
        }
        // dd($records);
        $records = $records->sortable('sort_order','asc')->paginate($perPage);

        $categories = Category::with('translations')
            ->get();


        if (!empty($isAjax)) {
            $html = view('admin.categoriesformfield.table', compact('records'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.categoriesformfield.index', compact('records','categories'));
        }

    }

    public function filter(Request $request)
    {
        $perPage = $request->perPage ?? 50;

        $query = CategoryFormField::with('category.translations');
        
            if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->search) {
            $query->where('label', 'like', '%' . $request->search . '%');
        }
        $records = $query->orderBy('sort_order', 'asc')->paginate($perPage);
        // Return partial HTML for AJAX
        $html = view('admin.categoriesformfield.table', compact('records'))->render();
        return response()->json(['html' => $html]);
    }


    public function create()
    {
        $categories = Category::with('translations')
            ->where('is_active', 1) 
            ->get();
        return view('admin.categoriesformfield.create', compact('categories'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255|unique:categoryformfields,name',
            'type' => 'required|string',

            // Multilingual required fields
            'trans.th.label' => 'required|string',
            'trans.en.label' => 'required|string',
            'trans.th.place_holder' => 'required|string',
            'trans.en.place_holder' => 'required|string',

            // Options (JSON text)
            'trans.th.options' => 'nullable|string',
            'trans.en.options' => 'nullable|string',

            // Images (one time only)
            'option_images.*' => 'nullable|image|mimes:jpg,jpeg,png,webp',
        ]);

        DB::beginTransaction();
        try {

            // Compute sort order
            $lastOrder = CategoryFormField::max('sort_order');
            $newSortOrder = $lastOrder ? $lastOrder + 1 : 1;

            // English fallback
            $englishLabel = $request->trans['en']['label'] ?? $request->trans['th']['label'];
            $englishPlaceholder = $request->trans['en']['place_holder'] ?? $request->trans['th']['place_holder'];

            // STEP 1: HANDLE OPTIONS

            $optionsPerLang = [];
            $primaryOptions = [];

            if (in_array($request->type, ['checkbox', 'radio', 'select'])) {

                // Decode options for each language
                foreach ($request->trans as $lang => $t) {
                    if (!empty($t['options'])) {
                        $decoded = json_decode($t['options'], true);

                        if (!is_array($decoded)) {
                            return back()->withErrors(["trans.$lang.options" => "Invalid JSON in $lang options."]);
                        }

                        $optionsPerLang[$lang] = json_encode($decoded);

                        // Use English as the master option list
                        if ($lang === 'en') {
                            $primaryOptions = $decoded;
                        }
                    }
                }

                // Validate image count matches number of English options
                $uploadedImages = $request->file('option_images') ?? [];

                if (count($uploadedImages) !== count($primaryOptions)) {
                    return back()->withErrors([
                        'option_images' => "Upload exactly " . count($primaryOptions) . " images to match English options."
                    ]);
                }

            
                 //STEP 2: UPLOAD IMAGES (only once)
                $imagePaths = [];
                $folder = 'admin/option_images/';

                if ($uploadedImages) {
                    if (!file_exists(public_path($folder))) {
                        mkdir(public_path($folder), 0777, true);
                    }

                    foreach ($uploadedImages as $img) {
                        $fileName = time() . '_' . uniqid() . '_' . str_replace(' ', '_', $img->getClientOriginalName());
                        $img->move(public_path($folder), $fileName);
                        $imagePaths[] = $folder . $fileName;
                    }
                }

            } else {
                // Non-option fields
                $imagePaths = null;
            }

            /**
             * ----------------------------------------------------------
             * STEP 3: Create main field record
             * ----------------------------------------------------------
             */

            $field = CategoryFormField::create([
                'category_id' => $request->category_id,
                'label' => $englishLabel,
                'place_holder' => $englishPlaceholder,
                'name' => $request->name,
                'type' => $request->type,

                // JSON encode English options (master copy)
                'options' => !empty($primaryOptions) ? json_encode($primaryOptions) : null,

                // images uploaded once
                // 'images' => $imagePaths ? json_encode($imagePaths) : null,
                'images' => $imagePaths ?: null,


                'is_required' => $request->has('is_required'),
                'sort_order' => $newSortOrder,
            ]);

            /**
             * ----------------------------------------------------------
             * STEP 4: Create translations (no images here)
             * ----------------------------------------------------------
             */

            foreach ($request->trans as $lang => $data) {
                CategoryFieldFormTranslation::create([
                    'categoryformfield_id' => $field->id,
                    'lang_code' => $lang,
                    'label' => $data['label'],
                    'place_holder' => $data['place_holder'],
                    'options' => $data['options'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.categoryformfield.index')
                ->with('success', 'Field created successfully.');

        } catch (\Throwable $th) {

            DB::rollBack();
            \Log::alert("Error in Store: " . $th->getMessage());

            return redirect()
                ->route('admin.categoryformfield.index')
                ->with('danger', 'Something went wrong');
        }
    }


    public function changeStatus($id)
    {
        $record = Categoryformfield::find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Category not found.'
            ]);
        }

        $record->is_required = $record->is_required == 1 ? 0 : 1;
        $record->save();

        $status = $record->is_required ? 'Yes' : 'No';

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => "Category Form Field Status changed to {$status} successfully!"
        ]);
    }



    public function edit($id)
    {
        $record = Categoryformfield::with('translations')->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'label' => $item->label,
                'place_holder' => $item->place_holder,
                'options' => $item->options ? json_encode(json_decode($item->options, true), JSON_UNESCAPED_UNICODE) : '',
                'images' => $item->images ? json_encode(json_decode($item->images, true), JSON_UNESCAPED_UNICODE) : '',
                ]];
        });
        $categories = Category::with('translations')->get();
        return view('admin.categoriesformfield.edit', compact('record', 'translations','categories'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'sort_order' => 'nullable|integer',
            'is_required' => 'nullable|boolean',

            // English
            'trans.en.label' => 'required|string|max:255',
            'trans.en.place_holder' => 'required|string|max:255',
            'trans.en.options' => 'nullable|string',

            // Thai
            'trans.th.label' => 'required|string|max:255',
            'trans.th.place_holder' => 'required|string|max:255',
            'trans.th.options' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {

            $field = CategoryFormField::findOrFail($id);

            // ---------------------- PRIMARY OPTIONS ----------------------
            $primaryOptions = [];

            if (in_array($request->type, ['select', 'checkbox', 'radio'])) {

                $raw = $request->trans['en']['options'] ?? '';

                if (is_string($raw)) {

                    // 1. Try decode JSON
                    $decoded = json_decode($raw, true);

                    if (is_array($decoded)) {
                        $primaryOptions = $decoded;
                    } else {
                        // 2. Convert CSV → array
                        $primaryOptions = array_filter(
                            array_map('trim', explode(',', $raw))
                        );
                    }
                }

                if (!is_array($primaryOptions)) {
                    $primaryOptions = [];
                }
            }

            // ---------------------- LOAD OLD IMAGES ----------------------
            $images = $field->images;

            if (is_string($images)) {
                $images = json_decode($images, true);
            }

            $images = $images ?? [];


            // ---------------------- REMOVE IMAGES ----------------------
            foreach ($request->remove_images ?? [] as $removeImg) {
                $key = array_search($removeImg, $images);

                if ($key !== false) {
                    unset($images[$key]);
                    @unlink(public_path($removeImg));
                }
            }

            $images = array_values($images);

            // ---------------------- UPLOAD NEW IMAGES ----------------------
            if ($request->hasFile('option_images')) {

                $folder = 'admin/option_images/';

                if (!file_exists(public_path($folder))) {
                    mkdir(public_path($folder), 0777, true);
                }

                foreach ($request->file('option_images') as $img) {
                    $name = time() . '_' . uniqid() . '_' . str_replace(' ', '_', $img->getClientOriginalName());
                    $img->move(public_path($folder), $name);
                    $images[] = $folder . $name;
                }
            }

            // ---------------------- UPDATE MAIN RECORD ----------------------
            $field->update([
                'label' => $request->trans['en']['label'],
                'place_holder' => $request->trans['en']['place_holder'],
                'name' => $request->name,
                'type' => $request->type,
                'options' => json_encode($primaryOptions),
                'images' => json_encode($images),
            ]);


            // ---------------------- UPDATE TRANSLATIONS ----------------------
            foreach ($request->trans as $lang => $translation) {

                $options = $translation['options'] ?? '';

                if (is_string($options)) {

                    // Try decode JSON
                    $decoded = json_decode($options, true);

                    if (is_array($decoded)) {
                        $options = $decoded;
                    } else {
                        // Convert CSV → array
                        $options = array_filter(
                            array_map('trim', explode(',', $options))
                        );
                    }
                }

                if (!is_array($options)) {
                    $options = [];
                }

                CategoryFieldFormTranslation::updateOrCreate(
                    [
                        'categoryformfield_id' => $field->id,
                        'lang_code' => $lang,
                    ],
                    [
                        'label' => $translation['label'] ?? null,
                        'place_holder' => $translation['place_holder'] ?? null,
                        'options' => json_encode($options),
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.categoryformfield.index')
                ->with('success', 'Field updated successfully.');

        } catch (\Throwable $th) {

            DB::rollBack();
            \Log::error("CategoryFormField Update Error: " . $th->getMessage());

            return back()->with('danger', 'Something went wrong.');
        }
    }



    public function delete($id)
    {
        $field = CategoryFormField::findOrFail($id);

        // Check if there are images to delete
        if (!empty($field->images)) {
            // Decode the images (assuming they are stored as JSON in the database)
            $images = json_decode($field->images, true);

            foreach ($images as $image) {
                $imagePath = public_path($image);  // Get the full path to the image
                if (file_exists($imagePath)) {
                    unlink($imagePath);  
                }
            }
        }
        $field->delete();
        return redirect()->route('admin.categoryformfield.index')->with('success', 'Category Form Fields Removed');
    }


    public function view($id)
    {
        $record = Categoryformfield::with(['translations', 'category.translations'])->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'label' => $item->label,
                'place_holder' => $item->place_holder,
                'options' => $item->options ? json_encode(json_decode($item->options, true), JSON_UNESCAPED_UNICODE) : '',
                ]];
        });
        $categories = Category::with('translations')->get();
        return view('admin.categoriesformfield.view', compact('record', 'translations','categories'));
    }

    public function reorder(Request $request)
    {
        // dd($request);
        foreach ($request->order as $item) {
            CategoryFormField::where('id', $item['id'])
                ->update(['sort_order' => $item['position']]);
        }

        return response()->json(['message' => 'Order updated successfully.']);
    }



}
