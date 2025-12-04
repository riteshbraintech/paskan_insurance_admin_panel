<?php

use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\BannerController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\BidController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CategoryFormFieldController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\PortalController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\TechnologyController;
use App\Http\Controllers\Admin\CMSController;
use App\Http\Controllers\Admin\ContactController;
use App\Http\Controllers\Admin\FAQController;
use App\Http\Controllers\Admin\InsuranceClaimController;
use App\Http\Controllers\Admin\InsuranceController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserEnqueryController;
use App\Http\Controllers\Admin\UserInsuranceFillupController;

Route::prefix('/admin')->group(function () {
    //super admin global
    Route::redirect('/', 'admin/dashboard');

    Route::middleware(['auth.admin'])->group(function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

        
        // --- Admin Routes for CMS Pages (Protected) ---
        Route::resource('cmspage', CMSController::class)->names('admin.cmspage');
        Route::get('delete/{cmspage?}', [CMSController::class, 'destroy'])->name('admin.cmspage.delete');
        Route::post('status/{id?}', [CMSController::class, 'changeStatus'])->name('admin.cmspage.change.status');



        Route::get('sessionMode', [DashboardController::class, 'sessionMode'])->name('admin.session.mode');
        Route::get('refresh-target-data', [DashboardController::class, 'refreshTargetData'])->name('admin.dashboard.refresh-target-data');
        
        Route::get('profile', [ProfileController::class, 'profile'])->name('admin.profile');
        Route::post('updateProfile', [ProfileController::class, 'updateProfile'])->name('admin.updateProfile');
        Route::get('password', [ProfileController::class, 'displaypassword'])->name('admin.password');
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
            Route::get('/',[CategoryController::class,'index'])->name('admin.categories.index');
            Route::get('create', [CategoryController::class, 'create'])->name('admin.categories.create');
            Route::post('store', [CategoryController::class, 'store'])->name('admin.categories.store');
            Route::post('status/{id?}', [CategoryController::class, 'changeStatus'])->name('admin.categories.change.status');
            Route::get('edit/{id?}', [CategoryController::class, 'edit'])->name('admin.categories.edit');
            Route::post('update/{id?}', [CategoryController::class, 'update'])->name('admin.categories.update');
            Route::get('delete/{id?}', [CategoryController::class, 'delete'])->name('admin.categories.delete');
            Route::get('view/{id?}', [CategoryController::class, 'view'])->name('admin.categories.view');
            Route::post('apilinkstatus/{id?}', [CategoryController::class, 'changeapilinkStatus'])->name('admin.categories.change.linktoapistatus');
            Route::post('reorder', [CategoryController::class, 'reorder'])->name('admin.categories.reorder');
        });

        // Category form field route
        Route::group(['prefix'=>'categoryformfield'],function(){
            Route::get('/',[CategoryFormFieldController::class,'index'])->name('admin.categoryformfield.index');
            Route::get('create', [CategoryFormFieldController::class, 'create'])->name('admin.categoryformfield.create');
            Route::post('store', [CategoryFormFieldController::class, 'store'])->name('admin.categoryformfield.store');
            Route::post('status/{id?}', [CategoryFormFieldController::class, 'changeStatus'])->name('admin.categoryformfield.change.status');
            Route::get('edit/{id?}', [CategoryFormFieldController::class, 'edit'])->name('admin.categoryformfield.edit');
            Route::post('update/{id?}', [CategoryFormFieldController::class, 'update'])->name('admin.categoryformfield.update');
            Route::get('delete/{id?}', [CategoryFormFieldController::class, 'delete'])->name('admin.categoryformfield.delete');
            Route::get('view/{id?}', [CategoryFormFieldController::class, 'view'])->name('admin.categoryformfield.view');
            Route::get('filter', [CategoryFormFieldController::class, 'filter'])->name('admin.categoryformfield.filter');
            Route::post('reorder', [CategoryFormFieldController::class, 'reorder'])->name('admin.categoryformfield.reorder');
            Route::get('view-update-options/{id}', [CategoryFormFieldController::class, 'viewOptions'])->name('admin.categoryformfield.viewOptions');
            Route::get('edit-update-options-form/{id}', [CategoryFormFieldController::class, 'editOptionsForm'])->name('admin.categoryformfield.editOptionsForm');
            Route::post('optionstore', [CategoryFormFieldController::class, 'optionstore'])->name('admin.categoryformfieldoptions.optionstore');
            Route::delete('optiondelete/{id}', [CategoryFormFieldController::class, 'deleteOption'])->name('admin.categoryformfieldoptions.optiondelete');
            Route::post('optionupdate/{id}', [CategoryFormFieldController::class, 'optionupdate'])->name('admin.categoryformfieldoptions.optionupdate');
            Route::get('optionfilter', [CategoryFormFieldController::class, 'optionfilter'])->name('admin.parentoptions.filter');
            Route::post('filterchangestatus/{id?}', [CategoryFormFieldController::class, 'filterchangeStatus'])->name('admin.categoryformfield.filterchange.status');


        });


        // User route
        Route::group(['prefix'=>'user'],function(){
            Route::get('/',[UserController::class,'index'])->name('admin.user.index');
            Route::get('create', [UserController::class, 'create'])->name('admin.user.create');
            Route::post('store', [UserController::class, 'store'])->name('admin.user.store');
            Route::post('status/{id?}', [UserController::class, 'changeStatus'])->name('admin.user.change.status');
            Route::get('edit/{id?}', [UserController::class, 'edit'])->name('admin.user.edit');
            Route::post('update/{id?}', [UserController::class, 'update'])->name('admin.user.update');
            Route::get('delete/{id?}', [UserController::class, 'delete'])->name('admin.user.delete');
            Route::get('view/{id?}', [UserController::class, 'view'])->name('admin.user.view');
            Route::get('formfieldview/{id?}', [UserController::class, 'formfieldview'])->name('admin.user.formfieldview');
        });

        //Banner Route
        Route::group(['prefix'=>'banner'],function(){
            Route::get('/',[BannerController::class,'index'])->name('admin.banner.index');
            Route::get('create', [BannerController::class, 'create'])->name('admin.banner.create');
            Route::post('store', [BannerController::class, 'store'])->name('admin.banner.store');
            Route::post('status/{id?}', [BannerController::class, 'changeStatus'])->name('admin.banner.change.status');
            Route::get('edit/{id?}', [BannerController::class, 'edit'])->name('admin.banner.edit');
            Route::post('update/{id?}', [BannerController::class, 'update'])->name('admin.banner.update');
            Route::get('delete/{id?}', [BannerController::class, 'delete'])->name('admin.banner.delete');
            Route::get('view/{id?}', [BannerController::class, 'view'])->name('admin.banner.view');
        });

        //FAQ Route
        Route::group(['prefix'=>'faq'],function(){
            Route::get('/',[FAQController::class,'index'])->name('admin.faq.index');
            Route::get('create', [FAQController::class, 'create'])->name('admin.faq.create');
            Route::post('store', [FAQController::class, 'store'])->name('admin.faq.store');
            Route::post('status/{id?}', [FAQController::class, 'changeStatus'])->name('admin.faq.change.status');
            Route::get('edit/{id?}', [FAQController::class, 'edit'])->name('admin.faq.edit');
            Route::post('update/{id?}', [FAQController::class, 'update'])->name('admin.faq.update');
            Route::get('delete/{id?}', [FAQController::class, 'delete'])->name('admin.faq.delete');
            Route::get('view/{id?}', [FAQController::class, 'view'])->name('admin.faq.view');
            Route::post('reorder', [FAQController::class, 'reorder'])->name('admin.faq.reorder');
        });

        //Article Route
        Route::group(['prefix'=>'article'],function(){
            Route::get('/',[ArticleController::class,'index'])->name('admin.article.index');
            Route::get('create', [ArticleController::class, 'create'])->name('admin.article.create');
            Route::post('store', [ArticleController::class, 'store'])->name('admin.article.store');
            Route::post('status/{id?}', [ArticleController::class, 'changeStatus'])->name('admin.article.change.status');
            Route::get('edit/{id?}', [ArticleController::class, 'edit'])->name('admin.article.edit');
            Route::post('update/{id?}', [ArticleController::class, 'update'])->name('admin.article.update');
            Route::get('delete/{id?}', [ArticleController::class, 'delete'])->name('admin.article.delete');
            Route::get('view/{id?}', [ArticleController::class, 'view'])->name('admin.article.view');
        });

        //Contact Route
        Route::group(['prefix'=>'contact'],function(){
            Route::get('/',[ContactController::class,'index'])->name('admin.contact.index');
            Route::get('create', [ContactController::class, 'create'])->name('admin.contact.create');
            Route::post('store', [ContactController::class, 'store'])->name('admin.contact.store');
            Route::post('status/{id?}', [ContactController::class, 'changeStatus'])->name('admin.contact.change.status');
            Route::get('edit/{id?}', [ContactController::class, 'edit'])->name('admin.contact.edit');
            Route::post('update/{id?}', [ContactController::class, 'update'])->name('admin.contact.update');
            Route::get('delete/{id?}', [ContactController::class, 'delete'])->name('admin.contact.delete');
            Route::get('view/{id?}', [ContactController::class, 'view'])->name('admin.contact.view');
        });

        // Insurance Categories route
        Route::group(['prefix'=>'insurance'],function(){
            Route::get('/',[InsuranceController::class,'index'])->name('admin.insurances.index');
            Route::get('create', [InsuranceController::class, 'create'])->name('admin.insurances.create');
            Route::post('store', [InsuranceController::class, 'store'])->name('admin.insurances.store');
            Route::post('status/{id?}', [InsuranceController::class, 'changeStatus'])->name('admin.insurances.change.status');
            Route::get('edit/{id?}', [InsuranceController::class, 'edit'])->name('admin.insurances.edit');
            Route::post('update/{id?}', [InsuranceController::class, 'update'])->name('admin.insurances.update');
            Route::get('delete/{id?}', [InsuranceController::class, 'delete'])->name('admin.insurances.delete');
            Route::get('view/{id?}', [InsuranceController::class, 'view'])->name('admin.insurances.view');
            Route::post('reorder', [InsuranceController::class, 'reorder'])->name('admin.insurances.reorder');
        });

        // Claim Insurance route
        Route::group(['prefix'=>'claiminsurance'],function(){
            Route::get('/',[InsuranceClaimController::class,'index'])->name('admin.claiminsurance.index');
            Route::get('create', [InsuranceClaimController::class, 'create'])->name('admin.claiminsurance.create');
            Route::post('store', [InsuranceClaimController::class, 'store'])->name('admin.claiminsurance.store');
            Route::post('status/{id?}', [InsuranceClaimController::class, 'changeStatus'])->name('admin.claiminsurance.change.status');
            Route::get('edit/{id?}', [InsuranceClaimController::class, 'edit'])->name('admin.claiminsurance.edit');
            Route::post('update/{id?}', [InsuranceClaimController::class, 'update'])->name('admin.claiminsurance.update');
            Route::get('delete/{id?}', [InsuranceClaimController::class, 'delete'])->name('admin.claiminsurance.delete');
            Route::get('view/{id?}', [InsuranceClaimController::class, 'view'])->name('admin.claiminsurance.view');
            Route::get('filter', [InsuranceClaimController::class, 'filter'])->name('admin.claiminsurance.filter');
            Route::post('reorder', [InsuranceClaimController::class, 'reorder'])->name('admin.claiminsurance.reorder');

        });

        // Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');


        // User Enquery route
        Route::group(['prefix'=>'user_enquery'],function(){
            Route::get('/',[UserEnqueryController::class,'index'])->name('admin.user_enquery.index');
            // Route::get('create', [UserEnqueryController::class, 'create'])->name('admin.user_enquery.create');
            // Route::post('store', [UserEnqueryController::class, 'store'])->name('admin.user_enquery.store');
            // Route::post('status/{id?}', [UserEnqueryController::class, 'changeStatus'])->name('admin.user_enquery.change.status');
            // Route::get('edit/{id?}', [UserEnqueryController::class, 'edit'])->name('admin.user_enquery.edit');
            // Route::post('update/{id?}', [UserEnqueryController::class, 'update'])->name('admin.user_enquery.update');
            Route::get('delete/{id?}', [UserEnqueryController::class, 'delete'])->name('admin.user_enquery.delete');
            Route::get('view/{id?}', [UserEnqueryController::class, 'view'])->name('admin.user_enquery.view');
            // Route::get('filter', [UserEnqueryController::class, 'filter'])->name('admin.user_enquery.filter');
            // Route::post('reorder', [UserEnqueryController::class, 'reorder'])->name('admin.user_enquery.reorder');

        });

    });

    require __DIR__ . '/admin-auth.php';
});
