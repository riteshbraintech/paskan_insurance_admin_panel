<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $current_page = $request->page ?? 1;
        $search = $request->search ?? '';
        $perPage = $request->perPage ?? 10;
        $leadFilter = $request->leadFilter ?? '';

        $dateRange = $request->dateRange ?? now()->startOfMonth()->format('m/d/Y') . ' - ' . now()->endOfMonth()->format('m/d/Y');
        $isAjax = $request->method;

        $logs = Log::query()->select('created_at','messages','new_status','old_status','page','lead_show_id','user_name','is_test');
        $leads = Lead::query()->select('id','lead_id');
        
        if($leadFilter) {
            $logs = $logs->where('lead_id', $leadFilter);
        }   

        if ($search) {
            $logs = $logs->whereRaw(" ( user_name like '%".$search."%' OR lead_show_id like '%".$search."%' OR page = '$search')"); 
        }
       
        if(!empty($dateRange)){
            $dateExp = explode(" - ", $dateRange);
            $start_date = date("Y-m-d", strtotime($dateExp[0]));
            $end_date = date("Y-m-d", strtotime($dateExp[1])); 
            $logs = $logs->whereBetween(DB::raw('DATE(created_at)'), [$start_date, $end_date]);
        }

        $logs = $logs->latest()->paginate($perPage);
        $leads = $leads->get();
        if (!empty($isAjax)) {
            $html = view('admin.log.table', compact(['logs','leads']))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.log.index', compact(['logs','leads']));
        }
        
    }
}
