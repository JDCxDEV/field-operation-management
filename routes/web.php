<?php

use App\Http\Controllers\Account\SettingsController;
use App\Http\Controllers\Auth\SocialiteLoginController;
use App\Http\Controllers\Documentation\LayoutBuilderController;
use App\Http\Controllers\Documentation\ReferencesController;
use App\Http\Controllers\Logs\AuditLogsController;
use App\Http\Controllers\Logs\SystemLogsController;
use App\Http\Controllers\PagesController;
use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Users;
use App\Http\Controllers\Companies\CompanyController;
use App\Http\Controllers\Markets\MarketController;
use App\Http\Controllers\Recruits\RecruitController;
use App\Http\Controllers\AgentSubmissionForms\AgentSubmissionFormController;
use App\Http\Controllers\BlacklistedAddresses\BlacklistedAddressController;
use App\Http\Controllers\FormTemplates\FormTemplateController;
use App\Http\Controllers\Topics\TopicController;
use App\Http\Controllers\Topics\TopicItemController;
use App\Http\Controllers\ActivityLogs\ActivityLogController;
use App\Http\Controllers\Statuses\StatusController;


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

// Route::get('/', function () {
//     return redirect('index');
// });

$menu = theme()->getMenu();

array_walk($menu, function ($val) {
    if (isset($val['path'])) {
        $route = Route::get($val['path'], [PagesController::class, 'index']);

        // Exclude documentation from auth middleware
        if (!Str::contains($val['path'], 'documentation')) {
            $route->middleware('auth');
        }

        // Custom page demo for 500 server error
        if (Str::contains($val['path'], 'error-500')) {
            Route::get($val['path'], function () {
                abort(500, 'Something went wrong! Please try again later.');
            });
        }
    }
});

// Documentations pages
Route::prefix('documentation')->group(function () {
    Route::get('getting-started/references', [ReferencesController::class, 'index']);
    Route::get('getting-started/changelog', [PagesController::class, 'index']);
    Route::resource('layout-builder', LayoutBuilderController::class)->only(['store']);
});

