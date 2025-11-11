<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryControler;
use App\Http\Controllers\Api\V1\CMSPageControler;
use App\Http\Controllers\Api\V1\ProfileControler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\HomeController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Semi public routes
Route::prefix('v1')->middleware(['api'])->group(function () {

    // public routes here
    Route::get('home', [HomeController::class, 'home']);
    Route::get('header-menu', [HomeController::class, 'headerMenu']);
    
    // global file upload route
    Route::post('uploadSingleFiles', [HomeController::class, 'uploadSingleFiles']);
    Route::post('uploadMultipleFiles', [HomeController::class, 'uploadMultipleFiles']);

    // get CMS page list and detail
    Route::get('pages/{slug}', [HomeController::class, 'getCMSPage']);
    

    
    Route::get('/cms-page',[CMSPageControler::class,'index']);
    Route::get('/cms-page/{slug}',[CMSPageControler::class,'cmspagedetail']);
    
    Route::get('/category-list',[CategoryControler::class,'index']);
    Route::get('/category-list/{slug}',[CategoryControler::class,'categorydetail']);


    // create a prefix for auth routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
    });

    // protected routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/logout',[AuthController::class,'logout']);
        Route::get('profile',[ProfileControler::class,'profile']);
        Route::post('/updateprofile',[ProfileControler::class,'updateProfile']);
    });

});






