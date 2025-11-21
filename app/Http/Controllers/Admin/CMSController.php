<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Client;
use App\Models\Lead;
use App\Models\CMSPage;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\CMSPageRequest;
use App\Models\Languages;
use Illuminate\Support\Facades\DB;

class CMSController extends Controller
{
    public function index(Request $request)
    {
        $current_page = $request->page ?? 1;
        $search = $request->search ?? "";
        $perPage = $request->perPage ?? 50;
        $isAjax = $request->method;

        $records = CMSPage::with('translations');

        if($search){
            $records = $records->whereHas('translations', function($q) use($search){
                $q->where('title', 'like', '%'.$search.'%');
            }); 
        }

        $records = $records->sortable('id','desc')->paginate($perPage);
        // dd($records);

        if (!empty($isAjax)) {
            $html = view('admin.cmspage.table', compact('records'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.cmspage.index', compact('records'));
        }

    }

    public function create()
    {
        return view('admin.cmspage.create');
    }

    public function store(CMSPageRequest $request)
    {
        DB::beginTransaction();

        try {

            // pick english title and make as slug an ttitle for cms_page table
            $englishTitle = $request->trans['en']['title'] ?? ($request->trans['th']['title'] ?? null);
            if(is_null($englishTitle)){
                return redirect()->back()->withInput()->with('danger',"English or Thai title is required");
            }
            $pageInfo  = CMSPage::create([
                'page_title' => $englishTitle,
                'page_slug' => \Str::slug($englishTitle),
                'is_published' => $request->is_published ?? 0,
            ]);

            // loop over trans and save each translation
            foreach ($request->trans as $langCode => $trans) {
                $pageInfo->translations()->create([
                    'lang_code' => $langCode,
                    'title' => $trans['title'],
                    'slug' => \Str::slug($trans['title']),
                    'content' => $trans['content'],
                    'meta_title' => $trans['meta_title'],
                    'meta_description' => $trans['meta_description'],
                    'meta_keywords' => $trans['meta_keywords'],
                ]);
            }

            // commit transaction
            DB::commit();

            return redirect()->route('admin.cmspage.index')->with('success',"Pages added successfully");
        } catch (\Throwable $th) {
            \Log::alert(" CMSController - Store Function : ". $th->getMessage());
            DB::rollBack();
            return redirect()->route('admin.cmspage.index')->with('danger',"Something went wrong");
        }
    }

    public function clientDetail(Request $request)
    {
        $detail = CMSPage::where('id',$request->id)->get();  
        if(!blank($detail)){
            return response()->json([
                'status'=>true,
                'detail' => $detail
            ]);
        }
        return response()->json([
            'status'=>false,
        ]);
    }
    
    public function edit($id)
    {
        $record = CMSPage::with('translations')->findOrFail($id);
        
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'content' => $item->content,
                'meta_title' => $item->meta_title,
                'meta_keywords' => $item->meta_keywords,
                'meta_description' => $item->meta_description,
                ]];
        });
        return view('admin.cmspage.edit', compact('record', 'translations'));
    }

    public function show($id)
    {
        $record = CMSPage::with('translations')->findOrFail($id);
        // Convert translations into a key-value pair for easy access
        $translations = $record->translations->mapWithKeys(function ($item) {
            return [$item->lang_code => [
                'id' => $item->id,
                'title' => $item->title,
                'content' => $item->content,
                'meta_title' => $item->meta_title,
                'meta_keywords' => $item->meta_keywords,
                'meta_description' => $item->meta_description,
                ]];
        });
        return view('admin.cmspage.view', compact('record', 'translations'));
    }
    
    public function update(CMSPageRequest $request, $id)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();

            // --- 1. Update the main CMS page ---
            $cmsPage = CMSPage::findOrFail($id);
            // $cmsPage->update([
            //     'title' => $data['title'],
            //     'slug' => $data['slug'],
            //     'is_published' => $data['is_published'],
            // ]);

            // --- 2. Handle translations ---
            foreach ($data['trans'] as $langCode => $translationData) {
                $cmsPage->translations()->updateOrCreate(
                    [
                        'lang_code' => $langCode,
                    ],
                    [
                        'title' => $translationData['title'],
                        'content' => $translationData['content'] ?? '',
                        'meta_title' => $translationData['meta_title'] ?? '',
                        'meta_description' => $translationData['meta_description'] ?? '',
                        'meta_keywords' => $translationData['meta_keywords'] ?? '',
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.cmspage.index')
                ->with('success', 'CMS Page updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the CMS page. ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {

        CMSPage::findOrFail($id)->delete();
        return redirect()->route('admin.cmspage.index')->with('success', 'Client Removed');
    }

    public function merge(Request $request)
    {   
        $idString = $request->ids;
        $ids = strpos($request->ids, ',') ? explode(",",$request->ids) : [$request->ids];
        $clients = CMSPage::select('id','client_name','mobile','email','linkedin','skype','other','location')->whereIn('id', $ids)->orderBy("email", "DESC")->get();
        
        return view('admin.cmspage.merge', compact('clients','idString'));
    }

    public function storeMerge(Request $request)
    {
        // update client
        $client = CMSPage::find($request->updateId);
        $client->fill([
            'client_name' => $request->client_name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'skype' => $request->skype,
            'linkedin' => $request->linkedin,
            'other' => $request->other,
            'location' => $request->location,
        ]);
        $client->save();

        // remove client and update to lead id
        $ids = strpos($request->removeIds, ',') ? explode(",",$request->removeIds) : [$request->removeIds];
        
        // update client ids in lead
        Lead::whereIn('client_id', $ids)->update(["client_id"=>$request->updateId]);
        Bid::whereIn('client_id', $ids)->update(["client_id"=>$request->updateId]);

        // remove merged client 
        if (($key = array_search($request->updateId, $ids)) !== false) {
            unset($ids[$key]);
            CMSPage::whereIn('id', $ids)->delete();
        }
        return redirect()->route('admin.cmspage.list')->with('success',"Client merged successfully");
    }

    public function clientBidsList(Request $request, $id){
        $bids = Bid::with('lead:bid_id,id,lead_id','CMSPage:id,client_name')->select('id','bid_date','created_at','user_name','job_title','job_link','portal','project_type','bid_quote','is_lead_converted','status','client_id')->where('client_id',$id)->get();
        
        return view('admin.cmspage.bids-list',compact('bids'));
    }

    public function changeStatus($id)
    {
        $record = CMSPage::findOrFail($id);

        $record->is_published = $record->is_published == 1 ? 0 : 1;
        $record->save();
        $status = $record->is_published == 1 ? 'yes' : 'no';
        return response()->json(['status' => $status]);
    }



}
