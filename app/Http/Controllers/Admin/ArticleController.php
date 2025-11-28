<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $current_page = $request->page ?? 1;
        $search = $request->search ?? "";
        $perPage = $request->perPage ?? 10;
        $isAjax = $request->method;

        $records = Article::with(['translation', 'translations']);

        if($search){
            $records = $records->whereHas('translations', function($q) use($search){
                $q->where('title', 'like', '%'.$search.'%');
            }); 
        }

        $records = $records->sortable('id','desc')->paginate($perPage);
        // dd($records);

        if (!empty($isAjax)) {
            $html = view('admin.article.table', compact('records'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.article.index', compact('records'));
        }

    }


    public function create()
    {
        return view('admin.article.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            // --- English ---
            'trans.en.title' => 'required|string|max:255',
            'trans.en.subtitle' => 'required|string|max:255',
            'trans.en.content' => 'required|string',

            // --- Thai ---
            'trans.th.title' => 'required|string|max:255',
            'trans.th.subtitle' => 'required|string|max:255',
            'trans.th.content' => 'required|string',

            // --- Optional ---
            'is_active' => 'nullable|boolean',
            // --- Image validation ---
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            // Custom error messages — English
            'trans.en.title.required' => 'The English title is required.',
            'trans.en.subtitle.required' => 'The English Sub title is required.',
            'trans.en.title.max' => 'The English title must not exceed 255 characters.',
            'trans.en.content.required' => 'The English description is required.',

            // Custom error messages — Thai
            'trans.th.title.required' => 'The Thai title is required.',
            'trans.th.subtitle.required' => 'The Thai Sub title is required.',
            'trans.th.title.max' => 'The Thai title must not exceed 255 characters.',
            'trans.th.content.required' => 'The Thai description is required.',

            // Optional field message
            'is_active.boolean' => 'The active status must be true or false.',
        ]);
        DB::beginTransaction();

        try {
            // pick English or Thai title
            $englishTitle = $request->trans['en']['title'] ?? ($request->trans['th']['title'] ?? "");

            if (is_null($englishTitle)) {
                return redirect()->back()->withInput()->with('danger', "English or Thai title is required");
            }

            $imageName = null;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $uploadPath = public_path('admin/articles/img'); 
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move($uploadPath, $imageName);
            }

            // Create main category
            $articleInfo = Article::create([
                'title' => $englishTitle,
                'is_active' => $request->is_active ?? 1,
                'image' => $imageName,
            ]);

            // save translations
            foreach ($request->trans as $langCode => $trans) {
                $articleInfo->translations()->create([
                    'lang_code' => $langCode,
                    'title' => $trans['title'],
                    'subtitle' =>$trans['subtitle'],
                    'content' => strip_tags($trans['content']),
                    'meta_title' => $trans['meta_title'],
                    'meta_description' => $trans['meta_description'],
                    'meta_keywords' => $trans['meta_keywords'],
                ]);
            }

            DB::commit();

            return redirect()->route('admin.article.index')->with('success', "Article added successfully");
        } catch (\Throwable $th) {
            \Log::alert("ArticleController - Store Function : " . $th->getMessage());
            DB::rollBack();
            return redirect()->route('admin.article.index')->with('danger', "Something went wrong");
        }
    }

    public function changeStatus($id)
    {
        $record = Article::findOrFail($id);

        $record->is_active = $record->is_active == 1 ? 0 : 1;
        $record->save();
        $status = $record->is_active == 1 ? 'active' : 'inactive';
        return response()->json([
            'status' => $status,
            'success' => true,
            'message' => "Article status changed successfully.",
        ]);
    }

    public function edit($id)
    {
        $record = Article::with('translations')->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'subtitle' => $item->subtitle,
                'content' => $item->content,
                'meta_title' => $item->meta_title,
                'meta_keywords' => $item->meta_keywords,
                'meta_description' => $item->meta_description,
                ]];
        });
        // dd($translations);
        return view('admin.article.edit', compact('record', 'translations'));
    }

    public function update(Request $request, $id)
    {
        // dd($request);
        $validatedData = $request->validate([
            // --- English ---
            'trans.en.title' => 'required|string|max:255',
            'trans.en.subtitle' => 'required|string|max:255',
            'trans.en.content' => 'required|string',
            'trans.en.meta_title' => 'required|string',
            'trans.en.meta_description' => 'required|string',
            'trans.en.meta_keywords' => 'required|string',

            // --- Thai ---
            'trans.th.title' => 'required|string|max:255',
            'trans.th.subtitle' => 'required|string|max:255',
            'trans.th.content' => 'required|string',
            'trans.th.meta_title' => 'required|string',
            'trans.th.meta_description' => 'required|string',
            'trans.th.meta_keywords' => 'required|string',
            // Optional field
            'is_active' => 'nullable|boolean',
        ], [
            'trans.en.title.required' => 'The English title is required.',
            'trans.en.subtitle.required' => 'The English sub title is required.',
            'trans.en.title.max' => 'The English title must not exceed 255 characters.',
            'trans.en.content.required' => 'The English Content is required.',
            
            'trans.th.title.required' => 'The Thai title is required.',
            'trans.th.subtitle.required' => 'The Thai sub title is required.',
            'trans.th.title.max' => 'The Thai title must not exceed 255 characters.',
            'trans.th.content.required' => 'The Thai Content is required.',
        ]);

        DB::beginTransaction();

        try {
            $article = Article::findOrFail($id);

            $englishTitle = $validatedData['trans']['en']['title'] ?? null;

            $imageName = $article->image;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $uploadPath = public_path('admin/articles/img'); 

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // delete old image if exists
                if ($article->image && file_exists($uploadPath . '/' . $article->image)) {
                    unlink($uploadPath . '/' . $article->image);
                }

                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move($uploadPath, $imageName);
            }

            if ($englishTitle) {
                $article->update([
                    'title' => $englishTitle,
                    // 'is_active' => $request->is_active ?? 0,
                    'image' => $imageName, 
                ]);
            }

            foreach ($validatedData['trans'] as $langCode => $translationData) {
                $article->translations()->updateOrCreate(
                    ['lang_code' => $langCode],
                    [
                        'title' => $translationData['title'],
                        'subtitle' => $translationData['subtitle'],
                        'content' => strip_tags( $translationData['content'] ?? ''),
                        'meta_title' => $translationData['meta_title'] ?? '',
                        'meta_description' => $translationData['meta_description'] ?? '',
                        'meta_keywords' => $translationData['meta_keywords'] ?? '',
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.article.index')
                ->with('success', 'Article updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Banner Update Error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('danger', 'Something went wrong while updating the category.');
        }
    }

    public function delete($id)
    {
        $article = Article::findOrFail($id);

        // Delete image if it exists
        if (!empty($article->image)) {
            $imagePath = public_path('/public/admin/articles/img/' . $article->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Delete record
        $article->delete();

        return redirect()->route('admin.article.index')->with('success', 'Article deleted successfully.');
    }

    public function view($id){
        $record = Article::with('translations')->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'subtitle' => $item->subtitle,
                'content' => $item->content,
                'meta_title' => $item->meta_title,
                'meta_keywords' => $item->meta_keywords,
                'meta_description' => $item->meta_description,
                ]];
        });
        return view('admin.article.view', compact('record', 'translations'));
    }

}
