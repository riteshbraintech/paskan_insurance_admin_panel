<?php

use App\Models\Bid;
use App\Models\Lead;
use Illuminate\Support\Facades\Auth;
use App\Models\Languages;

// used: for loading public assets for admin in future if we want change path
if (!function_exists('loadAssets')) {
    function loadAssets($dir)
    {
        return url('public/admin/' . $dir);
    }
}

// used: for call Auth::gurad('admin') once so we can easily make changes in gurad in future
if (!function_exists('admin')) {
    function admin()
    {
        return Auth::guard('admin');
    }
}

if (!function_exists('getImagePath')) {
    function getImagePath($name, $type, $size = '')
    {
        $imgPath = '';
        switch ($type) {
            case 'profile':
                $imgPath = loadAssets('upload/profile');
                break;
            case 'logo':
                $imgPath = loadAssets('images');
                break;
            case 'staff':
                $imgPath = loadAssets('upload/staff');
                break;
            default:
                break;
        }

        if (!empty($size)) {
            $imgPath =  $imgPath . '/' . $size . '/' . $name;
        } else {
            $imgPath = $imgPath . '/' . $name;
        }

        // if image not store
        if (empty($name) || $name == null)    
            return loadAssets('image/no-image.png');
        return $imgPath;

        if (file_exists($imgPath))
            return $imgPath;
        else
            return loadAssets('image/no-image.png');
    }
}

if (!function_exists('statusList')) {
    function statusList()
    {
        return ['open'=>'Open','replied'=>'Replied','followup'=>'Follow UP','hot_lead'=>'Hot Lead','dead_lead'=>'Dead Lead',
        'fake_lead'=>'Fake Lead','awarded'=>'Awarded','cancelled'=>'Cancelled','invite'=>'Invite'];
    }
}

if (!function_exists('checkBidJobLink')) {
    function checkBidJobLink($link,$id)
    {
        $bids = Bid::get();
        if($id != ""){
            $bids = Bid::where('id','<>',$id)->get();
        }
        
        if (str_contains($link, "upwork") || str_contains($link, "guru")) {
            $number = explode("/",$link);
            $num = $number[sizeof($number) - 1];
            if(str_contains($num, "?")){
                $num = explode("?",$num)[0];
            }
            
            foreach($bids as $bid){
                $bid_number = explode("/",$bid->job_link);
                $bid_num = $bid_number[sizeof($bid_number) - 1];
                if(str_contains($bid_num, "?")){
                    $bid_num = explode("?",$bid_num)[0];
                }

                if($bid_num == $num){
                    return false;
                }
            }
            return true;
        }
        return true;
    }
}

if (!function_exists('checkLeadJobLink')) {
    function checkLeadJobLink($link,$id)
    {
        $leads = Lead::get();
        if($id != ""){
            $leads = Lead::where('id','<>',$id)->get();
        }
        
        if (str_contains($link, "upwork") || str_contains($link, "guru")) {
            $number = explode("/",$link);
            $num = $number[sizeof($number) - 1];
            if(str_contains($num, "?")){
                $num = explode("?",$num)[0];
            }

            foreach($leads as $lead){
                $lead_number = explode("/",$lead->job_link);
                $lead_num = $lead_number[sizeof($lead_number) - 1];
                if(str_contains($lead_num, "?")){
                    $lead_num = explode("?",$lead_num)[0];
                }

                if($lead_num == $num){
                    return false;
                }
            }
            return true;
        }
        return true;
    }
}

if(!function_exists('getDates')){
    function getDates($start_date,$end_date,$graph_filter){

        $array = []; 
        $start = strtotime($start_date); 
        $end = strtotime($end_date); 

        // 86400 sec = 24 hrs = 60*60*24 = 1 day
        if($graph_filter == "daily"){
            for ($currentDate = $start; $currentDate <= $end; $currentDate += (86400)) {                                     
                $store = date('d-m-Y', $currentDate); 
                $array[] = $store; 
            }
        }

        if($graph_filter == "weekly"){
            $new['first'] =  date ('Y-m-d ', $start) ;
            $new['last'] = date ('Y-m-d 23:59', $end) ;
            
            $interval = new DateInterval('P1D');
            $dateRange = new DatePeriod(new DateTime($new['first']), $interval,new DateTime($new['last']));

            $weekNumber = 1;
            foreach ($dateRange as $date) {
                $array[$weekNumber][] = $date->format('d-m-Y');
                if ($date->format('N') == 7) {
                    $weekNumber++;
                }
            }
        }

        if($graph_filter == "monthly"){
            $key = 0;
            while (strtotime($start_date) <= strtotime($end_date)){
                $array[$key] = ['month' => date('M',strtotime($start_date)),'year' => date('Y',strtotime($start_date))];
                $start_date = date('01 M Y', strtotime($start_date . '+ 1 month'));
                $key++;
            }
        }
        return $array; 
    }
}

