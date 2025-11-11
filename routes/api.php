<?php

use App\Http\Controllers\Api\AuthControler;
use App\Http\Controllers\Api\CategoryControler;
use App\Http\Controllers\Api\CategoryFormControler;
use App\Http\Controllers\Api\CMSPageControler;
use App\Http\Controllers\Api\ProfileControler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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
Route::get('/cms-page',[CMSPageControler::class,'index']);
Route::get('/cms-page/{slug}',[CMSPageControler::class,'cmspagedetail']);


Route::get('/category-list',[CategoryControler::class,'index']);
Route::get('/category-list/{slug}',[CategoryControler::class,'categorydetail']);

Route::get('/categoryformfield-list',[CategoryFormControler::class,'index']);
Route::get('/categoryformfield-list/{label}',[CategoryFormControler::class,'categoryformfieldlist']);


Route::get('/categoryformfields/{category_id}',[CategoryFormControler::class,'categoryfields']);

Route::post('/register', [AuthControler::class, 'register']);
Route::post('/login', [AuthControler::class, 'login']);


// authentic routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout',[AuthControler::class,'logout']);
    Route::get('/profile',[ProfileControler::class,'profile']);
    Route::post('/updateprofile',[ProfileControler::class,'updateProfile']);
    
});