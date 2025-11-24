<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        
        $current_page = $request->page ?? 1;
        $search = $request->search ?? "";
        $perPage = $request->perPage ?? 10;
        $isAjax = $request->ajax(); // better way
        
        // Remove translation relations
        $records = Contact::query();

        // Simple search on contact table only
        $records = $records->where(function ($q) use ($search) {
            $q->where('fullname', 'like', "%$search%")
            ->orWhere('email', 'like', "%$search%")
            ->orWhere('phonenumber', 'like', "%$search%")
            ->orWhere('subject', 'like', "%$search%");
        });


       $records = $records->orderby('id', 'desc')->paginate($perPage);
        // Handle AJAX (table reload only)
        if ($isAjax) {
            $html = view('admin.contact.table', compact('records'))->render();
            return response()->json(['html' => $html]);
        }

        // Page load
        return view('admin.contact.index', compact('records'));
    }

    public function delete($id)
    {
        $contact = Contact::findOrFail($id);

        // Delete record
        $contact->delete();

        return redirect()->route('admin.contact.index')->with('success', 'Contact deleted successfully.');
    }

    public function view($id)
    {
        // Load only the main record (NO translations)
        $record = Contact::findOrFail($id);

        // Get all categories (NO translations)
        $contact = Contact::all();

        return view('admin.contact.view', compact('record', 'contact'));
    }


}
