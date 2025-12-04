<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/clear-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('route:cache');
    Artisan::call('view:clear');
    Artisan::call('view:cache');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('optimize:clear');
    
    return '<h1>Cache facade value cleared</h1>';
});

// Route::get('/', function () {
//     return view('welcome');
// });

Route::redirect('/', 'admin/dashboard');
// Route::get('/firebase-messaging-sw.js',[HomeController::class, 'firebase_messaging']);


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

// web auth route
// require __DIR__.'/auth.php';

// all admin route
require __DIR__.'/admin.php';



Broadcast::routes([
    'prefix' => 'admin',                // optional, for /admin/broadcasting/auth
    'middleware' => ['auth:admin']      // use your admin guard
]);


