<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $current_page = $request->page ?? 1;
        $search = $request->search ?? "";
        $perPage = $request->perPage ?? 10;
        $isAjax = $request->method;

        $records = Category::with(['translation', 'translations']);

        if($search){
            $records = $records->whereHas('translations', function($q) use($search){
                $q->where('title', 'like', '%'.$search.'%');
            }); 
        }

        $records = $records->sortable('id','desc')->paginate($perPage);
        // dd($records);

        if (!empty($isAjax)) {
            $html = view('admin.categories.table', compact('records'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.categories.index', compact('records'));
        }

    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            // --- English ---
            'trans.en.title' => 'required|string|max:255',
            'trans.en.description' => 'required|string',
            'trans.en.meta_title' => 'required|string|max:255',
            'trans.en.meta_description' => 'required|string',
            'trans.en.meta_keywords' => 'required|string|max:255',

            // --- Thai ---
            'trans.th.title' => 'required|string|max:255',
            'trans.th.description' => 'required|string',
            'trans.th.meta_title' => 'required|string|max:255',
            'trans.th.meta_description' => 'required|string',
            'trans.th.meta_keywords' => 'required|string|max:255',

            // --- Optional ---
            'is_active' => 'nullable|boolean',
            // --- Image validation ---
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            // Custom error messages — English
            'trans.en.title.required' => 'The English title is required.',
            'trans.en.title.max' => 'The English title must not exceed 255 characters.',
            'trans.en.description.required' => 'The English description is required.',
            'trans.en.meta_title.required' => 'The English meta title is required.',
            'trans.en.meta_description.required' => 'The English meta description is required.',
            'trans.en.meta_keywords.required' => 'The English meta keywords are required.',

            // Custom error messages — Thai
            'trans.th.title.required' => 'The Thai title is required.',
            'trans.th.title.max' => 'The Thai title must not exceed 255 characters.',
            'trans.th.description.required' => 'The Thai description is required.',
            'trans.th.meta_title.required' => 'The Thai meta title is required.',
            'trans.th.meta_description.required' => 'The Thai meta description is required.',
            'trans.th.meta_keywords.required' => 'The Thai meta keywords are required.',

            // Optional field message
            'is_active.boolean' => 'The active status must be true or false.',
        ]);
        DB::beginTransaction();

        try {
            // pick English or Thai title
            $englishTitle = $request->trans['en']['title'] ?? ($request->trans['th']['title'] ?? "hello");

            if (is_null($englishTitle)) {
                return redirect()->back()->withInput()->with('danger', "English or Thai title is required");
            }

            $slug = Str::slug($englishTitle);

            // Check if slug already exists in categories table
            if (\App\Models\Category::where('slug', $slug)->exists()) {
                return redirect()->back()
                    ->withErrors(['trans.en.title' => 'The slug generated from this English title already exists. Please choose a different title.'])
                    ->withInput();
            }

            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $uploadPath = public_path('admin/categories/img'); 
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move($uploadPath, $imageName);
            }

            // Create main category
            $categoryInfo = Category::create([
                'title' => $englishTitle,
                'slug' => $slug,
                'is_active' => $request->is_active ?? 1,
                'image' => $imageName,
            ]);

            // save translations
            foreach ($request->trans as $langCode => $trans) {
                $categoryInfo->translations()->create([
                    'lang_code' => $langCode,
                    'title' => $trans['title'],
                    'slug' => Str::slug($trans['title']),
                    'description' => $trans['description'],
                    'meta_title' => $trans['meta_title'],
                    'meta_description' => $trans['meta_description'],
                    'meta_keywords' => $trans['meta_keywords'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.categories.index')->with('success', "Category added successfully");
        } catch (\Throwable $th) {
            \Log::alert("CategoryController - Store Function : " . $th->getMessage());
            DB::rollBack();
            return redirect()->route('admin.categories.index')->with('danger', "Something went wrong");
        }
    }


    public function changeStatus($id)
    {
        $record = Category::findOrFail($id);

        $record->is_active = $record->is_active == 1 ? 0 : 1;
        $record->save();
        $status = $record->is_active == 1 ? 'active' : 'inactive';
        return response()->json([
            'status' => $status,
            'success' => true,
            'message' => "Category status changed successfully.",
        ]);
    }

    public function edit($id)
    {
        $record = Category::with('translations')->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'meta_title' => $item->meta_title,
                'meta_keywords' => $item->meta_keywords,
                'meta_description' => $item->meta_description,
                ]];
        });
        // dd($record);
        return view('admin.categories.edit', compact('record', 'translations'));
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            // --- English ---
            'trans.en.title' => 'required|string|max:255',
            'trans.en.description' => 'required|string',
            'trans.en.meta_title' => 'required|string|max:255',
            'trans.en.meta_description' => 'required|string',
            'trans.en.meta_keywords' => 'required|string|max:255',

            // --- Thai ---
            'trans.th.title' => 'required|string|max:255',
            'trans.th.description' => 'required|string',
            'trans.th.meta_title' => 'required|string|max:255',
            'trans.th.meta_description' => 'required|string',
            'trans.th.meta_keywords' => 'required|string|max:255',

            // Optional field
            'is_active' => 'nullable|boolean',
        ], [
            'trans.en.title.required' => 'The English title is required.',
            'trans.en.title.max' => 'The English title must not exceed 255 characters.',
            'trans.en.description.required' => 'The English description is required.',
            'trans.en.meta_title.required' => 'The English meta title is required.',
            'trans.en.meta_description.required' => 'The English meta description is required.',
            'trans.en.meta_keywords.required' => 'The English meta keywords are required.',

            'trans.th.title.required' => 'The Thai title is required.',
            'trans.th.title.max' => 'The Thai title must not exceed 255 characters.',
            'trans.th.description.required' => 'The Thai description is required.',
            'trans.th.meta_title.required' => 'The Thai meta title is required.',
            'trans.th.meta_description.required' => 'The Thai meta description is required.',
            'trans.th.meta_keywords.required' => 'The Thai meta keywords are required.',
        ]);

        DB::beginTransaction();

        try {
            $category = Category::findOrFail($id);

            // ✅ Update main category title & slug
            $englishTitle = $validatedData['trans']['en']['title'] ?? null;

            $imageName = $category->image;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $uploadPath = public_path('admin/categories/img'); 

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // delete old image if exists
                if ($category->image && file_exists($uploadPath . '/' . $category->image)) {
                    unlink($uploadPath . '/' . $category->image);
                }

                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move($uploadPath, $imageName);
            }

            if ($englishTitle) {
                $category->update([
                    'title' => $englishTitle,
                    'slug' => Str::slug($englishTitle),
                    // 'is_active' => $request->is_active ?? 0,
                    'image' => $imageName, 
                ]);
            }

            // ✅ Update or create translations
            foreach ($validatedData['trans'] as $langCode => $translationData) {
                $category->translations()->updateOrCreate(
                    ['lang_code' => $langCode],
                    [
                        'title' => $translationData['title'],
                        'slug' => Str::slug($translationData['title']),
                        'description' => $translationData['description'] ?? '',
                        'meta_title' => $translationData['meta_title'] ?? '',
                        'meta_description' => $translationData['meta_description'] ?? '',
                        'meta_keywords' => $translationData['meta_keywords'] ?? '',
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.categories.index')
                ->with('success', 'Category updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Category Update Error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('danger', 'Something went wrong while updating the category.');
        }
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);

        // Delete image if it exists
        if (!empty($category->image)) {
            $imagePath = public_path('/public/admin/categories/img/' . $category->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Delete record
        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Category deleted successfully.');
    }


    public function view($id){
        $record = Category::with('translations')->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'meta_title' => $item->meta_title,
                'meta_keywords' => $item->meta_keywords,
                'meta_description' => $item->meta_description,
                ]];
        });
        return view('admin.categories.view', compact('record', 'translations'));
    }

}
