<?php
// response helper  
if (!function_exists('JsonResponse')) {
    function JsonResponse($status = true, $data = [], $msg = '', $rescode = 200)
    {
        $response = [];
        $response['status'] = $status;
        $response['data'] = empty($data) ? null : $data;
        $response['message'] = $msg;
        return response()->json($response, $rescode);
    }
}

if (!function_exists('transLang')) {
    function transLang($template = null, $dataArr = [])
    {
        return $template ? trans("messages.{$template}", $dataArr) : '';
    }
}

// error log generate
if (!function_exists('LogError')) {
    function LogError($e)
    {
        \Log::critical($e->getMessage());
    }
}
