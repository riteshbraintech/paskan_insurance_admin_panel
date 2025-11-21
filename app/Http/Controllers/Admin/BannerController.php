<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $current_page = $request->page ?? 1;
        $search = $request->search ?? "";
        $perPage = $request->perPage ?? 10;
        $isAjax = $request->method;

        $records = Banner::with(['translation', 'translations']);

        if($search){
            $records = $records->whereHas('translations', function($q) use($search){
                $q->where('title', 'like', '%'.$search.'%');
            }); 
        }

        $records = $records->sortable('id','desc')->paginate($perPage);
        // dd($records);

        if (!empty($isAjax)) {
            $html = view('admin.banner.table', compact('records'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.banner.index', compact('records'));
        }

    }

    public function create()
    {
        return view('admin.banner.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            // --- English ---
            'trans.en.title' => 'required|string|max:255',
            'trans.en.sub_title' => 'required|string|max:255',
            'trans.en.description' => 'required|string',

            // --- Thai ---
            'trans.th.title' => 'required|string|max:255',
            'trans.th.sub_title' => 'required|string|max:255',
            'trans.th.description' => 'required|string',

            // --- Optional ---
            'is_active' => 'nullable|boolean',
            // --- Image validation ---
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            // Custom error messages — English
            'trans.en.title.required' => 'The English title is required.',
            'trans.en.title.max' => 'The English title must not exceed 255 characters.',
            'trans.en.description.required' => 'The English description is required.',

            // Custom error messages — Thai
            'trans.th.title.required' => 'The Thai title is required.',
            'trans.th.title.max' => 'The Thai title must not exceed 255 characters.',
            'trans.th.description.required' => 'The Thai description is required.',

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
                $uploadPath = public_path('admin/banners/img'); 
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }
                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move($uploadPath, $imageName);
            }

            // Create main category
            $bannerInfo = Banner::create([
                'title' => $englishTitle,
                'is_active' => $request->is_active ?? 1,
                'image' => $imageName,
            ]);

            // save translations
            foreach ($request->trans as $langCode => $trans) {
                $bannerInfo->translations()->create([
                    'lang_code' => $langCode,
                    'title' => $trans['title'],
                    'sub_title'=> $trans['sub_title'],
                    'description' => strip_tags($trans['description']),
                ]);
            }

            DB::commit();

            return redirect()->route('admin.banner.index')->with('success', "Banner added successfully");
        } catch (\Throwable $th) {
            \Log::alert("BannerController - Store Function : " . $th->getMessage());
            DB::rollBack();
            return redirect()->route('admin.banner.index')->with('danger', "Something went wrong");
        }
    }

    public function changeStatus($id)
    {
        $record = Banner::findOrFail($id);

        $record->is_active = $record->is_active == 1 ? 0 : 1;
        $record->save();
        $status = $record->is_active == 1 ? 'active' : 'inactive';
        return response()->json([
            'status' => $status,
            'success' => true,
            'message' => "Banner status changed successfully.",
        ]);
    }

    public function edit($id)
    {
        $record = Banner::with('translations')->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'sub_title' => $item->sub_title,
                'description' => $item->description,
                ]];
        });
        // dd($record);
        return view('admin.banner.edit', compact('record', 'translations'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            // --- English ---
            'trans.en.title' => 'required|string|max:255',
            'trans.en.sub_title' => 'required|string|max:255',
            'trans.en.description' => 'required|string',

            // --- Thai ---
            'trans.th.title' => 'required|string|max:255',
            'trans.th.sub_title' => 'required|string|max:255',
            'trans.th.description' => 'required|string',
            // Optional field
            'is_active' => 'nullable|boolean',
        ], [
            'trans.en.title.required' => 'The English title is required.',
            'trans.en.title.max' => 'The English title must not exceed 255 characters.',
            'trans.en.description.required' => 'The English description is required.',
            
            'trans.th.title.required' => 'The Thai title is required.',
            'trans.th.title.max' => 'The Thai title must not exceed 255 characters.',
            'trans.th.description.required' => 'The Thai description is required.',
        ]);

        DB::beginTransaction();

        try {
            $banner = Banner::findOrFail($id);

            // ✅ Update main category title & slug
            $englishTitle = $validatedData['trans']['en']['title'] ?? null;

            $imageName = $banner->image;
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $uploadPath = public_path('admin/banners/img'); 

                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0777, true);
                }

                // delete old image if exists
                if ($banner->image && file_exists($uploadPath . '/' . $banner->image)) {
                    unlink($uploadPath . '/' . $banner->image);
                }

                $imageName = time() . '.' . $image->getClientOriginalExtension();
                $image->move($uploadPath, $imageName);
            }

            if ($englishTitle) {
                $banner->update([
                    'title' => $englishTitle,
                    // 'is_active' => $request->is_active ?? 0,
                    'image' => $imageName, 
                ]);
            }

            // ✅ Update or create translations
            foreach ($validatedData['trans'] as $langCode => $translationData) {
                $banner->translations()->updateOrCreate(
                    ['lang_code' => $langCode],
                    [
                        'title' => $translationData['title'],
                        'sub_title'=>$translationData['sub_title'],
                        'description' => $translationData['description'] ?? '',
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.banner.index')
                ->with('success', 'Banner updated successfully.');

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
        $banner = Banner::findOrFail($id);

        // Delete image if it exists
        if (!empty($banner->image)) {
            $imagePath = public_path('/public/admin/banners/img/' . $banner->image);
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        // Delete record
        $banner->delete();

        return redirect()->route('admin.banner.index')->with('success', 'Banner deleted successfully.');
    }

    public function view($id){
        $record = Banner::with('translations')->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'sub_title'=>$item->sub_title,
                'description' => $item->description,
                ]];
        });
        return view('admin.banner.view', compact('record', 'translations'));
    }

}