if(!function_exists('getBidsLeadsPerMonth')){
    function getBidsLeadsPerMonth($bid,$month_dates,$graph_filter){
        $list = $bids = $converted_bids = [];

        foreach($bid as $data){
            $date = date('d-m-Y',strtotime($data['created_at']));
            
            if (!isset($list[$date])) {
                $list[$date] = ['bids' => 0, 'converted_bids_count' => 0];
            }

            $list[$date]['bids']++;
            if ($data['is_lead_converted']) {
                $list[$date]['converted_bids_count']++;
            }
        }

        if($graph_filter == "daily"){
            foreach($month_dates as $key1 => $date){
                $bids[$key1] = $list[$date]['bids'] ?? 0;
                $converted_bids[$key1] = $list[$date]['converted_bids_count'] ?? 0;  
            }
        }
        
        if($graph_filter == "weekly"){
            $index = 0;
            foreach($month_dates as $month_date){
                $bids[$index] = 0;
                $converted_bids[$index] = 0;
                foreach($month_date as $date){
                    $bids[$index] += $list[$date]['bids'] ?? 0;
                    $converted_bids[$index] += $list[$date]['converted_bids_count'] ?? 0;
                }
                $index++;
            }
        }

        if($graph_filter == "monthly"){
            $index = 0;
            foreach($month_dates as $month){
                $bids[$index] = 0;
                $converted_bids[$index] = 0;
                foreach($list as $date => $data){
                    if($month['month'] == date('M',strtotime($date)) && $month['year'] == date('Y',strtotime($date))){
                        $bids[$index] += $data['bids'];
                        $converted_bids[$index] += $data['converted_bids_count'];
                    }
                }
                $index++;
            }
        }
       
        $data= [];
        $data['total_bids'] = $bids;
        $data['total_converted_bids'] = $converted_bids;

        return $data;
    }
}

if(!function_exists('getAwardedPerMonth')){
    function getAwardedPerMonth($lead,$month_dates,$graph_filter){
        $lead = $lead->where('status','awarded');
        
        $awarded_per_day = [];
        foreach($lead as $data){
            $date = date('d-m-Y',strtotime($data['created_at']));
            $awarded_per_day[$date] = ($awarded_per_day[$date] ?? 0) + 1; 
        }

        $awarded = [];
        if($graph_filter == "daily"){
            foreach($month_dates as $key1 => $date){
                $awarded[$key1] = $awarded_per_day[$date] ?? 0;
            }
        }
        
        if($graph_filter == "weekly"){
            $index = 0;
            foreach($month_dates as $month_date){
                $awarded[$index] = 0;
                foreach($month_date as $date){
                    $awarded[$index] += $awarded_per_day[$date] ?? 0;
                }
                $index++;
            }
        }
        
        if($graph_filter == "monthly"){
            $index = 0;
            foreach($month_dates as $month){
                $awarded[$index] = 0;
                foreach($awarded_per_day as $date => $count){
                    if($month['month'] == date('M',strtotime($date)) && $month['year'] == date('Y',strtotime($date))){
                        $awarded[$index] += $count;
                    }
                }
                $index++;
            }
        }
        return $awarded;
    }
}

if(!function_exists('profiles')){
    function profiles(){
        $profiles = ['anurag' => 'ANURAG','ashish'=>'ASHISH','bt'=>'BT','jai prakash'=>'JAI PRAKASH','navita'=>'NAVITA','pradeep'=>'PRADEEP','vineet'=>'VINEET','vipin'=>'VIPIN','vishal'=>'VISHAL'];
        return $profiles;
    }
}

if(!function_exists('langueses')){
    function langueses(){
        return Languages::select('code', 'name')->orderBy('is_default','desc')->get()->pluck('name','code')->toArray();
    }
}


// create a function to group all language with comma seperate
if(!function_exists('getAllLanguages')){
    function getAllLanguageText(){
        $languages = langueses();
        return strtolower(implode(', ',array_values($languages)));
    }
}