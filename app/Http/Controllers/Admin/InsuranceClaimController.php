<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use App\Models\InsuranceClaim;
use App\Models\InsuranceClaimTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class InsuranceClaimController extends Controller
{
    public function index(Request $request)
    {
        $current_page = $request->page ?? 1;
        $search = $request->search ?? "";
        $perPage = $request->perPage ?? 50;
        $isAjax = $request->ajax();

        // Start query builder
        $records = InsuranceClaim::with('translations');

        // Apply search
        if ($search) {
            $records->whereHas('translations', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        // Sorting + pagination
        $records = $records->sortable(['sort_order' => 'asc'])->paginate($perPage);

        // If you need all for dropdown
        $insuranceclaims = InsuranceClaim::with('translations')->get();

        if ($isAjax) {
            $html = view('admin.claiminsurance.table', compact('records'))->render();
            return response()->json(['html' => $html]);
        }

        return view('admin.claiminsurance.index', compact('records','insuranceclaims'));
    }


    public function filter(Request $request)
    {
        $perPage = $request->perPage ?? 50;

        $query = InsuranceClaim::with('insurance.translations');
        
            if ($request->insurance_id) {
            $query->where('insurance_id', $request->insurance_id);
        }

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $records = $query->orderBy('sort_order', 'asc')->paginate($perPage);
        // Return partial HTML for AJAX
        $html = view('admin.claiminsurance.table', compact('records'))->render();
        return response()->json(['html' => $html]);
    }


    public function create()
    {
        $insurances = Insurance::with('translation')
            ->where('is_published', 1) 
            ->get();
        return view('admin.claiminsurance.create', compact('insurances'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'insurance_id' => 'required|exists:insurances,id',

            // Multilingual required fields
            'trans.th.title' => 'required|string',
            'trans.en.title' => 'required|string',
            'trans.th.description' => 'required|string',
            'trans.en.description' => 'required|string',
        ]);

        DB::beginTransaction();
        try {

            // Compute sort order
            $lastOrder = InsuranceClaim::max('sort_order');
            $newSortOrder = $lastOrder ? $lastOrder + 1 : 1;

            // English fallback
            $englishTitle = $request->trans['en']['title'] ?? $request->trans['th']['title'];

// dd($request->insurance_id);
            $field = InsuranceClaim::create([
                'insurance_id' => $request->insurance_id,
                'title' => $englishTitle,
                'sort_order' => $newSortOrder,
            ]);

            foreach ($request->trans as $lang => $data) {
                InsuranceClaimTranslation::create([
                    'insurance_claim_id' => $field->id,
                    'lang_code' => $lang,
                    'title' => $data['title'],
                    'description' => $data['description'],
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.claiminsurance.index')
                ->with('success', 'Insurance Claimed successfully.');

        } catch (\Throwable $th) {

            DB::rollBack();
            \Log::alert("Error in Store: " . $th->getMessage());

            return redirect()
                ->route('admin.claiminsurance.index')
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
            'message' => "Status changed to {$status} successfully!"
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
                'options' => $primaryOptions,
                'images' => $images,

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


}
