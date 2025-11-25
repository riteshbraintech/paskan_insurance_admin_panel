<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryFieldFormTranslation;
use App\Models\Categoryformfield;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;
use App\Http\Requests\Admin\CategoryFormFieldsRequest;
use App\Models\CategoryFieldFormOptions;
use App\Models\CategoryFieldFormOptionsTranslation;
use App\Models\Option;
use App\Models\OptionTranslation;

class CategoryFormFieldController extends Controller
{
    public function index(Request $request)
    {
        $current_page = $request->page ?? 1;
        $search = $request->search ?? "";
        $perPage = $request->perPage ?? 50;
        $isAjax = $request->method;

        $records = Categoryformfield::with(['translations','translation']);
        if($search){
            $records = $records->whereHas('translations', function($q) use($search){
                $q->where('label', 'like', '%'.$search.'%');
            }); 
        }
        // dd($records);
        $records = $records->sortable('sort_order','asc')->paginate($perPage);

        $categories = Category::with('translation')->get();

        // always choosen first as default
        $categoriesIDs = Category::with('translation')->first()->id ?? 0;
        $request->merge([
            'category_id' => $categoriesIDs 
        ]);



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


    public function create(Request $request)
    {
        $catid = $request->catId;

        $parentQuestion = Categoryformfield::with('translation');
        if($catid)  $parentQuestion = $parentQuestion->where('category_id', $catid);
        $parentQuestion = $parentQuestion->get();

        $categories = Category::with('translation')->where('is_active', 1)->get();
        return view('admin.categoriesformfield.create', compact('categories','parentQuestion'));
    }


    public function store(CategoryFormFieldsRequest $request)
    {

        DB::beginTransaction();
        try {

            // Compute sort order
            $lastOrder = CategoryFormField::max('sort_order');
            $newSortOrder = $lastOrder ? $lastOrder + 1 : 1;

            // English fallback
            $englishLabel = $request->trans['en']['label'] ?? $request->trans['th']['label'];
            $englishPlaceholder = $request->trans['en']['place_holder'] ?? $request->trans['th']['place_holder'];

            $field = CategoryFormField::create([
                'category_id' => $request->category_id,
                'name' => $request->name,
                'type' => $request->type,
                'parent_field_id' => $request->has('parent_field_id') ? $request->parent_field_id : null,
                'is_required' => $request->has('is_required'),
                'sort_order' => $newSortOrder,
            ]);
            // dd($field);

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

        
        $parentQuestion = Categoryformfield::with('translation');
        $parentQuestion = $parentQuestion->where('category_id', $record->category_id ?? 0);
        $parentQuestion = $parentQuestion->get();

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
        return view('admin.categoriesformfield.edit', compact('record', 'translations','categories','parentQuestion'));
    }


    public function update(CategoryFormFieldsRequest $request, $id)
    {

        DB::beginTransaction();
        try {

            $field = CategoryFormField::findOrFail($id);

            // ---------------------- UPDATE MAIN RECORD ----------------------
            $field->update([
                'name' => $request->name,
                'type' => $request->type,
                'parent_field_id' => $request->has('parent_field_id') ? $request->parent_field_id : null,
            ]);

            // ---------------------- UPDATE TRANSLATIONS ----------------------
            foreach ($request->trans as $lang => $translation) {
                CategoryFieldFormTranslation::updateOrCreate(
                    [
                        'categoryformfield_id' => $field->id,
                        'lang_code' => $lang,
                    ],
                    [
                        'label' => $translation['label'] ?? null,
                        'place_holder' => $translation['place_holder'] ?? null
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

        if (!empty($field->images)) {
            foreach ($field->images as $image) {
                $imagePath = public_path($image);
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


    // view form filed value and question
    public function viewOptions(Request $request, $id){

        $mainForm =  CategoryFormField::with(['parent','parent.translation', 'translation'])->findOrFail($id);
        $record = Categoryformfield::with('translations')->findOrFail($id);
        return view('admin.categoriesformfield.form-options', compact('mainForm', 'record'));
    }

    // edit Options Form
    public function editOptionsForm(Request $request, $id){
        $mainForm =  CategoryFormField::with(['parent','parent.translation', 'translation'])->findOrFail($id);
        $record = Categoryformfield::with('translations')->findOrFail($id);
        return view('admin.categoriesformfield.form-options-edit-form', compact('mainForm', 'record'));
        
    }

    public function optionstore(Request $request)
    {
        DB::beginTransaction();

        try {

            // Compute sort order
            $lastOrder = CategoryFormField::max('sort_order');
            $newSortOrder = $lastOrder ? $lastOrder + 1 : 1;

            // Create option record
            $option = CategoryFieldFormOptions::create([
                'field_id' => $request->field_id,
                'value'    => $request->value,
                'order'    => $newSortOrder,
            ]);

            /**
             * STEP 1: MULTIPLE IMAGE UPLOAD
             */
            $uploadedImages = [];

            if ($request->hasFile('image')) {

                foreach ($request->file('image') as $img) {

                    $fileName = time() . '-' . uniqid() . '.' . $img->getClientOriginalExtension();

                    // store in storage/app/public/form_options
                    $img->storeAs('public/form_options', $fileName);

                    $uploadedImages[] = $fileName;
                }
            }

            // Save images in option record (recommended JSON)
            if (!empty($uploadedImages)) {
                $option->update([
                    'images' => json_encode($uploadedImages)
                ]);
            }

            /**
             * STEP 2: Create translations
             */
            foreach ($request->trans as $lang => $data) {

                CategoryFieldFormOptionsTranslation::create([
                    'option_id' => $option->id,
                    'lang_code' => $lang,
                    'label'     => $data['label'],
                ]);
            }


            DB::commit();

            return redirect()
                ->route('admin.categoryformfield.index')
                ->with('success', 'Field option created successfully.');

        } catch (\Throwable $th) {

            DB::rollBack();
            \Log::alert("Error in Store: " . $th->getMessage());

            return redirect()
                ->route('admin.categoryformfield.index')
                ->with('danger', 'Something went wrong');
        }
    }


}
