<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $message = $request->session()->get('message');     // after login/logut message
        $request->session()->forget('message');
        // dd($message);
        return view('admin.auth.login')->with('logmessage', $message);
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\AdminLoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(AdminLoginRequest $request)
    {
        // dd($request);
        $request->authenticate($request);
        
        $user = Auth::guard('admin')->user();

        if ($user->status !== 'active') {
            // User is not active, log them out and throw validation exception
            Auth::guard('admin')->logout();

            throw ValidationException::withMessages([
                'email' => trans('account inactive or blocked'),
            ]);
        }

        $request->session()->regenerate();

        // dd(current_restaurant_list());



        // update firebase token while login
        // if (!empty($request->firebase_token)) {
        //     admin()->user()->firebase_token = $request->firebase_token;
        //     admin()->user()->save();
        // }

        return redirect(RouteServiceProvider::ADMINHOME);
        // return redirect()->intended(RouteServiceProvider::ADMINHOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        // set logout message
        $request->session()->put('message', 'Successfully logout !');

        return redirect(RouteServiceProvider::ADMINHOME);
    }
}
