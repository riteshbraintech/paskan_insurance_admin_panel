<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function markAsRead(Request $request)
    {
        $id = $request->id;
        $notification = Notification::find($id);
        if($notification) $notification->markAsRead();
        return response()->json(['status'=>'ok']);
    }

}
