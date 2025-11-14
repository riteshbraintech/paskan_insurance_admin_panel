<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Models\Admin;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminResetPassword;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     *
     * @return \Illuminate\View\View
     */

    protected function guard()
    {
        return Auth::guard('admin');
    }

    public function create()
    {
        return view('admin.auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        // dd(Password::RESET_LINK_SENT);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
       
        try {
                $res = Admin::where('email', $request->email)->first();
                // dd($res);
                if(!empty($res)){
                    $link =  url('admin/reset-password/'. base64_encode($res->id) . '?email='. $request->email);
                    Mail::to($request->email)->send(new AdminResetPassword($link));
                    return redirect()->route('reset-link-sent')->with('email', $request->email);
                }else
                    return back()->withInput($request->only('email'))->withErrors(['email' => __('Invalid email')]);
                    
        } catch (\Throwable $th) {
            // dd($th);
                \Log::critical($th->getMessage());
                return back()->withInput($request->only('email'))->withErrors(['email' => __('Some issue found')]);
        }
       
        
       
    }
    public function store_old(Request $request)
    {
        
        // dd(Password::RESET_LINK_SENT);

        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );
        // dd(auth());

        if($status == Password::RESET_LINK_SENT)
            return redirect()->route('reset-link-sent')->with('email', $request->email);
        else
            return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
        
       
    }
    public function resetLinkSent(Request $request){

        $email = $request->session()->get('email');
        if(empty($email))   return redirect('admin/login');     // only flash info
        return view('admin.auth.link-sent', compact('email'));
    }
}
