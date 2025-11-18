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
            'type' => 'required|array|min:1',
            'trans.th.label' => 'required|string',
            'trans.en.label' => 'required|string',
            'trans.th.place_holder' => 'required|string',
            'trans.en.place_holder' => 'required|string',
            'trans.th.options' => 'nullable|string',
            'trans.en.options' => 'nullable|string',
        ], [
            'category_id.required' => 'Please select a category.',
            'category_id.exists' => 'Selected category does not exist.',
            'name.required' => 'The field name is required.',
            'name.unique' => 'This field name is already taken.',
            'type.required' => 'Please select a field type.',
            'trans.th.label.required' => 'Label is required in Thai language.',
            'trans.en.label.required' => 'Label is required in English language.',
            'trans.th.place_holder.required' => 'place_holder is required in Thai language.',
            'trans.en.place_holder.required' => 'place_holder is required in English language.',
            'trans.th.options.string' => 'Options are required in Thai language.',
            'trans.en.options.string' => 'Options are required in English language.',
        ]);

        DB::beginTransaction();
        try {
            // Get last sort order
            $lastOrder = Categoryformfield::max('sort_order');
            $newSortOrder = $lastOrder ? $lastOrder + 1 : 1;

            $englishLabel = $request->trans['en']['label'] ?? ($request->trans['th']['label'] ?? null);
            if (is_null($englishLabel)) {
                return redirect()->back()->withInput()->with('danger', "English or Thai label is required.");
            }

            $englishPlaceholder = $request->trans['en']['place_holder'] ?? ($request->trans['th']['place_holder'] ?? null);
            if (is_null($englishPlaceholder)) {
                return redirect()->back()->withInput()->with('danger', "English or Thai Place Holder is required.");
            }

            

            $englishOptionsRaw = $request->trans['en']['options'] ?? ($request->trans['th']['options'] ?? '[]');

            // Ensure valid JSON (fallback to empty array if not)
            $decodedOptions = json_decode($englishOptionsRaw, true);
            $englishOptions = $decodedOptions !== null ? json_encode($decodedOptions) : json_encode([$englishOptionsRaw]);

            // Create main field record
            $field = Categoryformfield::create([
                'category_id' => $request->category_id,
                'label' => $englishLabel,
                'place_holder' => $englishPlaceholder,
                'options' => $englishOptions, 
                'name' => $request->name,
                'type' => json_encode($request->type), 
                'is_required' => $request->has('is_required'),
                'sort_order' => $newSortOrder,
            ]);

            // Create translations
            foreach ($request->trans as $langCode => $translation) {
                $optionsJson = json_decode($translation['options'] ?? '', true);
                CategoryFieldFormTranslation::create([
                    'categoryformfield_id' => $field->id,
                    'lang_code' => $langCode,
                    'label' => $translation['label'] ?? '',
                    'place_holder' => $translation['place_holder'] ?? '',
                    'options' => $translation['options'] ? json_encode(json_decode($translation['options'], true) ?? [$translation['options']]) : json_encode([]),
                ]);
            }

            DB::commit();
            return redirect()->route('admin.categoryformfield.index')->with('success', 'Field created successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            \Log::alert("CategoryFormFieldController - Store Function : " . $th->getMessage());
            return redirect()->route('admin.categoryformfield.index')->with('danger', "Something went wrong");
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
                ]];
        });
        $categories = Category::with('translations')->get();
        return view('admin.categoriesformfield.edit', compact('record', 'translations','categories'));
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|array|min:1',
            'sort_order' => 'nullable|integer',
            'is_required' => 'nullable|boolean',

            // --- English ---
            'trans.en.label' => 'required|string|max:255',
            'trans.en.place_holder' => 'required|string|max:255',
            'trans.en.options' => 'nullable|string',

            // --- Thai ---
            'trans.th.label' => 'required|string|max:255',
            'trans.th.place_holder' => 'required|string|max:255',
            'trans.th.options' => 'nullable|string',
        ], [
            'name.required' => 'The field name is required.',
            'type.required' => 'The field type is required.',
            'trans.en.label.required' => 'The English label is required.',
            'trans.th.label.required' => 'The Thai label is required.',
            'trans.en.place_holder.required' => 'The English Placeholder is required.',
            'trans.th.place_holder.required' => 'The Thai Placeholder is required.',
        ]);

        DB::beginTransaction();

        try {
            $record = Categoryformfield::findOrFail($id);

            // Pick English (or fallback) for main label/options
            $englishLabel = $validatedData['trans']['en']['label'] ?? ($validatedData['trans']['th']['label'] ?? '');
            $englishPlaceholder = $validatedData['trans']['en']['place_holder'] ?? ($validatedData['trans']['th']['place_holder'] ?? '');
            $englishOptions = $validatedData['trans']['en']['options'] ?? ($validatedData['trans']['th']['options'] ?? '');

            // Update single-language fields (main table)
            $record->update([
                'name' => $validatedData['name'],
                'type' => json_encode($validatedData['type']),
                // 'sort_order' => $validatedData['sort_order'] ?? 0,
                'label' => $englishLabel,
                'place_holder' => $englishPlaceholder,
                'options' => $englishOptions,
            ]);

            // Update or create translations
            foreach ($validatedData['trans'] as $langCode => $translationData) {
            $record->translations()->updateOrCreate(
                ['lang_code' => $langCode],
                [
                    'label' => $translationData['label'],
                    'place_holder' => $translationData['place_holder'],
                    'options' => $translationData['options']
                        ? json_encode(json_decode($translationData['options'], true) ?? [$translationData['options']])
                        : json_encode([]),
                ]
            );
            }

            DB::commit();

            return redirect()->route('admin.categoryformfield.index')->with('success', 'Category form field updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Category Form Field Update Error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('danger', 'Something went wrong while updating the category form field.');
        }
    }



    public function delete($id)
    {
        Categoryformfield::findOrFail($id)->delete();
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
