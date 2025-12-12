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
        $isAjax = $request->method;

        $insurances = Insurance::with('translation')->get();
        // Choose first Insurance as default
        $defaultInsuranceID = $insurances->first()->id ?? null;
        $insuranceID = $request->insurance_id ?? $defaultInsuranceID;
        $records = InsuranceClaim::with(['translations', 'translation'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('translations', function ($q) use ($search) {
                    $q->where('title', 'like', '%' . $search . '%');
                });
            })
            ->when($insuranceID, function ($query) use ($insuranceID) {
                $query->where('insurance_id', $insuranceID);
            })
            ->sortable(['sort_order' => 'asc'])
            ->paginate($perPage);
        // dd($records);


        // always choosen first as default
        $request->merge([
            'insurance_id' => $insuranceID 
        ]);



        if (!empty($isAjax)) {
            $html = view('admin.claiminsurance.table', compact('records'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.claiminsurance.index', compact('records','insurances','insuranceID'));
        }

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
                    'description' =>strip_tags($data['description']),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('admin.claiminsurance.index',['insurance_id'=> $request->insurance_id])
                ->with('success', 'FAQ Insurance Claimed Added successfully.');

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
        $record = InsuranceClaim::find($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'FAQ Insurance Claim not found.'
            ]);
        }

        $record->is_published = $record->is_published == 1 ? 0 : 1;
        $record->save();

        $status = $record->is_published ? 'Yes' : 'No';

        return response()->json([
            'success' => true,
            'status' => $status,
            'message' => "Status changed to {$status} successfully!"
        ]);
    }



    public function edit($id)
    {
        $record = InsuranceClaim::with('translations')->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                ]];
        });
        $insurances = Insurance::with('translations')->get();
        return view('admin.claiminsurance.edit', compact('record', 'translations','insurances'));
    }


    public function update(Request $request, $id)
    {
        $request->validate([
            'is_published' => 'nullable|boolean',

            // English
            'trans.en.title' => 'required|string|max:255',
            'trans.en.description' => 'required|string|max:255',

            // Thai
            'trans.th.title' => 'required|string|max:255',
            'trans.th.description' => 'required|string|max:255',
        ]);

        DB::beginTransaction();
        try {

            $field = InsuranceClaim::findOrFail($id);

       

            // ---------------------- UPDATE MAIN RECORD ----------------------
            $field->update([
                'title' => $request->trans['en']['title'],
            ]);


            // ---------------------- UPDATE TRANSLATIONS ----------------------
            foreach ($request->trans as $lang => $translation) {

                InsuranceClaimTranslation::updateOrCreate(
                    [
                        'insurance_claim_id' => $field->id,
                        'lang_code' => $lang,
                    ],
                    [
                        'title' => $translation['title'] ?? null,
                        'description' => strip_tags($translation['description'] ?? null),
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.claiminsurance.index',['insurance_id'=> $field->insurance_id])
                ->with('success', 'FAQ Insurance Claim Field updated successfully.');

        } catch (\Throwable $th) {

            DB::rollBack();
            \Log::error("InsuranceClaimController Update Error: " . $th->getMessage());

            return back()->with('danger', 'Something went wrong.');
        }
    }

    public function delete($id)
    {
        $field = InsuranceClaim::findOrFail($id);

        $field->delete();
        return redirect()->route('admin.claiminsurance.index')->with('success', 'FAQ Insurance Claim Fields Removed Successfully');
    }



    public function view($id)
    {
        $record = InsuranceClaim::with(['translations', 'insurance.translations'])->findOrFail($id);
        // dd($record);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                ]];
        });
        $insurances = Insurance::with('translations')->get();
        return view('admin.claiminsurance.view', compact('record', 'translations','insurances'));
    }

    public function reorder(Request $request)
    {
        // dd($request);
        foreach ($request->order as $item) {
            InsuranceClaim::where('id', $item['id'])
                ->update(['sort_order' => $item['position']]);
        }

        return response()->json(['message' => 'Order updated successfully.']);
    }


}
