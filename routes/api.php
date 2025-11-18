<?php

use App\Http\Controllers\Api\CategoryFormControler;
use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryControler;
use App\Http\Controllers\Api\V1\CMSPageControler;
use App\Http\Controllers\Api\V1\FAQController;
use App\Http\Controllers\Api\V1\ProfileControler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\UserInsuranceFillupController;

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
Route::get('/categoryformfield-list',[CategoryFormControler::class,'index']);
Route::get('/categoryformfield-list/{label}',[CategoryFormControler::class,'categoryformfieldlist']);


Route::get('/categoryformfields/{category_id}',[CategoryFormControler::class,'categoryfields']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// Semi public routes
Route::prefix('v1')->middleware(['api'])->group(function () {

    // public routes here
    Route::get('home', [HomeController::class, 'home']);
    Route::get('header-menu', [HomeController::class, 'headerMenu']);
    Route::get('category/fields/{slug}',[HomeController::class,'categoryfield']);


    //get FAQ List
    Route::get('faq',[FAQController::class,'faqlist']);

    //get Article List
    Route::get('article',[ArticleController::class,'articlelist']);

    //get Banner Data
    Route::get('banner',[HomeController::class,'banner']);

    
    //get form field image with option
    Route::get('optionimage/{id}',[HomeController::class,'show']);
    
    // global file upload route
    Route::post('uploadSingleFiles', [HomeController::class, 'uploadSingleFiles']);
    Route::post('uploadMultipleFiles', [HomeController::class, 'uploadMultipleFiles']);

    // get CMS page list and detail
    Route::get('pages/{slug}', [HomeController::class, 'getCMSPage']);
    

    
    Route::get('/cms-page',[CMSPageControler::class,'index']);
    Route::get('/cms-page/{slug}',[CMSPageControler::class,'cmspagedetail']);


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






