<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Insurance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InsuranceController extends Controller
{
    public function index(Request $request)
    {
        $current_page = $request->page ?? 1;
        $search = $request->search ?? "";
        $perPage = $request->perPage ?? 10;
        $isAjax = $request->method;

        $records = Insurance::with(['translation', 'translations']);

        if($search){
            $records = $records->whereHas('translations', function($q) use($search){
                $q->where('title', 'like', '%'.$search.'%');
            }); 
        }

        $records = $records->orderby('id','asc')->paginate($perPage);
        // dd($records);

        if (!empty($isAjax)) {
            $html = view('admin.insurance.table', compact('records'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.insurance.index', compact('records'));
        }

    }

    public function create()
    {
        return view('admin.insurance.create');
    }

    public function store(Request $request)
    {
        // dd($request);    
        $request->validate([
            // --- English ---
            'trans.en.title' => 'required|string|max:255',
            'trans.en.description' => 'required|string',

            // --- Thai ---
            'trans.th.title' => 'required|string|max:255',
            'trans.th.description' => 'required|string',

            // --- Optional ---
            'is_active' => 'nullable|boolean',
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
            // Compute sort order
            $lastOrder = Insurance::max('sort_order');
            $newSortOrder = $lastOrder ? $lastOrder + 1 : 1;

            // pick English or Thai title
            $englishTitle = $request->trans['en']['title'] ?? ($request->trans['th']['title'] ?? "");

            if (is_null($englishTitle)) {
                return redirect()->back()->withInput()->with('danger', "English or Thai title is required");
            }

            // $slug = Str::slug($englishTitle);

            // // Check if slug already exists in categories table
            // if (\App\Models\Insurance::where('slug', $slug)->exists()) {
            //     return redirect()->back()
            //         ->withErrors(['trans.en.title' => 'The slug generated from this English title already exists. Please choose a different title.'])
            //         ->withInput();
            // }

            // Create main category
            $insuranceInfo = Insurance::create([
                'title' => $englishTitle,
                'slug' => $englishTitle,
                'is_published' => $request->is_published ?? 1,
                'sort_order' => $newSortOrder,
            ]);

            // save translations
            foreach ($request->trans as $langCode => $trans) {
                $insuranceInfo->translations()->create([
                    'lang_code' => $langCode,
                    'title' => $trans['title'],
                    'description' => strip_tags($trans['description']),

                ]);
            }

            DB::commit();

            return redirect()->route('admin.insurances.index')->with('success', "Insurance added successfully");
        } catch (\Throwable $th) {
            \Log::alert("InsuranceController - Store Function : " . $th->getMessage());
            DB::rollBack();
            return redirect()->route('admin.insurances.index')->with('danger', "Something went wrong");
        }
    }

    public function changeStatus($id)
    {
        $record = Insurance::findOrFail($id);

        $record->is_published = $record->is_published == 1 ? 0 : 1;
        $record->save();
        $status = $record->is_published == 1 ? 'published' : 'unpublished';
        return response()->json([
            'status' => $status,
            'success' => true,
            'message' => "Insurance status changed successfully.",
        ]);
    }

    public function edit($id)
    {
        $record = Insurance::with('translations')->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                ]];
        });
        // dd($record);
        return view('admin.insurance.edit', compact('record', 'translations'));
    }


    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            // --- English ---
            'trans.en.title' => 'required|string|max:255',
            'trans.en.description' => 'required|string',

            // --- Thai ---
            'trans.th.title' => 'required|string|max:255',
            'trans.th.description' => 'required|string',
            
            // Optional field
            'is_published' => 'nullable|boolean',
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
            $insurance = Insurance::findOrFail($id);

            // ✅ Update main category title & slug
            $englishTitle = $validatedData['trans']['en']['title'] ?? null;

            
            if ($englishTitle) {
                $insurance->update([
                    'title' => $englishTitle,
                    'slug' => Str::slug($englishTitle), 
                ]);
            }

            // ✅ Update or create translations
            foreach ($validatedData['trans'] as $langCode => $translationData) {
                $insurance->translations()->updateOrCreate(
                    ['lang_code' => $langCode],
                    [
                        'title' => $translationData['title'],
                        'description' => $translationData['description'] ?? '',
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.insurances.index')
                ->with('success', 'Insurance updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            \Log::error('Insurance Update Error: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('danger', 'Something went wrong while updating the category.');
        }
    }

    public function delete($id)
    {
        $category = Insurance::findOrFail($id);

        // Delete record
        $category->delete();

        return redirect()->route('admin.insurances.index')->with('success', 'Insurance deleted successfully.');
    }


    public function view($id){
        $record = Insurance::with('translations')->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                ]];
        });
        return view('admin.insurance.view', compact('record', 'translations'));
    }

    public function reorder(Request $request)
    {
        // dd($request);
        foreach ($request->order as $item) {
            Insurance::where('id', $item['id'])
                ->update(['sort_order' => $item['position']]);
        }

        return response()->json(['message' => 'Order updated successfully.']);
    }

}
