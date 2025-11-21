<?php

use App\Models\UserLog;

function logActivity($action, $page = null, $description = null,$ip =null)
{
   
    $user = auth('sanctum')->user();
     
    UserLog::create([
        
        'user_id'    => $user?->id,
        'action'     => $action,
        'page'       => $page,
        'description'=> $description,
        'ip_address' => $ip,        
        'user_agent' => request()->userAgent(),
        'browser'    => getBrowserName(request()->userAgent()),
    ]);
}


if (!function_exists('getBrowserName')) {
    function getBrowserName($userAgent)
    {
        if (!$userAgent) return 'Unknown';

        $userAgent = strtolower($userAgent);

        if (strpos($userAgent, 'edg') !== false) return 'Microsoft Edge';
        if (strpos($userAgent, 'opr') !== false || strpos($userAgent, 'opera') !== false) return 'Opera';
        if (strpos($userAgent, 'chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'safari') !== false) return 'Safari';
        if (strpos($userAgent, 'firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'msie') !== false || strpos($userAgent, 'trident') !== false) return 'Internet Explorer';

        if (strpos($userAgent, 'postman') !== false) return 'Postman';
        if (strpos($userAgent, 'insomnia') !== false) return 'Insomnia';
        if (strpos($userAgent, 'okhttp') !== false) return 'Android App';
        if (strpos($userAgent, 'axios') !== false) return 'Axios Client';

        return 'Unknown';
    }
}


