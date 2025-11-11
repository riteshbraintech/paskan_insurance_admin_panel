<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\BidController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\PortalController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TechnologyController;
use App\Http\Controllers\Admin\CMSController;

Route::prefix('/admin')->group(function () {
    //super admin global
    Route::redirect('/', 'admin/dashboard');

    Route::middleware(['auth.admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        
        // --- Admin Routes for CMS Pages (Protected) ---
        Route::resource('cmspage', CMSController::class)->names('admin.cmspage');
        Route::post('status/{id?}', [CMSController::class, 'changeStatus'])->name('admin.cmspage.change.status');



        Route::get('sessionMode', [DashboardController::class, 'sessionMode'])->name('admin.session.mode');
        Route::get('refresh-target-data', [DashboardController::class, 'refreshTargetData'])->name('admin.dashboard.refresh-target-data');
        
        Route::get('profile', [ProfileController::class, 'profile'])->name('admin.profile');
        Route::post('updateProfile', [ProfileController::class, 'updateProfile'])->name('admin.updateProfile');
        Route::post('updatePassword', [ProfileController::class, 'updatePassword'])->name('admin.updatePassword');

        Route::group(['prefix' => 'role'], function () {
            Route::get('list', [RoleController::class, 'index'])->name('admin.role.list');
            Route::get('create', [RoleController::class, 'create'])->name('admin.role.create');
            Route::post('store', [RoleController::class, 'store'])->name('admin.role.store');
            Route::get('edit/{id?}', [RoleController::class, 'edit'])->name('admin.role.edit');
            Route::post('update/{id?}', [RoleController::class, 'update'])->name('admin.role.update');
            Route::get('delete/{id?}', [RoleController::class, 'destroy'])->name('admin.role.delete');
        });

        // staff route
        Route::group(['prefix' => 'staff'], function () {
            Route::get('staff-list',[StaffController::class,'staffList'])->name('admin.staff.staff-list');
            Route::get('list', [StaffController::class, 'index'])->name('admin.staff.list');
            Route::get('create', [StaffController::class, 'create'])->name('admin.staff.create');
            Route::post('store', [StaffController::class, 'store'])->name('admin.staff.store');
            Route::get('edit/{id?}', [StaffController::class, 'edit'])->name('admin.staff.edit');
            Route::post('update/{id?}', [StaffController::class, 'update'])->name('admin.staff.update');
            Route::post('status/{id?}', [StaffController::class, 'changeStatus'])->name('admin.staff.change.status');
            Route::get('delete/{id?}', [StaffController::class, 'destroy'])->name('admin.staff.delete');
        });

        // bid route
        Route::group(['prefix' => 'bid'], function () {
            Route::get('list', [BidController::class, 'index'])->name('admin.bid.list');
            Route::get('create', [BidController::class, 'create'])->name('admin.bid.create');
            Route::post('store', [BidController::class, 'store'])->name('admin.bid.store');
            Route::get('edit/{id?}', [BidController::class, 'edit'])->name('admin.bid.edit');
            Route::post('update/{id?}', [BidController::class, 'update'])->name('admin.bid.update');
            Route::post('status/{id?}', [BidController::class, 'changeStatus'])->name('admin.bid.change.status');
            Route::get('delete/{id?}', [BidController::class, 'destroy'])->name('admin.bid.delete');
            Route::get('view/{id?}', [BidController::class, 'view'])->name('admin.bid.view');
        });

        // lead route
        Route::group(['prefix' => 'lead'], function () {
            Route::get('list', [LeadController::class, 'index'])->name('admin.lead.list');
            Route::get('create', [LeadController::class, 'create'])->name('admin.lead.create');
            Route::post('store', [LeadController::class, 'store'])->name('admin.lead.store');
            Route::get('edit/{id?}', [LeadController::class, 'edit'])->name('admin.lead.edit');
            Route::post('update/{id?}', [LeadController::class, 'update'])->name('admin.lead.update');
            Route::get('delete/{id?}', [LeadController::class, 'destroy'])->name('admin.lead.delete');
            Route::get('view/{id?}', [LeadController::class, 'view'])->name('admin.lead.view');
            Route::get('view-only/{id?}', [LeadController::class, 'viewOnly'])->name('admin.lead.viewOnly');
            Route::get('log/{id?}', [LeadController::class, 'log'])->name('admin.lead.log');
            Route::post('status-update', [LeadController::class, 'statusUpdate'])->name('admin.lead.status-update');
            Route::get('client-edit/{id?}', [LeadController::class, 'clientEdit'])->name('admin.lead.client-edit');
            Route::post('view-form-update/{id?}', [LeadController::class, 'viewFormUpdate'])->name('admin.lead.view-form-update');
            Route::get('list-per-date', [LeadController::class, 'showLeadListPerDate'])->name('admin.lead.list-per-date');
            Route::get('export-csv', [LeadController::class, 'exportCSV'])->name('admin.lead.export-csv');
            Route::post('update-job', [LeadController::class,'updateJob'])->name('admin.lead.update-job');
            Route::get('clone-lead/{id?}', [LeadController::class,'cloneThisLead'])->name('admin.lead.clone');
            
        });

        // client route
        Route::group(['prefix'=>'client'],function(){
            Route::get('detail',[ClientController::class,'clientDetail'])->name('admin.client.detail');
            Route::get('list', [ClientController::class, 'index'])->name('admin.client.list');
            Route::get('create', [ClientController::class, 'create'])->name('admin.client.create');
            Route::post('store', [ClientController::class, 'store'])->name('admin.client.store');
            Route::get('edit/{id?}', [ClientController::class, 'edit'])->name('admin.client.edit');
            Route::post('update/{id?}', [ClientController::class, 'update'])->name('admin.client.update');
            Route::get('delete/{id?}', [ClientController::class, 'destroy'])->name('admin.client.delete');
            Route::get('merge', [ClientController::class, 'merge'])->name('admin.client.merge');
            Route::post('storeMerge', [ClientController::class, 'storeMerge'])->name('admin.client.storeMerge');
            Route::get('client-bids-list/{id?}', [ClientController::class, 'clientBidsList'])->name('admin.client.client-bids-list');
        });

        // Portal route
        Route::group(['prefix'=>'portal'],function(){
            Route::get('create', [PortalController::class, 'create'])->name('admin.portal.create');
            Route::post('store', [PortalController::class, 'store'])->name('admin.portal.store');
            Route::get('get-slug',[PortalController::class, 'getSlug'])->name('admin.portal.name-slug');
        });

        // Technology route
        Route::group(['prefix'=>'technology'],function(){
            Route::get('create', [TechnologyController::class, 'create'])->name('admin.technology.create');
            Route::post('store', [TechnologyController::class, 'store'])->name('admin.technology.store');
            Route::get('get-slug',[TechnologyController::class, 'getSlug'])->name('admin.technology.name-slug');
        });

        // log route
        Route::group(['prefix'=>'log'],function(){
            Route::get('list', [LogController::class, 'index'])->name('admin.log.list');
        });

        // report route
        Route::group(['prefix'=>'report'],function(){
            Route::get('list', [ReportController::class, 'index'])->name('admin.report.list');
            Route::get('export-incentive-excel', [ReportController::class, 'exportIncentiveExcel'])->name('admin.report.export-incentive-excel');
            Route::get('budget/{id?}', [ReportController::class, 'budget'])->name('admin.report.add-budget');
            Route::post('add-budget/{id?}', [ReportController::class, 'storeBudget'])->name('admin.report.store-budget');
            Route::get('edit-budget/{id?}', [ReportController::class, 'editBudget'])->name('admin.report.edit-budget');
            Route::post('update-budget/{id?}', [ReportController::class, 'updateBudget'])->name('admin.report.update-budget');
            Route::get('delete-budget/{id?}', [ReportController::class, 'destroyBudget'])->name('admin.report.delete-budget');
            Route::post('update-project_type', [ReportController::class, 'updateProjectType'])->name('admin.report.update.project_type');
            Route::post('update-client_budget/{id?}', [ReportController::class, 'clientUpdateBudget'])->name('admin.report.update-client_budget');
        });

        // project route
        Route::group(['prefix'=>'project'],function(){
            Route::get('/list',[ProjectController::class,'index'])->name('admin.project.index');
            Route::get('create', [ProjectController::class, 'create'])->name('admin.project.create');
            Route::post('store', [ProjectController::class, 'store'])->name('admin.project.store');
            Route::get('edit/{id?}', [ProjectController::class, 'edit'])->name('admin.project.edit');
            Route::post('update/{id?}', [ProjectController::class, 'update'])->name('admin.project.update');
            Route::get('delete/{id?}', [ProjectController::class, 'destroy'])->name('admin.project.delete');
        });


        // Categories route
        Route::group(['prefix'=>'categories'],function(){
            Route::get('/list',[CategoryController::class,'index'])->name('admin.categories.index');
            Route::get('create', [CategoryController::class, 'create'])->name('admin.categories.create');
            Route::post('store', [CategoryController::class, 'store'])->name('admin.categories.store');
            Route::post('status/{id?}', [CategoryController::class, 'changeStatus'])->name('admin.categories.change.status');
            Route::get('edit/{id?}', [CategoryController::class, 'edit'])->name('admin.categories.edit');
            Route::post('update/{id?}', [CategoryController::class, 'update'])->name('admin.categories.update');
            Route::get('delete/{id?}', [CategoryController::class, 'delete'])->name('admin.categories.delete');
            Route::get('view/{id?}', [CategoryController::class, 'view'])->name('admin.categories.view');

        });



    });

    require __DIR__ . '/admin-auth.php';
});
