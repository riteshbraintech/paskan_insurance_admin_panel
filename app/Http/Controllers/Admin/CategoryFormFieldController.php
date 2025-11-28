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
use App\Http\Requests\Admin\CategoryFormFieldOptionsRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;


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

        // dd($request->all());
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
                'is_filter' => $request->has('is_filtered'),
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
                    'short_description' => $data['short_description'],
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

    public function filterchangeStatus($id)
    {
        $record = Categoryformfield::find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Category Form Field not found.'
            ]);
        }

        $record->is_filtered = $record->is_filtered == 1 ? 0 : 1;
        $record->save();

        $status = $record->is_filtered ? 'Yes' : 'No';

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => "Category Form Field Filter Status changed to {$status} successfully!"
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
                'short_description' => $item -> short_description,
                'options' => $item->options ? json_encode(json_decode($item->options, true), JSON_UNESCAPED_UNICODE) : '',
                'images' => $item->images ? json_encode(json_decode($item->images, true), JSON_UNESCAPED_UNICODE) : '',
                ]];
        });
        $categories = Category::with('translations')->get();
        return view('admin.categoriesformfield.edit', compact('record', 'translations','categories','parentQuestion'));
    }


    public function update(CategoryFormFieldsRequest $request, $id)
    {
        // dd($request->all());
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
                        'place_holder' => $translation['place_holder'] ?? null,
                        'short_description' => $translation['short_description'] ?? null,
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
                'short_description' => $item->short_description,
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

        $mainForm =  CategoryFormField::with(['options.optionIds','parent','options','options.translations','parent.translation', 'translation'])->findOrFail($id);
        // dd($mainForm);
        $record = Categoryformfield::with('translations')->findOrFail($id);
        return view('admin.categoriesformfield.form-options', compact('mainForm', 'record'));
    }

    // edit Options Form
    public function editOptionsForm(Request $request, $id){
        $mainForm =  CategoryFormField::with(['options.optionIds','parent','parent.translation', 'translation'])->findOrFail($id);
        $record = Categoryformfield::with('translations')->findOrFail($id);
        return view('admin.categoriesformfield.form-options-edit-form', compact('mainForm', 'record'));
        
    }

    public function optionstore(CategoryFormFieldOptionsRequest $request)
    {
        // dd($request->all());
        DB::beginTransaction();

        try {
            
            // Compute sort order
            $lastOrder = CategoryFormField::max('sort_order');
            $newSortOrder = $lastOrder ? $lastOrder + 1 : 1;
            // Create option record
            $field = CategoryFormField::find($request->field_id);
            // dd($field,$request->all());
            $option = CategoryFieldFormOptions::create([
                'field_id' => $request->field_id,
                'value'    => $request->value,
                'order'    => $newSortOrder,
            ]);

            if($request->has('parent_option_id') && !empty($request->parent_option_id)){
                $idsss = explode(',',$request->parent_option_id);
                $option->optionIds()->sync($idsss);
                // dd($option->optionIds);
            }

            /**
             * STEP 2: Create translations
             */
            if($request->has('trans') && is_array($request->trans) && count($request->trans) > 0){   
                foreach ($request->trans as $lang => $data) {
                        $imageName = null;

                        if (!empty($data['images'])) {
                            $img = $data['images'];

                            // Define upload path
                            $uploadPath = public_path('admin/form_options/');
                            
                            // Create folder if it doesn't exist
                            if (!file_exists($uploadPath)) {
                                mkdir($uploadPath, 0777, true);
                            }
                            $imageName = time() . '.' . $img->getClientOriginalExtension();

                            $img->move($uploadPath, $imageName);
                        }


                    CategoryFieldFormOptionsTranslation::create([
                        'option_id' => $option->id,
                        'lang_code' => $lang,
                        'label'     => $data['label'],
                        'image' => $imageName,
                    ]);
                }
            }

            $mainForm =  CategoryFormField::with(['options.optionIds','parent','options','options.translations','parent.translation', 'translation'])->findOrFail($request->field_id);
            $html=view('admin.categoriesformfield.form-line-options',['option'=>$option,'mainForm'=>$mainForm])->render();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Option created successfully.',
                'option_id' => $option->id,
                'html'=>$html,
            ]);

        } catch (\Throwable $th) {

            DB::rollBack();
            // dd($th);
            \Log::alert("Error in Store: " . $th->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Option created failed.',
                'html'=>'',
            ]);

        }
    }


    public function optionupdate(CategoryFormFieldOptionsRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            // Find existing option
            $option = CategoryFieldFormOptions::findOrFail($id);

            // Update option basic values
            $option->update([
                'value' => $request->value,
                'parent_option_id' => $request->has('parent_option_id') ? $request->parent_option_id : $option->parent_option_id,
            ]);

            if($request->has('parent_option_id') && !empty($request->parent_option_id)){
                $idsss = explode(',',$request->parent_option_id);
                $option->optionIds()->sync($idsss);
                // dd($option->optionIds);
            }

            /**
             * STEP 2: Update translations
             */
            if ($request->has('trans') && is_array($request->trans)) {

                foreach ($request->trans as $lang => $data) {

                    $translation = CategoryFieldFormOptionsTranslation::where('option_id', $option->id)
                        ->where('lang_code', $lang)
                        ->first();

                    if ($translation) {
                        $translation->label = $data['label'] ?? $translation->label;

                        // Update image if provided
                        if (!empty($data['images'])) {
                            $img = $data['images'];

                            $uploadPath = public_path('admin/form_options/');
                            
                            if (!file_exists($uploadPath)) {
                                mkdir($uploadPath, 0777, true);
                            }

                            $fileName = time() . '.' . $img->getClientOriginalExtension();

                            // Move uploaded file to the folder
                            $img->move($uploadPath, $fileName);

                            // Assign new file name to translation
                            $translation->image = $fileName;
                        }

                        $translation->save();
                    }

                }
            }

            $mainForm =  CategoryFormField::with(['parent','options','options.translations','parent.translation', 'translation'])->findOrFail($request->field_id);
            $html=view('admin.categoriesformfield.form-line-options',['option'=>$option,'mainForm'=>$mainForm])->render();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Option updated successfully.',
                'html' =>$html
            ]);

        } catch (\Throwable $th) {

            DB::rollBack();
            \Log::alert("Error in Update: " . $th->getMessage());

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'html' =>''
            ]);
        }
    }

    // public function deleteOption($id)
    // {
    //     $option = CategoryFieldFormOptions::findOrFail($id);

     
    //     // Delete translations first
    //     CategoryFieldFormOptionsTranslation::where('option_id', $id)->delete();

    //     // Delete main option
    //     $option->delete();

    //     return response()->json([
    //         'success' => true,
    //         'message' => 'Option deleted'
    //     ]);
    // }

    public function deleteOption($id)
    {
        $option = CategoryFieldFormOptions::findOrFail($id);

        // Delete images from translations
        $translations = CategoryFieldFormOptionsTranslation::where('option_id', $id)->get();

        foreach ($translations as $translation) {
            if (!empty($translation->image)) {
                $imagePath = public_path('admin/form_options/' . $translation->image);
                if (File::exists($imagePath)) {
                    File::delete($imagePath);
                }
            }
        }

        // Delete translations
        CategoryFieldFormOptionsTranslation::where('option_id', $id)->delete();

        // Delete main option
        $option->delete();

        return response()->json([
            'success' => true,
            'message' => 'Option deleted'
        ]);
    }



    

    public function optionfilter(Request $request)
    {
        $mainForm = CategoryFormField::find($request->form_id); 
        $query = CategoryFieldFormOptions::with(['children', 'translation', 'parents']);

        // If a specific parent_option_id is passed → filter by that parent
        if ($request->parent_option_id) {
            $query->whereHas('parents', function ($q) use ($request) {
                $q->where('parent_option_id', $request->parent_option_id);
            });
        } 
        // If no parent_option_id → show ONLY child options
        else {
            $query->whereHas('parents');
        }

        $records = $query->paginate(50);

        $html = '';
        foreach ($records as $option) {
            $html .= view('admin.categoriesformfield.form-line-options', compact('option', 'mainForm'))->render();
        }

        return response()->json([
            'html' => $html,
        ]);

    }
    





}
     