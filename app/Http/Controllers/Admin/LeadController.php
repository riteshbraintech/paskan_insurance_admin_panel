<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Lead;
use App\Models\Bid;
use App\Models\LeadAttachment;
use App\Models\Client;
use App\Models\LeadRemark;
use App\Models\LeadReason;
use App\Models\Log;
use App\Models\Portal;
use App\Models\Role;
use App\Models\Technology;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Models\BudgetHistory;

class LeadController extends Controller
{
    private $excludeIds = [51];
    public function index(Request $request)
    {
        $staffs_dd = [];
        $role = admin()->user()->role_id;
        $current_page = $request->page ?? 1;
        $search = $request->search ?? '';
        $perPage = $request->perPage ?? 50;
        $status = $request->status ?? '';
        $portalFilter = $request->portalFilter ?? '';
        $staffFilter = $request->staffFilter ?? '';
        $managerFilter = $request->managerFilter ?? '';
        $invitedFilter = $request->is_invited ?? '';
        $isAjax = $request->method;  // for ajax check
       
        $date = isset($request->disabledBtn) && $request->disabledBtn == "false" ? "" : now()->startOfMonth()->format('m/d/Y') . ' - ' . now()->endOfMonth()->format('m/d/Y');
        $dateRange = $request->filled('dateRange') ? $request->dateRange : $date ;

        $leadsQuery = Lead::query()->with('client');
        if (isset($request->disabledBtn) && $request->disabledBtn != 'false') {
            $leadsQuery = $leadsQuery->whereHas('admin', function ($query) {
                $query->where('status', 'active');
            });
        }
        
        $staffQuery = Admin::query()->testfilter()->where("role_id",Role::STAFF);
        $managers = Admin::query()->testfilter()->whereNotIn('id',$this->excludeIds)->where('role_id',Role::MANAGER);
        $portals = Portal::select('name','slug')->get();

        if(in_array($role, [Role::SUPERADMIN,Role::ADMIN]) ){
            $leadsQuery = Lead::query()->with('client');

            if(!empty($managerFilter) && !empty($staffFilter)){
                $leadsQuery = $leadsQuery->where('admin_id',$staffFilter);
                $staffQuery = $staffQuery->where('created_by', $managerFilter);
            }

            if(empty($managerFilter) && !empty($staffFilter)){
                $managerFilter = Admin::find($staffFilter)->created_by;
                $leadsQuery = $leadsQuery->where('admin_id',$staffFilter);
                $staffQuery = $staffQuery->where('created_by', $managerFilter);
            }

            if(!empty($managerFilter) && empty($staffFilter)){
                
                $managerId = $managerFilter;
                $ids = Admin::where(['created_by'=> $managerId, 'status' => 'active'])->get()->pluck('id')->toArray();
                array_push($ids, $managerId);
                $leadsQuery = $leadsQuery->whereIn('admin_id',$ids);
                $staffQuery = $staffQuery->where('created_by', $managerFilter);
            }

            // generate staffs list for drop down
            $staffs_dd = array_merge($managers->get()->toArray(),$staffQuery->get()->toArray()); 
        }
 
        if( $role == Role::MANAGER){
            $admin_id = admin()->user()->id;
            $managerData = $managers->where('id',$admin_id)->get()->toArray();
            $staffData = $staffQuery->where('created_by',$admin_id)->get()->toArray();
            $staffs_dd = array_merge($managerData,$staffData);

            if(!empty($staffFilter)){
                $leadsQuery =  $leadsQuery->withoutGlobalScope("App\Scopes\AdminIdScope")->where('admin_id',$staffFilter);
            }

            $staffQuery = $staffQuery->where('created_by', $admin_id);
        }

        if(!empty($dateRange)){
            $dateExp = explode(" - ", $dateRange);
            $start_date = date("Y-m-d", strtotime($dateExp[0]));
            $end_date = date("Y-m-d", strtotime($dateExp[1])); 
            if($status == "awarded"){
                $leadsQuery = $leadsQuery->whereBetween(DB::raw('DATE(awarded_date)'), [$start_date, $end_date]);
            }else{
                $leadsQuery = $leadsQuery->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
            }
        }
        
        if (!empty($invitedFilter)) {
            $leadsQuery = $leadsQuery->where('is_invited', '1');
        } 

        if ($portalFilter) {
            $leadsQuery = $leadsQuery->where('portal', $portalFilter);
        }

        if ($search) {
            $leadsQuery = $leadsQuery->whereRaw(" ( lead_id like '%".$search."%' OR user_name like '%".$search."%' OR job_title like '%".$search."%' OR project_type like '%".$search."%' OR job_link like '%".$search."%' OR status = '$search' or exists (select * from clients where leads.client_id = clients.id and client_name like '%".$search."%'))");
        }
       
        if ($status) {
            $leadsQuery->where('status', $status);
        }

        $connects_sum = 0;
        $lead_connects = $leadsQuery->pluck('connects_needed')->toArray();
        $total_record = $leadsQuery->count();
        foreach($lead_connects as $connect){
            $connects_sum += $connect;
        }
        
        if($request->filled('manager_id')){
            $staffQuery = $staffQuery->where('created_by', $request->manager_id);
        }
        
        $leads = $leadsQuery->sortable(['updated_at' => 'desc'])->paginate($perPage);
        $managers = $managers->get();

        if (!empty($isAjax)) {
            $html = view('admin.lead.table', compact('leads','staffs_dd','managers','connects_sum','total_record'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.lead.index', compact('leads','portals','staffs_dd','managers','connects_sum','total_record'));
        }
    }

    public function create(Request $request)
    {
        $bid = $btn = $client_data = "";
        // $clients_list = Client::query()->get();
        $portals = Portal::select('name','slug')->get(); 
        $technologies = Technology::select('name','slug')->get();
        
        if ($request->filled('id')) {
            $bid = Bid::findOrFail($request->id);
            $client_data = Client::find($bid->client_id);
            $btn = "Convert";
            if ($bid && $bid->is_lead_converted == 1) {
                return redirect()->route('admin.bid.list')->with('error', 'Bid Already Converted');
            }
        }

        return view('admin.lead.create', compact(['bid','client_data','portals','technologies','btn']));
    }

    public function store(Request $request)
    {   
        $this->validate($request, [
            'bid_date' => ['required', 'date'],
            'bid_quote' => ['required', 'numeric'],
            // 'technology' => ['required'],
            'client_budget' => ['required', 'numeric'],
            'profile' => ['required'],
            'job_title' => ['required','max:200'],
            'job_link' => ['required','url','unique:leads,job_link'],
            'project_type' => ['required'],
            'connects_needed' => ['required', 'numeric'],
            'next_followup' => ['required', 'date','after_or_equal:'.Carbon::today()],
            // 'event_type'=>['required'],
            // 'client_id' => ['required_if:event_type,==,dropdown'],
            'client_name' => 'required|regex:/^[a-zA-Z\s]+$/',
            //'email' => ['required_if:event_type,==,input','email','unique:clients,email'],
            //'email' => ['required','email','unique:clients,email,'.$request->client_id.',id'],
            'status' => ['required'], 
            'is_invited' => ['required', 'boolean'],
            'mobile' => ['nullable','numeric','digits_between:6,15'],
            'email'=>['nullable','email:rfc,dns'],
        ]);


        if(empty($request->bidId) && !empty($request->portal) && ($request->portal=='upwork' || $request->portal=='guru')){
            
            $this->validate($request,[
                // 'bid_date'=>['required','date'],
                'bid_quote'=>['required','numeric'],
                'client_budget'=>['required','numeric'],
                'profile'=>['required'],
                'job_title'=>['required','max:200'],
                'job_link'=>['required','unique:leads,job_link','url'],
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
    
            if(!checkLeadJobLink($request->job_link,"")){
                return redirect()->back()->withInput()->with(['job_link' => "The job link has already been taken."]);
            }
    
            if($request->has('client_name') && !empty($request->client_name)){
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
    
            $bid = Bid::create([
                'bid_date' => Carbon::today(),
                'admin_id' => admin()->user()->id,
                'client_id' => $request->has('client_name') && !empty($request->client_name) ? $client->id : NULL,
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
            $request->bidId = $bid->id;        
        }

        if(!checkLeadJobLink($request->job_link,"")){
            return redirect()->back()->withInput()->with(['job_link' => "The job link has already been taken."]);
        }

        try {
            DB::beginTransaction();
            $client="";

            if(empty($request->bidId)){
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
            else{
                $bid = Bid::find($request->bidId);
                if($bid->client_id == NULL){
                    $client = Client::create([
                        'client_name' => $request->client_name,
                        'mobile' => $request->mobile,
                        'email' => $request->email,
                        'skype' => $request->skype,
                        'linkedin' => $request->linkedin,
                        'other' => $request->other,
                        'location' => $request->location,
                    ]);
                    $bid->client_id = $client->id;
                    $bid->save();
                }else{
                    $client = Client::find($bid->client_id);
                    $client->client_name = $request->client_name;
                    $client->mobile = $request->mobile;
                    $client->email = $request->email;
                    $client->skype = $request->skype;
                    $client->linkedin = $request->linkedin;
                    $client->other = $request->other;
                    $client->location = $request->location;
                    $client->save(); 
                }
                     
            }

            $adminId = admin()->user()->id;
            if ($request->has('bidId') && $request->bidId && !empty($request->bidId)) {
                $adminId =  Bid::findOrFail($request->bidId)->admin_id;
            }
            
            $lead = Lead::create([
                'bid_date' => $request->bid_date,
                'bid_id' => $request->bidId ? $request->bidId : null,
                'client_id' => $client->id,
                'admin_id' => $adminId,
                'user_name' => $request->username,
                'next_followup' => $request->next_followup,
                'job_title' => $request->job_title,
                'job_link' => $request->job_link,
                'project_type' => $request->project_type,
                'portal' => !empty($request->portal) ? $request->portal : null,
                'bid_quote' => $request->bid_quote,
                'client_budget' => $request->client_budget,
                'profile' => $request->profile,
                'connects_needed' => $request->connects_needed,
                'technology' => $request->technology,
                'description' => $request->description,
                'status' => $request->status, 
                'is_invited' => $request->input('is_invited', '0'), 
                'bid_created_at' => $request->has('bidId') && !empty($request->bidId) ? $bid->created_at : Carbon::now()->toDateTimeString(),
            ]);
            
            $id = strlen($lead->id) >= 2 ? $lead->id :"0".$lead->id;
            $ldId = 'LEAD-'.$id;
            $log = Log::create([
                'admin_id'=>admin()->user()->id,
                'user_name'=>admin()->user()->email,
                'lead_id'=>$lead->id,
                'lead_show_id'=> $ldId,
                'old_status'=>"",
                'new_status'=> "",
                'page'=>'Create',
                'messages'=>'New Lead Created by: '.admin()->user()->email,
                'extra'=>json_encode(collect($lead)->toArray())
            ]);
            $log->save();

            // bidId update
            if ($request->has('bidId') && $request->bidId && !empty($request->bidId)) {
                Bid::where('id', $request->bidId)->update([
                    'is_lead_converted' => 1,
                ]);
            }
            
            $remark = $request->remark;
           
            if($request->has('remark') && !empty($remark)){
                $this->createLeadRemark($lead,$remark);
            }

            $attachments = $request->attachments;
            $this->saveAttachments($attachments, $lead);

            DB::commit();
            
            if($request->has('bidId') && !empty($request->bidId)){
                return redirect()->route('admin.lead.list')->with('success', 'Bid Converted to Lead Successfully');
            }
            
            return redirect()->route('admin.lead.list')->with('success', 'Lead added Successfully');
        } catch (\Throwable $th) {
            // dd($th);
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function view($id)
    {
        $lead_data = Lead::with('client', 'remarks', 'attachments','budgets')->find($id);
        return view('admin.lead.view', compact('lead_data'));
    }

    public function edit($id)
    {
        $lead = Lead::with('client','remarks','attachments')->findOrFail($id);
        $portals = Portal::select('name','slug')->get(); 
        $technologies = Technology::select('name','slug')->get();
        $clients_list = Client::query()->get();
        return view('admin.lead.edit', compact(['lead','clients_list','portals','technologies']));
    }


    public function viewOnly($id)
    {
        $lead = Lead::with('client', 'remarks', 'attachments','budgets')->find($id);
        return view('admin.lead.viewOnly', compact('lead'));
    }
  
    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'bid_date' => ['required', 'date'],
            'bid_quote' => ['required', 'numeric'],
            // 'description' => ['required'],
            'client_budget' => ['required', 'numeric'],
            'profile' => ['required'],
            'job_title' => ['required','max:200'],
            'job_link' => ['required','url','unique:leads,job_link,'.$id],
            'project_type' => ['required'],
            // 'portal' => ['required'],
            'status' => ['required'],
            'client_name' => 'required|regex:/^[\p{L}\'\-\. ]+$/u',
            'connects_needed' => ['required', 'numeric'],
        ]);
        
        if(!checkLeadJobLink($request->job_link,$id)){
            return redirect()->back()->withInput()->with(['job_link' => "The job link has already been taken."]);
        }

        $lead = Lead::find($id);
        $old_client_budget = $lead->client_budget;

        $remark = $request->remark ?? '';
        if(isset($request->disable_btn) && $request->disable_btn == "on" && $lead->next_followup != $request->next_followup){
            $remark = $remark." Next Follow up date is ". $request->next_followup;
        }

        $excludedStatuses = ["fake_lead","cancelled"];
        
        if(!in_array($lead->status,$excludedStatuses)){
            $lead->update([
                'bid_date' => $request->bid_date,
                'awarded_date' => $request->status == "awarded" ? date('Y-m-d') : NULL,
                // $lead->client_id = $request->client_id,
                // $lead->user_name = $request->username,
                'status' => $request->status,
                'description' => $request->description,
                'bid_quote' => $request->bid_quote,
                'client_budget' => $request->has('client_budget') ? $request->client_budget : $lead->client_budget,
                'profile' => $request->profile,
                'job_title' => $request->job_title,
                'job_link' => $request->job_link,
                'project_type' => $request->project_type,
                'portal' => $request->has('portal') ? $request->portal : $lead->portal,
                'connects_needed' => $request->has('connects_needed') ? $request->connects_needed : $lead->connects_needed,
                'technology' => $request->technology,
                'next_followup' => (isset($request->disable_btn) && $request->disable_btn == "on") ? $request->next_followup : $lead->next_followup
            ]);
        }
        
        $client = Client::findOrFail($lead->client_id);
        $client->update([
            'client_name' => $request->client_name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'linkedin' => $request->linkedin,
            'skype' => $request->skype,
            'other' => $request->other,
            'location' => $request->location,
        ]);
        
        if ($request->filled('remark')) {
            $this->createLeadRemark($lead,$remark);
        }

        $attachments = $request->attachments;
        $this->saveAttachments($attachments, $lead);
       
        if($old_client_budget != $request->client_budget){
            BudgetHistory::create([
                'added_at' => Carbon::now()->toDateTimeString(),
                'budget'=> $request->client_budget,
                'admin_id'=> admin()->user()->id,
                'lead_id'=> $lead->id,
                'is_test'=> admin()->user()->is_test,
            ]);
        }

        if ($lead->bid_id) {
            $bid = Bid::findOrFail($lead->bid_id);
            $bid->update([
                'project_type' => $request->project_type,
                'portal'=>  $request->portal,
                'connects_needed'=> $request->connects_needed,
                'client_budget'=>   $request->client_budget,
                'technology'=>   $request->technology,
                'job_link'=>   $request->job_link,
            ]);
        }
        return redirect()->back()->with('success', 'Lead Updated successfully');
    }

    public function destroy($id)
    {
        $msg = 'Something went wrong try again.';
        $status = 'error';

        $data = Lead::find($id);
        if(!blank($data)){
            $data->delete();
            $status = 'success';
            $msg = 'Lead Deleted.';
        }
        session()->flash($status, $msg);
        return response()->json([
            'status' => $status,
            'message' => $msg,
        ]);
        // return redirect()->route('admin.lead.list')->with('success', 'Lead Removed');
    }
  
    public function statusUpdate(Request $request)
    {
        try {
          
            $leadId = $request->leadIdInput ?? '';
            $leadIdStatus = $request->leadIdStatus ?? '';
            $remark = $request->remark ?? '';
            $reason = $request->reason ?? '';
    
            if (!empty($leadId) && !empty($leadIdStatus)) {
                $lead = Lead::find($leadId);
                $lead->status = $leadIdStatus;
                if($leadIdStatus == "awarded"){
                    $lead->awarded_date = date('Y-m-d');
                }
                $lead->save();
    
                if (!empty($reason)) {
                    LeadReason::create([
                        'lead_id' => $leadId,
                        'admin_id' => admin()->user()->id,
                        'reason' => $reason,
                    ]);
                }
                
                if (!empty($remark)) {
                    $this->createLeadRemark($lead,$remark);
                }
                
                return redirect()->back()->with('success', 'Success');
            } else {
                return redirect()->back()->with('error', 'Something went wrong!');
            }
        } catch (\Throwable $th) {
            // dd($th);
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function log($id){
        $logs = Log::query()->where('lead_id',$id)->latest()->get();
        return view('admin.lead.log', compact(['logs']));
    }

    public function clientEdit($id){
        $client = Client::findOrFail($id);
        return view('admin.lead.client-edit',compact('client'));
    }

    public function viewFormUpdate(Request $request, $id){
        
        $this->validate($request, [
            'bid_quote' => ['required', 'numeric'],
            'status' => ['required'],
            'project_type' => ['required'],
        ]);

        $lead = Lead::find($id);
        
        $remark = $request->remark;
        if(isset($request->disable_btn) && $request->disable_btn == "on"){
            if($lead->next_followup != $request->next_followup){
                $remark = $remark." Next Follow up date is ". $request->next_followup;
            }
        }

        $lead->update([
            'status' => $request->status,
            'awarded_date' => $request->status == "awarded" ? date('Y-m-d') : NULL,
            'bid_quote' => $request->bid_quote,
            'project_type' => $request->project_type,
            'next_followup' => (isset($request->disable_btn) && $request->disable_btn == "on") ? $request->next_followup : $lead->next_followup
        ]);
        
        if ($request->has('remark') && !empty($remark)) {
            $this->createLeadRemark($lead,$remark);
        }

        $attachments = $request->attachments;
        $this->saveAttachments($attachments, $lead);
       
        return redirect()->back()->with('success', 'Lead Updated successfully');
    }

    public function showLeadListPerDate(Request $request){
        $leads = Lead::with('client', 'remarks', 'attachments')->where('next_followup','=',$request->date)->whereNotIn('status', ['dead_lead','fake_lead','awarded'])->get();
        return view('admin.lead.list-per-date', compact('leads'));
    }

    public function exportCSV(){
        $fileName = 'leads.csv';
        
        $leads = Lead::with('admin')->whereHas('admin', function ($query) {
            $query->where('status', 'active');
        })->get();

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        // $columns = array('S.No','adminid', 'lead_id', 'client_id', 'bid_id', 'bid_date','next_followup','status', 'job_title', 'user_name', 'job_link', 'project_type','portal','bid_quote', 'client_budget', 'profile', 'technology', 'description','connects_needed');

        $columns = array('S.No', 'Admin Name', 'Client Name', 'Bid Date','Next Followup','Status', 'Job Title', 'User Name', 'Job Link', 'Project Type','Is Invited','Portal','Bid Quote', 'Client Budget', 'Profile', 'Technology', 'Description','Connects Needed');

        $callback = function() use($leads, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            $SNo = 1;
            foreach ($leads as $lead) {
                $row['S.No']  = $SNo;
                $row['Admin Name']    = $lead->admin->name;
                // $row['Lead ID']    = $lead->lead_id;
                $row['Client Name']  = $lead->client->client_name;
                // $row['Bid ID']  = $lead->bid_id;
                $row['Bid Date']    = $lead->bid_date;
                $row['Next Followup']    = $lead->next_followup;
                $row['Status']  = $lead->status;
                $row['Job Title']  = $lead->job_title;
                $row['User Name']    = $lead->user_name;
                $row['Job Link']    = $lead->job_link;
                $row['Project Type']  = $lead->project_type;
                $row['Is Invited']  = $lead->is_invited;
                $row['Portal']  = $lead->portal;
                $row['Bid Quote']    = $lead->bid_quote;
                $row['Client Budget']    = $lead->client_budget;
                $row['Profile']  = $lead->profile;
                $row['Technology']  = $lead->technology;
                $row['Description']  = $lead->description;
                $row['Connects Needed']  = $lead->connects_needed;
                $SNo++;
                fputcsv($file, array($row['S.No'], $row['Admin Name'], $row['Client Name'], $row['Bid Date'], $row['Next Followup'], $row['Status'],  $row['Job Title'], $row['User Name'], $row['Job Link'], $row['Project Type'],$row['Is Invited'], $row['Portal'], $row['Bid Quote'], $row['Client Budget'], $row['Profile'], $row['Technology'], $row['Description'], $row['Connects Needed']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function updateJob(Request $request)
    {
        $validator = Validator()->make(request()->all(), [
            'id' => 'required|exists:leads,id',
            'job_title' => 'required|max:200',
            'job_link' => 'required|unique:leads,job_link,' . $request->input('id'),
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors();
            return response()->json([
                'status' => false,
                'errors' => $errors,
            ]);
        }

        if(!checkLeadJobLink($request->job_link, $request->input('id'))) {
            return response()->json(['job_link' => "The job link has already been taken."]);
        }

        else{
            $id = $request->input('id');
            $jobTitle = $request->input('job_title');
            $jobLink = $request->input('job_link');
            
            $lead = Lead::findOrFail($id);
            $lead->job_title = $jobTitle;
            $lead->job_link = $jobLink;
            $lead->save();
            
            return response()->json(['status' => true]);
        }
    }

    public function cloneThisLead(Request $request)
    {  
        if(admin()->user()->role_id == Role::STAFF){
            return redirect()->back()->with('error',"You don't have permission to perform following action!");
        }

        try{
            $lead = Lead::findOrFail($request->id);
            if(!blank($lead)){
                $cloned_lead = Lead::create([
                    'admin_id' => $lead->admin_id,
                    'cloned_reference_id' => $lead->id,
                    'client_id' => $lead->client_id,
                    'bid_id' => $lead->bid_id,
                    'bid_date' => $lead->bid_date,
                    'next_followup' => $lead->next_followup,
                    'awarded_date' => date('Y-m-d'),
                    'is_invited' => $lead->is_invited,
                    'status' => $lead->status, 
                    'followup_count' => $lead->followup_count, 
                    'job_title' => $lead->job_title,
                    'user_name' => $lead->user_name,
                    'job_link' => $lead->job_link,
                    'project_type' => $lead->project_type,
                    'portal' => !empty($lead->portal) ? $lead->portal : NULL,
                    'bid_quote' => $lead->bid_quote,
                    'client_budget' => $lead->client_budget,
                    'profile' => $lead->profile,
                    'technology' => $lead->technology,
                    'description' => $lead->description,
                    'connects_needed' => $lead->connects_needed,
                    'bid_created_at' => $lead->bid_created_at,
                    'is_test' => $lead->is_test,
                    'is_cloned' => 0,
                ]);
                        
                $id = strlen($cloned_lead->id) >= 2 ? $cloned_lead->id :"0".$cloned_lead->id;
                $ldId = 'LEAD-'.$id;
                $log = Log::create([
                    'admin_id'=>admin()->user()->id,
                    'user_name'=>admin()->user()->email,
                    'lead_id'=>$cloned_lead->id,
                    'lead_show_id'=> $ldId,
                    'old_status'=>"",
                    'new_status'=> "",
                    'page'=>'Lead Cloned',
                    'messages'=>'Lead Cloned from: '.$lead->lead_id.' to '.$cloned_lead->lead_id. ' by: '.admin()->user()->email,
                    'extra'=>json_encode(collect($cloned_lead)->toArray())
                ]);
                $log->save();

                // store cloned lead id
                $lead->is_cloned =  1;
                $lead->save();

                request()->session()->flash('success',"Lead Cloned Successfully");
                return response()->json([
                    'success' => true,
                    'msg' => "Lead Cloned Successfully",
                ]);
            }
        } catch (\Throwable $th) {
            // dd($th);
            DB::rollback();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    protected function saveAttachments($attachments, $lead)
    {
        if (!empty($attachments) && is_array($attachments)) {
            foreach ($attachments as $attachment) {
                $path = public_path(LeadAttachment::ATTACHMENT_PATH);
                $attachment_name = $attachment->getClientOriginalName();
                $attachment_extension = $attachment->getClientOriginalExtension();
                $attachment->move($path, $attachment_name);

                LeadAttachment::create([
                    'lead_id' => $lead->id,
                    'attachment' => $attachment_name,
                    'extension' => $attachment_extension,
                    'admin_id' => admin()->user()->id,
                ]);
            }
        }
    }

    protected function createLeadRemark($lead,$remark){
        LeadRemark::create([
            'lead_id' => $lead->id,
            'remark' => $remark,
            'admin_id' => admin()->user()->id,
        ]);
    }
}
