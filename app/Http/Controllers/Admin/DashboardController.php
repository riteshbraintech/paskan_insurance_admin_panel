<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Bid;
use App\Models\Lead;
use App\Models\Portal;
use App\Models\Role;
use App\Scopes\IsClonedScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Session;
class DashboardController extends Controller
{
    private $excludeIds = [42, 33, 51];
    public function index(Request $request)
    {
        $staffs_list = $staffs_dd = [];
        $role_id = admin()->user()->role_id;
        $IsAjax = $request->method;
        $staff_filter = $request->staff_filter ?? "" ;
        $manager_filter = $request->manager_filter ?? "" ;
        $graph_filter = $request->graph_filter ?? "weekly";

        $date = now()->startOfMonth()->format('m/d/Y') . ' - ' . now()->endOfMonth()->format('m/d/Y');
        $dateRange = $request->filled('dateRange') ? $request->dateRange : $date ;
        
        $bid = Bid::query();
        $portals = Portal::select('slug','name')->get();
        $leadQuery = Lead::query();
        $hot_lead = Lead::query()->where('status','hot_lead');
        $awardeds= Lead::query()->where('status','awarded');

        $staffs = Admin::query()->testfilter()->where('role_id',Role::STAFF)->where('status','active')->whereNotIn('id', $this->excludeIds);
        
        $managers = Admin::query()->testfilter()->where(['role_id' => Role::MANAGER,'status' => 'active'])->whereNotIn('id', $this->excludeIds);

        $targetLeadsQuery = Lead::query()->with('admin','budgets')->withoutGlobalScope(IsClonedScope::class)->where('status','awarded')
            ->whereHas('admin', function ($query) {
                $query->where('status', 'active')->whereNotIn('id', $this->excludeIds); 
            });

        $total_hot_lead = $hot_lead->count();
            
        if(in_array($role_id, [Role::ADMIN, Role::SUPERADMIN])){
                
            $staffs_list = $managers->pluck('name', 'id')->union($staffs->pluck('name', 'id'))->toArray();
            if(!empty($staff_filter)){
                $bid =  $bid->where('admin_id',$staff_filter);
                $leadQuery = $leadQuery->where('admin_id',$staff_filter);
                $hot_lead =  $hot_lead->where('admin_id',$staff_filter);
                $awardeds = $awardeds->where('admin_id',$staff_filter);  
                $staffs_list = Admin::query()->testfilter()->where(['status' => 'active','id' => $staff_filter])->get()->pluck('name','id')->toArray();
            }

            else if(!empty($manager_filter) && empty($staff_filter)){
                $managerId = (int)$manager_filter;
                
                $current_manager = $managers->where('id' , $managerId)->pluck('name','id')->toArray();
                $staffs_list = $current_manager;

                $staff_under_manager = $staffs->where('created_by', $managerId)->pluck('name', 'id')->toArray();
                $staffs_list += $staff_under_manager;
                // Collect all relevant admin IDs
                $ids = array_merge(array_keys($staff_under_manager), [$managerId]);
                
                $bid =  $bid->whereIn('admin_id',$ids);
                $leadQuery = $leadQuery->whereIn('admin_id',$ids);
                $hot_lead = $hot_lead->whereIn('admin_id',$ids);
                $awardeds= $awardeds->whereIn('admin_id',$ids);
            }

            // generate staffs list for drop down
            $staffs_dd = array_merge($managers->get()->toArray(),$staffs->get()->toArray());
        }

        if($role_id == Role::MANAGER){
            $admin_id = admin()->user()->id;
            
            // Get current manager info
            $current_manager = $managers->where('id',$admin_id)->pluck('name','id')->toArray();
            $staffs_list = $current_manager;
            
            // Filter staff under current manager
            $staffs = $staffs->where('created_by',$admin_id);
            $staffs_list += $staffs->pluck('name','id')->toArray();

            // Remove global scope and filter by selected staff
            if(!empty($staff_filter)){
                $bid =  $bid->withoutGlobalScope("App\Scopes\AdminIdScope")->where('admin_id',$staff_filter);
                $leadQuery = $leadQuery->withoutGlobalScope("App\Scopes\AdminIdScope")->where('admin_id',$staff_filter);
                $targetLeadsQuery = $targetLeadsQuery->withoutGlobalScope("App\Scopes\AdminIdScope")->where('admin_id',$staff_filter);
                $hot_lead =  $hot_lead->withoutGlobalScope("App\Scopes\AdminIdScope")->where('admin_id',$staff_filter);
                $awardeds = $awardeds->withoutGlobalScope("App\Scopes\AdminIdScope")->where('admin_id',$staff_filter);

                $staffs_list = Admin::query()->testfilter()->where(['status' => 'active','id' => $staff_filter])->pluck('name','id')->toArray();
            }

            // generate staffs list for drop down
            $staffs_dd = array_merge($managers->get()->toArray(),$staffs->get()->toArray());
        }

        if($role_id == Role::STAFF){
            $staffs_list = $staffs->where('id',admin()->user()->id)->get()->pluck('name','id')->toArray();
        }

        $dateExp = explode(" - ", $dateRange);
        $start_date = date("Y-m-d", strtotime($dateExp[0]));
        $end_date = date("Y-m-d", strtotime($dateExp[1])); 
       
        if(!empty($dateRange)){
            $bid = $bid->whereBetween(DB::raw('DATE(bid_date)'), [$start_date, $end_date])->get();
            $leadQuery = $leadQuery->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date])->get();
            $hot_lead = $hot_lead->whereBetween(DB::raw('DATE(bid_date)'), [$start_date, $end_date])->get();
            $awardeds = $awardeds->whereBetween(DB::raw('DATE(awarded_date)'), [$start_date, $end_date])->get();
        }
        
        // total lead and bid hover staff stats and pie chart data 
        $staffStats = $this->getStaffStats($staffs_list,$start_date,$end_date);
        $staff_bid_list = $staffStats['staff_bid_list']; 
        $staff_lead_list = $staffStats['staff_lead_list']; 
        // end
        
        $lead = $leadQuery;
        
        // calender data
        $calnder_datas = $this->getCalendarData();
        //end
        
        // graph data
        if(!empty($graph_filter)){
            $month_dates = [];
            $start_date = date("d-m-Y", strtotime($start_date));
            $end_date = date("d-m-Y", strtotime($end_date));

            $month_dates = getDates($start_date,$end_date,$graph_filter);
            $awarded_per_month = getAwardedPerMonth($lead,$month_dates,$graph_filter);
            $record = getBidsLeadsPerMonth($bid,$month_dates,$graph_filter);
            // dd($month_dates,$awarded_per_month,$record);
            if($IsAjax == "graph-ajax"){
                $html = view('admin.graph', compact('month_dates','record','awarded_per_month','graph_filter'))->render();
                return response()->json(['html' => $html]);
            }
        }

        $managers_dd = $managers->get();
        $total_hot_lead = $total_hot_lead ?? 0;

        // todays followup data
        $firstdate = date('Y-m-d');
        $seconddate = date('Y-m-d', strtotime($firstdate. ' - 15 days'));
        $todayFollowUpLeads = Lead::whereBetween(DB::raw('DATE(next_followup)'), [$seconddate, $firstdate])->whereNotIn('status', ['dead_lead','fake_lead','awarded'])->orderBy('next_followup', 'DESC')->get();
        
        if (!empty($IsAjax)) {
            $html = view('admin.dashboard-content', compact('staff_bid_list','staff_lead_list','bid','lead','hot_lead','awardeds','staffs_dd','managers_dd','calnder_datas','month_dates','record','awarded_per_month','graph_filter','todayFollowUpLeads','total_hot_lead','portals'))->render();

            return response()->json(['html' => $html]);

        } else {
            // target data
            $data = $this->getTargetData($targetLeadsQuery);
            $target = $data['target'];
            $received = $data['received'];
            $archieved = $data['archieved'];
            $short_fall = $data['short_fall'];
            
            return view('admin.dashboard',compact('staff_bid_list','staff_lead_list','bid','lead','hot_lead','awardeds','staffs_dd','managers_dd','calnder_datas','month_dates','record','awarded_per_month','graph_filter','todayFollowUpLeads','total_hot_lead','portals','target','received','archieved','short_fall'));
        }        
    }

    private function getCalendarData(){
        $calnder_datas = [];
        $calnder_dates = Lead::selectRaw('next_followup, COUNT(next_followup) as total_followup')->groupBy('next_followup')->whereNotIn('status', ['dead_lead','fake_lead','awarded'])->get();
        if(!empty($calnder_dates) && !blank($calnder_dates)){
            foreach ($calnder_dates as $key => $value) {
                array_push($calnder_datas, [
                    "id" => rand(),
                    "title"=> $value->total_followup, 
                    "start"=> $value->next_followup,
                    "backgroundColor"=>'red',
                    "allDay"=> false, 
                    "editable"=> false,
                ]);
            }
        }
        return $calnder_datas;
    }

    private function getStaffStats($staffs_list,$start_date,$end_date){
        $staff_bid_list = $staff_lead_list = [];
        foreach($staffs_list as $key=>$val){

            $bid_count = Bid::query()->where('admin_id',$key)
                ->whereBetween(DB::raw('DATE(bid_date)'),[$start_date,$end_date])
                ->count();

            $lead_count = Lead::query()->where('admin_id',$key)
                ->whereNotIn('status', ['fake_lead','cancelled'])
                ->whereBetween(DB::raw('DATE(created_at)'),[$start_date,$end_date])
                ->count();

            $invited_open_count = Lead::where('admin_id', $key)
                ->where('is_invited', 1)
                ->where('status', 'open')
                ->whereBetween(DB::raw('DATE(bid_date)'), [$start_date, $end_date])
                ->count();
            
            $percentage = 0;
            if( $bid_count && $lead_count){
                $percent_inner = round((($lead_count - $invited_open_count) / $bid_count) * 100,2);
                $percentage = $percent_inner > 100 ? 100 : $percent_inner;
            }

            $staff_bid_list[$val] = [
                'bid' => $bid_count,
                'lead' => $lead_count,
                'percentage' => $percentage,
            ];
            
            $staff_lead_list[$val] = [
                'lead_count' => $lead_count,
                'percentage' => $percentage,
            ];
        }

        $data['staff_bid_list'] = $staff_bid_list;
        $data['staff_lead_list'] = $staff_lead_list;
        return $data;
    }

    public function getTargetData($targetLeadsQuery){
        // logic for dashboard target report
        $target = 50000;
        $data = [];
        $total_sale = $total_Received = $archieved = $received = $short_fall = 0;

        $dateRange = $this->getCurrentQuarterDate();
       
        $dateExp = explode(" - ", $dateRange);
        $start_date = date("Y-m-d", strtotime($dateExp[0]));
        $end_date = date("Y-m-d", strtotime($dateExp[1])); 
    
        if(!empty($dateRange)){
            $targetLeadsQuery = $targetLeadsQuery->whereBetween(DB::raw('DATE(awarded_date)'), [$start_date, $end_date]);
        }

        $leads = $targetLeadsQuery->get();
        foreach($leads as $item){
            $sum = 0;   
                
            foreach($item->budgets as $budget){
                $sum = $sum + $budget['budget'];
            }
            
            $total_Received = $total_Received + $sum;

            if($item->project_type == "fixed")        
                $total_sale = $total_sale + $item->client_budget;
            elseif($item->project_type == "hourly")
                $total_sale = $total_sale + $sum;
        }
        $data['target'] = $target;
        $data['archieved'] = $total_sale;
        $data['received'] = $total_Received;
        $data['short_fall'] = $target - $total_sale;
        
        return $data;
    }
    public function getCurrentQuarterDate(){
        // Get the current month
        $currentMonth = date('n'); // Numeric representation of the current month (1-12)

        // Determine the starting and ending months of the current quarter
        $quarterStartMonth = floor(($currentMonth - 1) / 3) * 3 + 1;
        $quarterEndMonth = $quarterStartMonth + 2;

        // Get the start and end dates of the current quarter
        $quarterStartDate = date('m/d/Y', strtotime("first day of " . date("F", mktime(0, 0, 0, $quarterStartMonth, 1)) . " this year"));
        $quarterEndDate = date('m/d/Y', strtotime("last day of " . date("F", mktime(0, 0, 0, $quarterEndMonth, 1)) . " this year"));

        // Combine the date range
        $quarterDateRange = $quarterStartDate . ' - ' . $quarterEndDate;

        return $quarterDateRange;
    }
    public function sessionMode(Request $request){
        $crValue = blank($request->session()->get('is_test')) ? false : $request->session()->get('is_test');
        session(['is_test'=> $crValue ? false : true ]);
        return back();
    }
    public function refreshTargetData(Request $request){
        $target = 50000;
        $date = $this->getCurrentQuarterDate();
        $dateRange = $request->dateRange ?? $date ;

        $targetLeadsQuery = Lead::query()->with('admin','budgets')->withoutGlobalScope(IsClonedScope::class)->where('status','awarded')
            ->whereHas('admin', function ($query){
                $query->where('status', 'active')->whereNotIn('id', $this->excludeIds); 
            });

        if($request->filled('portalFilter')){
            $targetLeadsQuery = $targetLeadsQuery->where('portal',$request->portalFilter);
        }
        
        if($request->filled('staff_filter')){
            $targetLeadsQuery = $targetLeadsQuery->where('admin_id',$request->staff_filter);
        }

        if($request->filled('manager_filter')){
            $targetLeadsQuery = $targetLeadsQuery->where('admin_id',$request->manager_filter);
        }

        $dateExp = explode(" - ", $dateRange);
        $start_date = date("Y-m-d", strtotime($dateExp[0]));
        $end_date = date("Y-m-d", strtotime($dateExp[1])); 
       
        if(!empty($dateRange)){
            $targetLeadsQuery = $targetLeadsQuery->whereBetween(DB::raw('DATE(awarded_date)'), [$start_date, $end_date]);
        }

        $leads = $targetLeadsQuery->get();

        $total_sale = $total_Received = $archieved = $received = $short_fall = 0;

        foreach($leads as $item){
            $sum = 0;   
                
            foreach($item->budgets as $budget){
                $sum = $sum + $budget['budget'];
            }
            
            $total_Received = $total_Received + $sum;

            if($item->project_type == "fixed")        
                $total_sale = $total_sale + $item->client_budget;
            elseif($item->project_type == "hourly")
                $total_sale = $total_sale + $sum;
        }

        $archieved = $total_sale;
        $received = $total_Received;
        $short_fall = $target - $archieved;

        return response()->json([
            'archieved' => $archieved,
            'received' => $received,
            'short_fall' => $short_fall,
        ]);
    }
}