Route::middleware('auth')->group(function () {
    
    Route::get('/', [PagesController::class, 'dashboard']);
    Route::get('/index', [PagesController::class, 'dashboard'])->name("dashboard");
    
    // Account pages
    Route::prefix('account')->group(function () {
        Route::get('overview', [SettingsController::class, 'overview'])->name('overview.index');
        Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('settings/{id?}', [SettingsController::class, 'update'])->name('settings.update');
        Route::post('settings/email/{id?}', [SettingsController::class, 'changeEmail'])->name('settings.changeEmail');
        Route::post('settings/password/{id?}', [SettingsController::class, 'changePassword'])->name('settings.changePassword');
        Route::post('settings/update-two-auth/{id?}', [SettingsController::class, 'updateTwoAuth'])->name('settings.updateTwoAuth');
        Route::post('settings/verify/{id?}', [SettingsController::class, 'verify'])->name('settings.verify');        
    });

    // Logs pages
    Route::prefix('log')->name('log.')->group(function () {
        Route::resource('system', SystemLogsController::class)->only(['index', 'destroy']);
        Route::resource('audit', AuditLogsController::class)->only(['index', 'destroy']);
    });

    Route::prefix('users')->group(function () {
        Route::get('/', UsersController::class, [UsersController::class, 'index'])->name("users.index");
        Route::post("/store", [UsersController::class, "store"])->name("users.store");
        Route::get("/get/user", [UsersController::class, "getUser"])->name("users.get");
        Route::get("/{id}/find", [UsersController::class, "find"])->name("users.find");        
        Route::get("/fetch", [UsersController::class, 'fetch'])->name("users.fetch");
        Route::get("/{id}/pod-users", [UsersController::class, 'podUsers'])->name("users.pod-users");
        Route::post("/{id}/appoint-pod-users", [UsersController::class, 'appointPodUsers'])->name("users.appoint-pod-users");
        Route::post("/{id}/pod-users/remove", [UsersController::class, 'removePodUsers'])->name("users.remove-pod-users");        
        Route::post("/{id}/update", [UsersController::class, 'update'])->name("users.update");
        Route::post("/{id}/deactivate", [UsersController::class, 'deactivate'])->name("users.deactivate");
        Route::post("/batch/deactivate-users", [UsersController::class, 'batchDeactivate'])->name("users.batch-deactivate");
        Route::post("/{id}/activate", [UsersController::class, 'activate'])->name("users.activate");
        Route::post("/batch/activate-users", [UsersController::class, 'batchActivate'])->name("users.batch-activate");
        Route::post("/{id}/block", [UsersController::class, 'block'])->name("users.block");
        Route::post("/{id}/unblock", [UsersController::class, 'unblock'])->name("users.unblock");   
        Route::post("/{id}/reset-password", [UsersController::class, 'resetPassword'])->name("users.reset-password");
        Route::post("/{id}/update-theme", [UsersController::class, 'updateTheme'])->name("users.update-theme");
        Route::get("/login-sessions/{id?}", [UsersController::class, 'loginSessions'])->name("users.login-sessions");        
    });

    Route::prefix("companies")->group(function() {
        Route::get("/", [CompanyController::class, "index"])->name("companies.index");
        Route::get("/fetch/{id?}", [CompanyController::class, "fetch"])->name("companies.fetch");     
        Route::get("/create", [CompanyController::class, "create"])->name("companies.create");
        Route::get("/assign-coordinators", [CompanyController::class, "appoint"])->name("companies.appoint");
        Route::get("/{id}/overview", [CompanyController::class, "overview"])->name("companies.overview");
        Route::get("/{id}/coordinators-and-users", [CompanyController::class, "coordinatorsAndUsers"])->name("companies.coordinators-and-users");
        Route::get("/{id}/common-users", [CompanyController::class, "commonUsers"])->name("companies.common-users");
        Route::get("/{id}/markets", [CompanyController::class, "markets"])->name("companies.markets");
        Route::post("/{id}/appoint", [CompanyController::class, "appointUsers"])->name("companies.appoint-users");
        Route::post("/{id}/coordinators/remove", [CompanyController::class, "coordinatorsRemove"])->name("companies.coordinators-remove");        
        Route::post("/store", [CompanyController::class, "store"])->name("companies.store");
        Route::get("/{id}/find", [CompanyController::class, "find"])->name("companies.find");
        Route::post("/{id}/update", [CompanyController::class, "update"])->name("companies.update");
        Route::post("/{id}/delete", [CompanyController::class, "destroy"])->name("companies.delete");
        Route::post("/{id}/restore", [CompanyController::class, "restore"])->name("companies.restore");        
    });

    Route::prefix("markets")->group(function() {
        Route::get("/", [MarketController::class, "index"])->name("markets.index");
        Route::get("/fetch/{id?}", [MarketController::class, "fetch"])->name("markets.fetch");
        Route::get("/create", [MarketController::class, "create"])->name("markets.create");
        Route::get("/{id}/overview", [MarketController::class, "overview"])->name("markets.overview");
        Route::get("/assign-directors", [MarketController::class, "appoint"])->name("markets.appoint");
        Route::get("/{id}/directors-and-users", [MarketController::class, "directorsAndUsers"])->name("markets.directors-and-users");
        Route::get("/{id}/common-users", [MarketController::class, "commonUsers"])->name("markets.common-users");        
        Route::post("/{id}/appoint", [MarketController::class, "appointUsers"])->name("markets.appoint-users");
        Route::post("/{id}/directors/remove", [MarketController::class, "directorsRemove"])->name("markets.directors-remove");
        Route::get("/{id}/pod-leaders", [MarketController::class, "marketPodLeaders"])->name("markets.pod-leaders");
        Route::post("/{id}/appoint-as-pod-leaders", [MarketController::class, "appointUsersAsPodLeaders"])->name("markets.appoint-users-as-pod-leaders");
        Route::post("/{id}/pod-leaders/remove", [MarketController::class, "podLeadersRemove"])->name("markets.pod-leaders-remove");                
        Route::post("/link-company", [MarketController::class, "linkCompany"])->name("markets.link-company");     
        Route::post("/{id}/unlink-company", [MarketController::class, "unlinkCompany"])->name("markets.unlink-company");        
        Route::post("/store", [MarketController::class, "store"])->name("markets.store");
        Route::get("/{id}/find", [MarketController::class, "find"])->name("markets.find");
        Route::post("/{id}/update", [MarketController::class, "update"])->name("markets.update");
        Route::post("/{id}/delete", [MarketController::class, "destroy"])->name("markets.delete");
        Route::post("/batch/archive", [MarketController::class, 'batchArchive'])->name("markets.batch-archive");
        Route::post("/batch/restore", [MarketController::class, 'batchRestore'])->name("markets.batch-restore");              
        Route::post("/{id}/restore", [MarketController::class, "restore"])->name("markets.restore"); 
    });

    Route::prefix("recruits")->group(function() {
        Route::get("/", [RecruitController::class, "index"])->name("recruits.index");
        Route::get("/fetch", [RecruitController::class, "fetch"])->name("recruits.fetch");
        Route::get("/create", [RecruitController::class, "create"])->name("recruits.create");
        Route::post("/store", [RecruitController::class, "store"])->name("recruits.store");
        Route::get("/{id}/find", [RecruitController::class, "find"])->name("recruits.find");
        Route::post("/{id}/update", [RecruitController::class, "update"])->name("recruits.update");
        Route::post("/{id}/delete", [RecruitController::class, "destroy"])->name("recruits.delete");
        Route::post("/{id}/restore", [RecruitController::class, "restore"])->name("recruits.restore");        
    });

    Route::prefix("agent-submission-forms")->group(function() {
        Route::get("/", [AgentSubmissionFormController::class, "index"])->name("agent-submission-forms.index");
        Route::get("/fetch", [AgentSubmissionFormController::class, "fetch"])->name("agent-submission-forms.fetch");
        Route::get("/create", [AgentSubmissionFormController::class, "create"])->name("agent-submission-forms.create");
        Route::get("/{id}/find", [AgentSubmissionFormController::class, "find"])->name("agent-submission-forms.find");
        Route::get("/{id}/show", [AgentSubmissionFormController::class, "show"])->name("agent-submission-forms.show");
        Route::get("/{id}/view", [AgentSubmissionFormController::class, "view"])->name("agent-submission-forms.view");
        Route::post("/client-validation/{id?}", [AgentSubmissionFormController::class, "clientValidation"])->name("agent-submission-forms.client-validation");
        Route::post("/client-address-validation/{id?}", [AgentSubmissionFormController::class, "clientAddressValidation"])->name("agent-submission-forms.client-address-validation");
        Route::post("/update-dependent/{id?}", [AgentSubmissionFormController::class, "updateDependent"])->name("agent-submission-forms.update-dependent");
        Route::get("/delete-dependent/{id}", [AgentSubmissionFormController::class, "deleteDependent"])->name("agent-submission-forms.delete-dependent");
        Route::post("/enrollment-data/{id?}", [AgentSubmissionFormController::class, "enrollmentData"])->name("agent-submission-forms.enrollment-data");        
        Route::post("/additional-files/{id?}", [AgentSubmissionFormController::class, "additionalFiles"])->name("agent-submission-forms.additional-files");
        Route::post("/employment-data/{id?}", [AgentSubmissionFormController::class, "employmentData"])->name("agent-submission-forms.employment-data");
        Route::post("/plan-information/{id?}", [AgentSubmissionFormController::class, "planInformation"])->name("agent-submission-forms.plan-information");
        Route::post("/payment-information/{id?}", [AgentSubmissionFormController::class, "paymentInformation"])->name("agent-submission-forms.payment-information");
        Route::get("/delete-file/{fileId}", [AgentSubmissionFormController::class, "deleteFile"])->name("agent-submission-forms.delete-file");                
        Route::post("/save-signature/{id?}", [AgentSubmissionFormController::class, "saveSignature"])->name("agent-submission-forms.save-signature");
        Route::post("/submit/{id}", [AgentSubmissionFormController::class, "submit"])->name("agent-submission-forms.submit");
        Route::post("/save-changes/{id}", [AgentSubmissionFormController::class, "saveChanges"])->name("agent-submission-forms.save-changes");
        Route::post("/update-status/{id}", [AgentSubmissionFormController::class, "updateStatus"])->name("agent-submission-forms.update-status");
        Route::post("/export/{type}", [AgentSubmissionFormController::class, "export"])->name("agent-submission-forms.export");        
    });


    Route::prefix("blacklisted-addresses")->group(function() {
        Route::get("/", [BlacklistedAddressController::class, "index"])->name("blacklisted-addresses.index");
        Route::get("/fetch/{id?}", [BlacklistedAddressController::class, "fetch"])->name("blacklisted-addresses.fetch");
        Route::post("/store", [BlacklistedAddressController::class, "store"])->name("blacklisted-addresses.store");
        Route::get("/{id}/find", [BlacklistedAddressController::class, "find"])->name("blacklisted-addresses.find");
        Route::post("/{id}/update", [BlacklistedAddressController::class, "update"])->name("blacklisted-addresses.update");
        Route::post("/{id}/delete", [BlacklistedAddressController::class, "destroy"])->name("blacklisted-addresses.delete");
        Route::post("/{id}/restore", [BlacklistedAddressController::class, "restore"])->name("blacklisted-addresses.restore"); 
        Route::get("/export", [BlacklistedAddressController::class, "export"])->name("blacklisted-addresses.export"); 
    });

    Route::prefix("form-templates")->group(function() {
        Route::get("/", [FormTemplateController::class, "index"])->name("form-templates.index");
        Route::get("/fetch/{id?}", [FormTemplateController::class, "fetch"])->name("form-templates.fetch");
        Route::get("/fetch-active", [FormTemplateController::class, "fetchActive"])->name("form-templates.fetch-active");
        Route::post("/store", [FormTemplateController::class, "store"])->name("form-templates.store");
        Route::get("/{id}/find", [FormTemplateController::class, "find"])->name("form-templates.find");
        Route::post("/{id}/update", [FormTemplateController::class, "update"])->name("form-templates.update");
        Route::post("/{id}/delete", [FormTemplateController::class, "destroy"])->name("form-templates.delete");
        Route::post("/{id}/restore", [FormTemplateController::class, "restore"])->name("form-templates.restore");
        Route::post("/{id}/activate", [FormTemplateController::class, "activate"])->name("form-templates.activate");
    });

    Route::prefix("faqs")->group(function() {
        Route::get("/", [TopicController::class, "faqs"])->name("faqs.index");
    });

    Route::prefix("topics")->group(function() {
        Route::get("/", [TopicController::class, "index"])->name("topics.index");
        Route::get("/fetch", [TopicController::class, "fetch"])->name("topics.fetch");
        Route::post("/store", [TopicController::class, "store"])->name("topics.store");
        Route::get("/{id}/find", [TopicController::class, "find"])->name("topics.find");
        Route::post("/{id}/update", [TopicController::class, "update"])->name("topics.update");
        Route::post("/{id}/delete", [TopicController::class, "destroy"])->name("topics.delete");
        Route::post("/{id}/restore", [TopicController::class, "restore"])->name("topics.restore");
        Route::post("/sort/order/update", [TopicController::class, "updateOrder"])->name("topics.update-order");
    });

    Route::prefix("topic-items")->group(function() {
        Route::post("/{topic}/store", [TopicItemController::class, "store"])->name("topic-items.store");
        Route::post("/{id}/update", [TopicItemController::class, "update"])->name("topic-items.update");
        Route::post("/{id}/delete", [TopicItemController::class, "destroy"])->name("topic-items.delete");
        Route::post("/{id}/restore", [TopicItemController::class, "restore"])->name("topic-items.restore");
        Route::post("/sort/order/update/{topic}", [TopicItemController::class, "updateOrder"])->name("topic-items.update-order");        
    });


    Route::prefix("statuses")->group(function() {
        Route::get("/fetch", [StatusController::class, "fetch"])->name("statuses.fetch");
        Route::get("/{id}/find", [StatusController::class, "find"])->name("statuses.find");        
        Route::post("/store", [StatusController::class, "store"])->name("statuses.store");
        Route::post("/{id}/update", [StatusController::class, "update"])->name("statuses.update");
        Route::post("/{id}/delete", [StatusController::class, "destroy"])->name("statuses.delete");
        Route::post("/{id}/force-delete", [StatusController::class, "forceDelete"])->name("statuses.force-delete");
        Route::post("/{id}/restore", [StatusController::class, "restore"])->name("statuses.restore");
        Route::post("/sort/order/update/{topic}", [StatusController::class, "updateOrder"])->name("statuses.update-order");        
    });

    Route::prefix("activity-logs")->group(function() {
        Route::get("/", [ActivityLogController::class, "index"])->name("activity-logs.index");
        Route::get("/fetch", [ActivityLogController::class, "fetch"])->name("activity-logs.fetch");
        Route::get("/fetch-by-subject/{subjectId}/{subjectType}", [ActivityLogController::class, "fetchBySubject"])->name("activity-logs.fetch-by-subject");
        Route::get("/recent", [ActivityLogController::class, "recent"])->name("activity-logs.recent");
        Route::post("/{id}/delete", [ActivityLogController::class, "destroy"])->name("activity-logs.delete");
    });

});

/**
 * Socialite login using Google service
 * https://laravel.com/docs/8.x/socialite
 */
Route::get('/auth/redirect/{provider}', [SocialiteLoginController::class, 'redirect']);

require __DIR__.'/auth.php';
