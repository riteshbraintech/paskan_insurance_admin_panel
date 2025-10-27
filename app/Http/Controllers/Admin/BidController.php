<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Bid;
use App\Models\Client;
use App\Models\Portal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Role;
use App\Models\Technology;

class BidController extends Controller
{
    private $excludeIds = [42, 33, 51];
    public function index(Request $request)
    {
        $staffs_dd = [];
        $role = admin()->user()->role_id;
        $isAjax = $request->method;  // for ajax check
        $current_page = $request->page ?? 1;
        $search = $request->search ?? '';
        $perPage = $request->perPage ?? 50;
        $status = $request->status ?? '';
        $portalFilter = $request->portalFilter ?? '';
        $staffFilter = $request->staffFilter ?? '';
        $managerFilter = $request->managerFilter ?? '';
        $bidsFilter = $request->has('bidsFilter') ? $request->bidsFilter : 0 ;
        
        $date = isset($request->disabledBtn) && $request->disabledBtn == "false" ? "" : now()->startOfMonth()->format('m/d/Y') . ' - ' . now()->endOfMonth()->format('m/d/Y');
        $dateRange = $request->filled('dateRange') ? $request->dateRange : $date ;
       
        $bidsQuery = Bid::query()->with('client','admin')
            ->whereHas('admin',function($q) {
                $q->where('status','active')->whereNotIn('id', $this->excludeIds);
            });

        $staffsQuery = Admin::query()->testfilter()->where(["role_id" => Role::STAFF])->whereNotIn('id', $this->excludeIds);
        $managers = Admin::query()->testfilter()->where(['role_id' => Role::MANAGER])->whereNotIn('id', $this->excludeIds);
        $portals = Portal::select('name','slug')->get();

        if(in_array($role, [Role::SUPERADMIN,Role::ADMIN]) ){
            $bidsQuery= Bid::query()->with('client');

            if(!empty($managerFilter) && !empty($staffFilter)){
                $bidsQuery =  $bidsQuery->where('admin_id',$staffFilter);
                $staffsQuery = $staffsQuery->where('created_by', $managerFilter);
            }

            if(empty($managerFilter) && !empty($staffFilter)){
                $bidsQuery =  $bidsQuery->where('admin_id',$staffFilter);
                $staffsQuery = $staffsQuery->where('created_by', $managerFilter);
            }

            if(!empty($managerFilter) && empty($staffFilter)){
                $managerId = $managerFilter;
                $ids = Admin::where(['created_by' => $managerId,'status' => 'active'] )->get()->pluck('id')->toArray();
                array_push($ids, $managerId);
                $bidsQuery =  $bidsQuery->whereIn('admin_id',$ids);
                $staffsQuery = $staffsQuery->where('created_by', $managerFilter);
            }

            // generate staffs list for drop down
            $staffs_dd = array_merge($managers->get()->toArray(),$staffsQuery->get()->toArray());
        }
        
        if($role == Role::MANAGER){
            $admin_id = admin()->user()->id;
            // generate staffs list for drop down
            $managerData = $managers->where('id',$admin_id)->get()->toArray();
            $staffData = $staffsQuery->where('created_by',$admin_id)->get()->toArray();
            $staffs_dd = array_merge($managerData,$staffData);

            $staffsQuery = $staffsQuery->where('created_by', $admin_id);
            if(!empty($staffFilter)){
                $bidsQuery =  $bidsQuery->withoutGlobalScope("App\Scopes\AdminIdScope")->where('admin_id',$staffFilter);
            }
        }

        if($portalFilter){
            $bidsQuery = $bidsQuery->where('portal','=',$portalFilter);
        }

        if($bidsFilter != ''){
            $bidsQuery = $bidsQuery->where('is_lead_converted',$bidsFilter);
        }

        if($search){
            $bidsQuery = $bidsQuery->whereRaw(" ( user_name like '%".$search."%' OR job_link like '%".$search."%' OR job_title like '%".$search."%' OR project_type like '%".$search."%' or exists (select * from `leads` where `bids`.`id` = `leads`.`bid_id` and `lead_id` like '%".$search."%' and `leads`.`deleted_at` is null ) )");
        }
       
        if(!empty($dateRange)){
            $dateExp = explode(" - ", $dateRange);
            $start_date = date("Y-m-d", strtotime($dateExp[0]));
            $end_date = date("Y-m-d", strtotime($dateExp[1])); 
            $bidsQuery = $bidsQuery->whereBetween(DB::raw('DATE(bid_date)'), [$start_date, $end_date]);
        }
    
        $connects_sum = $total_record = 0;
        $lead_connects = $bidsQuery->pluck('connects_needed')->toArray();
        $total_record = $bidsQuery->count();
        foreach($lead_connects as $connect){
            $connects_sum += $connect;
        }

        if($request->filled('manager_id')){
            $staffsQuery = $staffsQuery->where('created_by', $request->manager_id);
        }
        $bids = $bidsQuery->sortable(['updated_at' => 'DESC'])->paginate($perPage);
        $managers = $managers->get();

        if (!empty($isAjax)) {
            $html = view('admin.bid.table', compact('bids','staffs_dd','managers','connects_sum','total_record'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.bid.index', compact('bids','portals','staffs_dd','managers','connects_sum','total_record'));
        }
    }

    public function create()
    {
        $portals = Portal::select('name','slug')->get(); 
        $technologies = Technology::select('name','slug')->get(); 
        // $clients_list = Client::query()->get();
        return view('admin.bid.create',compact(['portals','technologies']));
    }
   
    public function store(Request $request)
    {
        $this->validate($request,[
            // 'bid_date'=>['required','date'],
            'bid_quote'=>['required','numeric'],
            'client_budget'=>['required','numeric'],
            'profile'=>['required'],
            'job_title'=>['required','max:200'],
            'job_link'=>['required','unique:bids,job_link','url'],
            'project_type'=>['required'],
            // 'portal'=>['required'],
            'email'=>'nullable|email',
            'connects_needed'=>['required','numeric'],
            'currency'=>['required'],
            'mobile'=>'nullable|numeric|digits_between:6,15|unique:clients,mobile',
            'client_name' => 'nullable|regex:/^[\p{L}\'\-\. ]+$/u',
            // 'status'=>['required'],
            // 'event_type'=>['required'],
            // 'client_id' => ['required_if:event_type,==,dropdown'],
            // 'client_name' => ['required'],
        ]);

        if(!checkBidJobLink($request->job_link,"")){
            return back()->withInput()->with(['job_link' => "The job link has already been taken."]);
        }

        if($request->filled('client_name')){
            $client = Client::create([
                'client_name' => $request->client_name,
                'mobile' => $request->mobile,
                'email' => $request->email,
                'skype' => $request->skype,
                'linkedin' => $request->linkedin,
                'other' => $request->other,
                'location' => $request->location,
            ]);
        }

        Bid::create([
            'bid_date' => Carbon::today(),
            'admin_id' => admin()->user()->id,
            'client_id' => $request->filled('client_name') ? $client->id : NULL,
            'user_name' => admin()->user()->name,
            'job_title' => $request->job_title,
            'job_link' => $request->job_link,
            'project_type' => $request->project_type,
            'portal' => $request->portal,
            'bid_quote' => $request->bid_quote,
            'currency' => $request->currency,
            'client_budget' => $request->client_budget,
            'profile' => $request->profile,
            'description' => $request->description,
            'connects_needed' => $request->connects_needed,
            'technology' => $request->technology,
        ]);

        return redirect()->route('admin.bid.list')->with('success',"Bid added successfully");
    }

    public function view($id)
    {
        $bid = Bid::with('lead', 'client')->find($id);
        return view('admin.bid.view', compact('bid'));
    }
    
    public function edit($id)
    {
        $bid = Bid::with('client')->find($id);
        $portals = Portal::select('name','slug')->get(); 
        $technologies = Technology::select('name','slug')->get();
        return view('admin.bid.edit', compact(['bid','portals','technologies']));
    }
    
    public function update(Request $request, $id)
    {
        $this->validate($request,[
            // 'bid_date'=>['required','date'],
            // 'username'=>['required'],
            'bid_quote'=>['required','numeric'],
            'client_budget'=>['required','numeric'],
            'profile'=>['required'],
            'job_title'=>['required','max:200'],
            'job_link'=>['required','url','unique:bids,job_link,'.$id],
            'project_type'=>['required'],
            // 'portal'=>['required'],
            'email'=>'nullable|email',
            'mobile'=>'nullable|numeric|digits_between:6,15|unique:clients,mobile',
            'client_name' => 'nullable|regex:/^[\p{L}\'\-\. ]+$/u',
            'connects_needed'=>['required','numeric'],
            'currency'=>['required'],
            // 'status'=>['required'],
            // 'client_name' => ['required'],
       ]);

       if(!checkBidJobLink($request->job_link,$id)){
            return redirect()->back()->withInput()->with(['job_link' => "The job link has already been taken."]);
       }

       $bid = Bid::find($id);
       $client = "";
       if($request->filled('client_name')){
            if($bid->client_id != NULL){
                $client = Client::findOrFail($bid->client_id);
                $client->update([
                    'client_name' => $request->client_name,
                    'mobile' => $request->mobile,
                    'email' => $request->email,
                    'linkedin' => $request->linkedin,
                    'skype' => $request->skype,
                    'other' => $request->other,
                    'location' => $request->location,
                ]);
            }else{
                $client = Client::create([
                    'client_name' => $request->client_name,
                    'mobile' => $request->mobile,
                    'email' => $request->email,
                    'linkedin' => $request->linkedin,
                    'skype' => $request->skype,
                    'other' => $request->other,
                    'location' => $request->location,
                ]);
            }
        }

        $bid->update([
            // $bid->bid_date = $request->bid_date,
            // $bid->user_name = $request->username,
            'bid_quote' => $request->bid_quote,
            'client_id' => $request->filled('client_name') ? $client->id : NULL,
            'client_budget' => $request->client_budget,
            'profile' => $request->profile,
            'job_title' => $request->job_title,
            'job_link' => $request->job_link,
            'project_type' => $request->project_type,
            'portal' => $request->portal,
            'connects_needed' => $request->connects_needed,
            'currency' => $request->currency,
            'description' => $request->description,
            'technology' => $request->technology,
        ]);
        
        return redirect()->back()->with('success',"Bid Updated successfully");
    }

    public function destroy($id)
    {
        $bid = Bid::findOrFail($id);
        $bid->delete();
        return redirect()->route('admin.bid.list')->with('success', 'Bid Removed');
    }

    public function changeStatus(Request $request){
        Bid::findOrFail($request->id)->update(['status' => $request->status]);
        // session()->flash('success',"Staff Status Changed !!");
        return response()->json(['status'=>true]);
    }
}