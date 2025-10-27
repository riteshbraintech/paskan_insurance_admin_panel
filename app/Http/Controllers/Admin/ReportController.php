<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Bid;
use App\Models\BudgetHistory;
use App\Models\Lead;
use App\Models\Portal;
use App\Models\Role;
use App\Scopes\IsClonedScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class ReportController extends Controller
{
    private $excludeIds = [42, 33, 51];
    public function index(Request $request)
    {
        $staffs_dd = [];
        $role = admin()->user()->role_id;
        
        $startOfQuarter = Carbon::now()->firstOfQuarter()->format('m/d/Y');
        $endOfQuarter = Carbon::now()->lastOfQuarter()->format('m/d/Y');
        $dateRange = $request->dateRange ?? $startOfQuarter . ' - ' . $endOfQuarter;
        // $dateRange = $request->dateRange ?? now()->startOfMonth()->format('m/d/Y') . ' - ' . now()->endOfMonth()->format('m/d/Y');
        
        $current_page = $request->page ?? 1;
        $search = $request->search ?? '';
        $perPage = $request->perPage ?? 10;
        $status = $request->status ?? '';
        $portalFilter = $request->portalFilter ?? '';
        $staffFilter = $request->staffFilter ?? '';
        $managerFilter = $request->managerFilter ?? '';
        $isAjax = $request->method;  // for ajax check
        
        $leadsQuery = Lead::query()
                    ->with('admin')
                    ->withoutGlobalScope(IsClonedScope::class)
                    ->where('status','awarded')
                    ->whereHas('admin', function ($query){
                        $query->where('status', 'active')->whereNotIn('id', $this->excludeIds); 
                    });

        $staffQuery = Admin::query()->testfilter()->where([ 'role_id' => Role::STAFF]);
        $managers = Admin::query()->testfilter()->whereNotIn('id',$this->excludeIds)->where([ 'role_id' => Role::MANAGER]);
        $portals = Portal::select('name','slug')->get();

        if(in_array($role, [Role::SUPERADMIN,Role::ADMIN]) ){

            $leadsQuery = Lead::query()->with('admin')->where('status','awarded');
            
            if(!empty($managerFilter) && !empty($staffFilter)){
                $leadsQuery =  $leadsQuery->where('admin_id',$staffFilter);
                $staffQuery = $staffQuery->whereRaw("( created_by = '$managerFilter')");
            }

            if(empty($managerFilter) && !empty($staffFilter)){
                $leadsQuery =  $leadsQuery->where('admin_id',$staffFilter);
                $staffQuery = $staffQuery->whereRaw("(created_by = '$managerFilter')");
            }

            if(!empty($managerFilter) && empty($staffFilter)){
                
                $managerId = (int)$managerFilter;
                $ids = Admin::where(['created_by' => $managerId, 'status' => 'active'])->get()->pluck('id')->toArray();
                array_push($ids, $managerId);
                $leadsQuery =  $leadsQuery->whereIn('admin_id',$ids);
                $staffQuery = $staffQuery->where('created_by', $managerFilter);
            }

            // generate staffs list for drop down
            $staffs_dd = array_merge($managers->get()->toArray(),$staffQuery->get()->toArray()); 
        }
 
        if($role == Role::MANAGER){
            $idFilter = admin()->user()->id;
            $managers = $managers->whereRaw("(id = '$idFilter')");
            $staffQuery = $staffQuery->whereRaw("(created_by = '$idFilter')");

            if(!empty($staffFilter)){
                $leadsQuery = $leadsQuery->withoutGlobalScope("App\Scopes\AdminIdScope")->where('admin_id',$staffFilter);
            }
            
            // Generate staffs list for dropdown
            $staffs_dd = array_merge($managers->get()->toArray(),$staffQuery->get()->toArray());
        } 
        
        if(!empty($dateRange)){
            $dateExp = explode(" - ", $dateRange);
            $start_date = date("Y-m-d", strtotime($dateExp[0]));
            $end_date = date("Y-m-d", strtotime($dateExp[1])); 
            $leadsQuery = $leadsQuery->whereBetween(DB::raw('DATE(awarded_date)'), [$start_date, $end_date]);
        }

        if ($portalFilter) {
            $leadsQuery = $leadsQuery->where('portal', $portalFilter);
        }

        if ($search) {
            $leadsQuery = $leadsQuery->whereRaw(" ( lead_id like '%".$search."%' OR user_name like '%".$search."%' OR job_title like '%".$search."%' OR project_type like '%".$search."%' OR portal like '%".$search."%')");
        }

        if ($status) {
            $leadsQuery = $leadsQuery->where('status', $status);
        }

        if($request->filled('manager_id')){
            $staffQuery = $staffQuery->where('created_by', $request->manager_id);
        }

        $leads = $leadsQuery->sortable()->get();
        $managers = $managers->get();

        // to show user with their total incentive
        $user = $this->generateUserWithTotalSale($leads);
    
        if (!empty($isAjax)) {
            $html = view('admin.report.table', compact('leads','staffs_dd','managers','user'))->render();
            return response()->json(['html' => $html]);
        } else {
            return view('admin.report.index', compact('leads','portals','staffs_dd','managers','user'));
        }
    }

    public function generateUserWithTotalSale($leads){
        $user = [];

        foreach ($leads as $item) {
            $adminId = $item->admin_id;

            $sum = 0;
            foreach($item->budgets as $budget){
                $sum = $sum + $budget['budget'];
            }

            $incentive = ($item->project_type === "fixed") ? $item->client_budget : $sum;

            if(!isset($user[$adminId])){
                $user[$adminId] = [
                    'user' => $item->user_name, 
                    'incentive' => $incentive, 
                    'rec'=> $sum,
                    'total_leads' => 1,  
                    'total_bids' => 1, 
                    'lead_percentage' => 0, 
                    'is_test' => $item->admin ? $item->admin->is_test : 'Not Found'
                ];
            }else{
                $user[$adminId]['rec'] += $sum;
                $user[$adminId]['incentive'] += $incentive;
                $user[$adminId]['total_leads'] += 1;
                $user[$adminId]['total_bids'] += 1;
            }
        }

        foreach ($user as &$data) {
            if ($data['total_bids'] > 0) {
                $data['lead_percentage'] = ($data['total_leads'] / $data['total_bids']) * 100;
            }
        }
       
        return $user;
    }

    public function generateManagerTotalSale($leads , $request){
        $incentive = $received=0;
        $managerList = [];

        $managersQuery = Admin::query()->testfilter()->where(['status' => 'active', 'role_id' => Role::MANAGER])->whereNotIn('id', $this->excludeIds);

        $currentUser = admin()->user();

        if(in_array($currentUser->role_id, [Role::ADMIN,Role::SUPERADMIN]) ){

            if(!empty($request->managerFilter)){
                $managersQuery = $managersQuery->where('id',$request->managerFilter);
            }elseif(empty($request->managerFilter) && !empty($request->staffFilter)){
                $staff = Admin::query()->testfilter()->where(['status' => 'active', 'id' => $request->staffFilter])->first();

                if (!blank($staff)) {
                    if ($staff->role_id == Role::MANAGER) {
                        $managersQuery = $managersQuery->where('id', $staff->id);
                    } elseif ($staff->role_id == Role::STAFF) {
                        $managersQuery = $managersQuery->whereRaw("(id = '$staff->created_by')");
                    }
                }

            }

        }elseif($currentUser->role_id == Role::MANAGER){
            $managersQuery = $managersQuery->where('id',$currentUser->id);
        }
        
        $managers = $managersQuery->get();
        
        foreach($managers as $manager){
            
            $staffIds = Admin::where(['role_id' => Role::STAFF,'created_by' => $manager->id,'status' => 'active' ])->whereNotIn('id', $this->excludeIds)->get()->pluck('id')->toArray();

            array_merge($staffIds, [$manager->id]);
            
            $managerList[$manager->id] = $staffIds;
        }
        
        foreach($managerList as $key => $val){
            $managerList[$key] = 0;
            foreach($leads->whereIn('admin_id',$val) as $val){
                
                $sum = 0;
                foreach($val->budgets as $budget){
                    $sum = $sum + $budget['budget'];
                }

                if ($val->project_type == "fixed"){
                    $managerList[$key] = $managerList[$key] + $incentive + $val->client_budget;
                }else{
                    $managerList[$key] = $managerList[$key] + $incentive + $sum;
                }
                
                $managerList[$key] = $managerList[$key] + $received + $sum;
                $received=0;
                $incentive = 0;
            }
        }
        return $managerList;
    }

    private function getStaffLeadData($leads, $request){
        $staffLeadData = [];

        $managerIds = Admin::where('role_id', Role::MANAGER)
            ->whereStatus('active')
            ->pluck('id')
            ->toArray();

        $staffMembers = Admin::whereIn('created_by', $managerIds)
            ->whereStatus('active')
            ->get();

        foreach ($staffMembers as $staff) {
            $staffLeads = $leads->where('admin_id', $staff->id);
            $leadCount = $staffLeads->count();
            $bidCount = $staffLeads->where('status', 'awarded')->count();
            $bidRatio = $leadCount > 0 ? ($bidCount / $leadCount) * 100 : 0;
        
            $staffLeadData[$staff->id] = [
                'name' => $staff->name,
                'leadCount' => $leadCount,
                'bidCount' => $bidCount,
                'bidRatio' => $bidRatio,
            ];
        }
    
        return $staffLeadData;
    }

    public function exportIncentiveExcel(Request $request){
        $baseLeadQuery = Lead::with('admin')->withoutGlobalScope(IsClonedScope::class)->where('status','awarded')->whereHas('admin', function ($query) {
            $query->where('status', 'active')->whereNotIn('id', $this->excludeIds);
        });
        
        $currentUser = admin()->user();
        $role = $currentUser->role_id;

        if(in_array($role, [Role::ADMIN, Role::SUPERADMIN]) ){

            if(!empty($request->managerFilter) && !empty($request->staffFilter)){

                $baseLeadQuery = $baseLeadQuery->where('admin_id', $request->staffFilter);
                
            }elseif(empty($request->managerFilter) && !empty($request->staffFilter)){
                
                $baseLeadQuery = $baseLeadQuery->where('admin_id', $request->staffFilter);

            }elseif(!empty($request->managerFilter) && empty($request->staffFilter)){
                
                $managerId = (int)$request->managerFilter;
                $ids = Admin::where(['created_by' => $managerId, 'status' => 'active'])->whereNotIn('id', $this->excludeIds)->get()->pluck('id')->toArray();

                array_push($ids, $managerId); 
                $baseLeadQuery = $baseLeadQuery->whereIn('admin_id', $ids);
            }
        }elseif($role == Role::MANAGER){
            if(!empty($request->staffFilter)){
                $baseLeadQuery = $baseLeadQuery->withoutGlobalScope("App\Scopes\AdminIdScope")->where('admin_id',$request->staffFilter);
            }
        } 

        if ($request->filled('status')) {
           $baseLeadQuery = $baseLeadQuery->where('status', $request->status);
        }

        if ($request->filled('portalFilter')) {
            $baseLeadQuery = $baseLeadQuery->where('portal', $request->portalFilter);
        }

        if($request->filled('dateRange')){
            $dateExp = explode(" - ", $request->dateRange);
            $start_date = date("Y-m-d", strtotime($dateExp[0]));
            $end_date = date("Y-m-d", strtotime($dateExp[1])); 
            $baseLeadQuery = $baseLeadQuery->whereBetween(DB::raw('DATE(awarded_date)'), [$start_date, $end_date]);
        }
        
        $leads = $baseLeadQuery->get();
        
        $user = $user = $this->generateUserWithTotalSale($leads);
        $managerListWithTotalSale = $this->generateManagerTotalSale($leads,$request);

        $managers = $data = [];
        
        $BDMTeamIncentive = $BDMIncentive = $carryForwardToNextQtrTgt = $quarterlyTeamIncentive = $surplusSalesInTheQtr = $shortageDeviationFromMin = $minimumAcceptedQtrlyTarget = $qtrlyTarget = $actualSalesAchievedInQtr = 0.0;
        
        foreach($managerListWithTotalSale as $key => $val){
            $managers[$key] = Admin::query()->testfilter()->where(['role_id' => Role::MANAGER, 'status' => 'active' , 'id' => $key])->first();
            $managers[$key]->totalSale = $val;
        }
        
        if(count($managers) > 0){
            foreach($managers as $key => $manager){
                $actualSalesAchievedInQtr = (float)$manager->totalSale;   
                $qtrlyTarget = (float)$manager->qtrly_target ?? 0.00;
                $minimumAcceptedQtrlyTarget = (float)$manager->minimum_accepted_qtrly_target ?? 0.00;
                $shortageDeviationFromMin = ($actualSalesAchievedInQtr - $qtrlyTarget) > 0 ? 0.0 : ($actualSalesAchievedInQtr - $qtrlyTarget);
                $surplusSalesInTheQtr = ($actualSalesAchievedInQtr - $qtrlyTarget) > 0 ? ($actualSalesAchievedInQtr - $qtrlyTarget) : 0.0 ;
                $quarterlyTeamIncentive =  $surplusSalesInTheQtr * 4;
                $carryForwardToNextQtrTgt =  $shortageDeviationFromMin;
                $BDMIncentive = $quarterlyTeamIncentive * (70/100);
                $BDMTeamIncentive = $quarterlyTeamIncentive * (30/100);

                $array = [
                    'Manager Name' => $manager->name,
                    'Actual Sales Achieved in a Qtr' => '$ '.$actualSalesAchievedInQtr , 
                    'Qtrly Target ($)' => '$ '.$qtrlyTarget , 
                    'Minimum Accepted Qtrly Tgt ($)' => '$ '.$minimumAcceptedQtrlyTarget, 
                    'Shortage Deviation from Min.' => '$ '.$shortageDeviationFromMin,
                    'Surplus Sales in the Qtr ($)' => '$ '.$surplusSalesInTheQtr, 
                    'Quarterly Team Incentive (Rs.)' => '₹ '.$quarterlyTeamIncentive, 
                    'Carry Forward to Next Qtr Tgt.' => '$ '.abs($carryForwardToNextQtrTgt), 
                    'BDM Incentive' => 'INR '.$BDMIncentive, 
                    'BDE Team Incentive' => 'INR '.$BDMTeamIncentive, 
                    'Lead Generation Additional Incentive:-' => '',
                ];

                $data[$key] = $array;
            }
        }

        // Create new Spreadsheet object
        $spreadsheet = new Spreadsheet();

        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // Font color (black)
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFC300'], // Background color (yellow)
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Center alignment
                'vertical' => Alignment::VERTICAL_CENTER, // Middle alignment
            ],
            // 'borders' => [
            //     'allBorders' => [
            //         'borderStyle' => Border::BORDER_THIN, // Thin border
            //         'color' => ['rgb' => '000000'], // Border color (black)
            //     ],
            // ],
        ];

        $headStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // Font color (black)
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5C001'], // Background color (yellow)
            ],
            // 'alignment' => [
            //     'horizontal' => Alignment::HORIZONTAL_CENTER, // Center alignment
            //     'vertical' => Alignment::VERTICAL_CENTER, // Middle alignment
            // ],
            // 'borders' => [
            //     'allBorders' => [
            //         'borderStyle' => Border::BORDER_THIN, // Thin border
            //         'color' => ['rgb' => '000000'], // Border color (black)
            //     ],
            // ],
        ];

        $dataStyle = [
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT, // Right alignment
                'vertical' => Alignment::VERTICAL_CENTER, // Middle alignment
            ],
            // 'borders' => [
            //     'allBorders' => [
            //         'borderStyle' => Border::BORDER_THIN, // Thin border
            //         'color' => ['rgb' => '000000'], // Border color (black)
            //     ],
            // ],
        ];

        $mergeStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // Font color (black)
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F5C001'], // Background color (yellow)
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Center alignment
                'vertical' => Alignment::VERTICAL_CENTER, // Middle alignment
            ],
            // 'borders' => [
            //     'allBorders' => [
            //         'borderStyle' => Border::BORDER_THIN, // Thin border
            //         'color' => ['rgb' => '000000'], // Border color (black)
            //     ],
            // ],
        ];
        
        
        $greenBackgroundStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFB00'], // Font color 
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '44B149'], // Background color (green)
            ],
            
            // 'borders' => [
            //     'allBorders' => [
            //         'borderStyle' => Border::BORDER_THIN, // Thin border
            //         'color' => ['rgb' => '000000'], // Border color (black)
            //     ],
            // ],
        ];

        $greyBackgroundStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // Font color 
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D9D9D9'], // Background color (green)
            ],  
        ];

        $yellowBackgroundStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // Font color 
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'FFC300'], // Background color (green)
            ],
        ];

        $boldAlignStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // Font color 
            ],

            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT, // Right alignment
                'vertical' => Alignment::VERTICAL_CENTER, // Middle alignment
            ],            
           
        ];

        $boldStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => '000000'], // Font color 
            ],
        
        ];


        $leadAwardStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'], // Font color (black)
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '305496'], // Background color (yellow)
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER, // Center alignment
                'vertical' => Alignment::VERTICAL_CENTER, // Middle alignment
            ],
        
        ];

        // first sheet
        $incentiveSheet = $spreadsheet->getActiveSheet();
        $incentiveSheet->setTitle('Incentive');
        $incentiveSheet->getColumnDimension('A')->setWidth(40);
        $incentiveSheet->getColumnDimension('B')->setWidth(30);

        // second sheet
        $leadSheet = $spreadsheet->createSheet();
        $leadSheet->setTitle('Awarded Lead');
        $leadSheet->getColumnDimension('A')->setWidth(40);
        $leadSheet->getColumnDimension('B')->setWidth(15);
        $leadSheet->getColumnDimension('C')->setWidth(15);
        $leadSheet->getColumnDimension('D')->setWidth(15);
        $leadSheet->getColumnDimension('E')->setWidth(20);
        $leadSheet->getColumnDimension('F')->setWidth(20);
        $leadSheet->getColumnDimension('G')->setWidth(20);

        // third sheet
        $userTotalSaleSheet = $spreadsheet->createSheet();
        $userTotalSaleSheet->setTitle('IndividualUserSale');
        $userTotalSaleSheet->getColumnDimension('A')->setWidth(20);
        $userTotalSaleSheet->getColumnDimension('B')->setWidth(10);
        
        // add headers and data for sheet 1.
        $incentiveSheet->mergeCells('A1:B1');
       
        $incentiveSheet->getStyle('A1')->applyFromArray($mergeStyle);       
        $incentiveSheet->setCellValue('A1', "Incentive Report For QTR:");
        $incentiveSheet->setCellValue('B1', $request->dateRange);
        $incentiveSheet->setCellValue('A2', "Report generated on",);
        $incentiveSheet->setCellValue('B2', date("d-M-Y"));
        $incentiveSheet->getStyle('B2')->applyFromArray($headerStyle);
        $incentiveSheet->getStyle('A1:A2')->applyFromArray($headerStyle);

        $row = 3;
        if(!blank($data)){
            foreach ($data as $k => $val) {
                foreach($val as $key =>$value){
                    $rr = $row + 1;
                    $incentiveSheet->setCellValue('A'.$rr, $key);

                    if ($key === 'Manager Name') {
                        $incentiveSheet->getStyle('A'.$rr)->applyFromArray($yellowBackgroundStyle); 
                        $incentiveSheet->getStyle('B'.$rr)->applyFromArray($yellowBackgroundStyle); 
                    }

                    if ($key === 'Actual Sales Achieved in a Qtr') {
                        $incentiveSheet->getStyle('A'.$rr)->applyFromArray($yellowBackgroundStyle); 
                        $incentiveSheet->getStyle('B'.$rr)->applyFromArray($yellowBackgroundStyle); 
                    }

                    if ($key === 'Carry Forward to Next Qtr Tgt.') {
                        $incentiveSheet->getStyle('A'.$rr)->applyFromArray($greyBackgroundStyle); 
                        $incentiveSheet->getStyle('B'.$rr)->applyFromArray($greyBackgroundStyle); 
                    }

                    if ($key === 'BDM Incentive') {
                        $incentiveSheet->getStyle('A'.$rr)->applyFromArray($boldAlignStyle); 
                        $incentiveSheet->getStyle('B'.$rr)->applyFromArray($boldAlignStyle);
                        
                    }

                    if ($key === 'BDE Team Incentive') {
                        $incentiveSheet->getStyle('A'.$rr)->applyFromArray($boldAlignStyle); 
                        $incentiveSheet->getStyle('B'.$rr)->applyFromArray($boldAlignStyle);
                        $row = $row +1;
                    }

                    if ($key === 'Lead Generation Additional Incentive:-') {
                        $incentiveSheet->getStyle('A'.$rr)->applyFromArray($boldStyle); 
                        $incentiveSheet->getStyle('B'.$rr)->applyFromArray($boldStyle);    
                    }

                    if ($key === 'Quarterly Team Incentive (Rs.)') {
                        $incentiveSheet->getStyle('A'.$rr)->applyFromArray($greenBackgroundStyle); 
                        $incentiveSheet->getStyle('B'.$rr)->applyFromArray($greenBackgroundStyle); 
                    } else {
                        $incentiveSheet->setCellValue('B'.$rr, $value);
                        $incentiveSheet->getStyle('A'.$rr); // Apply header style
                    }
                    $row++;
                }
                $row = $row + 2;
            }
        }

        $incentiveSheet->getStyle('B1:B'.$row)->applyFromArray($dataStyle); // Apply style to sheet1 data

        // Add headers for sheet 2
        $headers = ['ProjectName (Client Name)', 'Bid Date', 'Total Amount','Incentive Amount', 'Bid Owner', 'Lead Closure','Is Invited'];
        $leadSheet->fromArray([$headers], null, 'A1');
        $leadSheet->getStyle('A1:G1')->applyFromArray($leadAwardStyle); // Apply style to sheet header
       

        // Add headers for sheet 3
        $headers = ['UserName', 'Total Sale'];
        $userTotalSaleSheet->fromArray([$headers], null, 'A1');
        $userTotalSaleSheet->getStyle('A1:B1')->applyFromArray($leadAwardStyle); // Apply style to sheet header

        // Add data to sheet1
        $row = 2;
        if(!blank($leads)){
            foreach ($leads as $item) {
                $leadSheet->setCellValue('A' . $row, $item->job_title ? ($item->job_title."(".($item->client ? $item->client->client_name : 'client not found').")") : '');
                
                $leadSheet->setCellValue('B' . $row, $item->bid_date ?? '');
               
                $sum = 0;
                if(!blank($item->budgets)){
                    foreach($item->budgets as $budget){
                        $sum = $sum + $budget['budget'];
                    }
                }

                $leadSheet->setCellValue('C' . $row, $sum);
                
                if ($item->project_type == "fixed"){
                    $leadSheet->setCellValue('D' . $row, $item->client_budget);
                    // $this->total_incentive = $this->total_incentive + $item->client_budget;
                }
                                     
                else if($item->project_type == "hourly"){
                    $leadSheet->setCellValue('D' . $row, $sum);
                    // $this->total_incentive = $this->total_incentive + $sum;
                }
                
                $leadSheet->setCellValue('E' . $row, $item->admin ? $item->admin->name : '');
                $leadSheet->setCellValue('F' . $row, $item->admin ? ($item->admin->createdby ? $item->admin->createdby->name : '') : '');
                $leadSheet->setCellValue('G' . $row, $item->is_invited ? 'Yes' : 'No');
                $row++;
            }
        }
        
        // Add data to sheet2
        $row = 2;
        if(count($user) > 0){
            foreach ($user as $item) {
                $userTotalSaleSheet->setCellValue('A' . $row, $item['user']);
                $userTotalSaleSheet->setCellValue('B' . $row, $item['incentive']);
                $row++;
            }
        }

        // Set Sheet 1 as active sheet
        $spreadsheet->setActiveSheetIndex(0);

        // Create a new Excel writer object
        $writer = new Xlsx($spreadsheet);

        // Set headers
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        header('Cache-Control: max-age=0');

        // Save the spreadsheet to a file
        $writer->save('php://output');
    }

    public function budget(Request $request){
        $lead_id = $request->id;
        return view('admin.report.add-budget',compact('lead_id'));
    }

    public function storeBudget(Request $request){

        $validator = Validator::make($request->all(),[
            'budget_date' => 'required',
            'budget' => 'required',
        ]);

        if($validator->passes()){
            BudgetHistory::create([
                'added_at' => $request->budget_date,
                'budget'=> $request->budget,
                'admin_id'=> admin()->user()->id,
                'lead_id'=> $request->leadId,
                'is_test'=> admin()->user()->is_test,
            ]);
            session()->flash('success','Budget Added !!');
            return response()->json([
                'status'=>true,
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'error'=> $validator->errors(),
            ]);
        }
    }

    public function editBudget(Request $request){
        $budget_id = $request->id;
        $budget = BudgetHistory::findOrFail($budget_id);
        return view('admin.report.edit-budget',compact('budget'));
    }

    public function updateBudget(Request $request){
        $validator = Validator::make($request->all(),[
            'budget' => ['required','numeric'],
        ]);

        if($validator->passes()){
            BudgetHistory::findOrFail($request->budgetId)->update([
                'budget'=> $request->budget,
            ]);
            return response()->json([
                'status'=>true,
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'error'=> $validator->errors(),
            ]);
        }
    }

    public function clientUpdateBudget(Request $request){
        $validator = Validator::make($request->all(),[
            'budget' => ['required','numeric'],
        ]);

        if($validator->passes()){
            $lead = Lead::findOrFail($request->leadId);
            $lead->update([
                'client_budget' => $request->budget,
            ]);

            if ($lead->bid_id) {
                $bid = Bid::findOrFail($lead->bid_id);
                $bid->update([
                    'client_budget' => $request->budget,
                ]);
            }

            // BudgetHistory::create([
            //     'added_at' => Carbon::now()->toDateTimeString(),
            //     'budget'=> $request->budget,
            //     'admin_id'=> admin()->user()->id,
            //     'lead_id'=> $request->leadId,
            //     'is_test'=> admin()->user()->is_test,
            // ]);

            session()->flash('success','Budget Added !!');
            
            return response()->json([
                'status'=>true,
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'error'=> $validator->errors(),
            ]);
        }
    }

    public function destroyBudget(Request $request){
        $data = BudgetHistory::findOrFail($request->id);
        $data->delete();
        return response()->json([
            'status'=>true,
        ]);
    }

    public function updateProjectType(Request $request) {
        $id = $request->id;
        $project_type = $request->project_type;
       
        $lead = Lead::find($id);
        if (blank($lead)) {
            return response()->json([
                'success' => false,
                'message' => 'Lead not found',
            ], 404);
        }
    
        $lead->update(['project_type' => $request->project_type]);
    
        if (!empty($lead->bid_id)) {
            $bid = Bid::find($lead->bid_id);
            if(!blank($bid)){
                $bid->update(['project_type' => $project_type]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Project type updated successfully',
        ]);
    }
}
