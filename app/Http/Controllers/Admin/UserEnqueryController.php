<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserEnquery;
use Illuminate\Http\Request;

class UserEnqueryController extends Controller
{
    public function index(Request $request)
    {
        
        $current_page = $request->page ?? 1;
        $search = $request->search ?? "";
        $perPage = $request->perPage ?? 10;
        $isAjax = $request->ajax(); // better way
        
        // Remove translation relations
        // $records = UserEnquery::query();
        $records=UserEnquery::with('fillups');

   
       $records = $records->orderby('id', 'desc')->paginate($perPage);
        // Handle AJAX (table reload only)
        if ($isAjax) {
            $html = view('admin.user_enquery.table', compact('records'))->render();
            return response()->json(['html' => $html]);
        }

        // Page load
        return view('admin.user_enquery.index', compact('records'));
    }


    public function view($id)
    {
        $record = UserEnquery::with(['fillups', 'user', 'category'])->findOrFail($id);

        return view('admin.user_enquery.view', compact('record'));
    }


    public function updateStatus(Request $request, $id)
    {
        $item = UserEnquery::findOrFail($id);
        $item->status = $request->status;
        $item->save();

        return back()->with('success', 'Status updated successfully');
    }

}
