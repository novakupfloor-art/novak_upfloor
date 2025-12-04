<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MobilePackageController;
use App\Http\Controllers\Api\MobilePropertyController;
use App\Http\Controllers\Api\MobileSignupController;
use App\Http\Controllers\Api\MobileLoginController;
use App\Http\Controllers\Api\MobileStaffController;
use App\Http\Controllers\Api\MobileDashboardController;
use App\Http\Controllers\Api\MobileArticleController;
use App\Http\Controllers\Api\MobileStorageController;
use App\Http\Controllers\Api\AiWaisakaSearchController;
use App\Http\Controllers\Api\AiWaisakaInsightController;
use App\Http\Controllers\Api\AdminDashboardController;
use Illuminate\Support\Facades\Mail;

Route::prefix('v1')->group(function () {


    // ========================================
    // (No Authentication Required)
    // ========================================

    Route::get('/articles', [MobileArticleController::class, 'index']);
    Route::get('/articles/{id}', [MobileArticleController::class, 'show']);

    Route::get('/mobile/packages', [MobilePackageController::class, 'index']);

    // AI Waisaka Search Routes (Public - No Authentication Required)
    Route::post('/ai-waisaka/search', [AiWaisakaSearchController::class, 'processAiSearch']);
    Route::get('/ai-waisaka/suggestions', [AiWaisakaSearchController::class, 'getSearchSuggestions']);
    Route::post('/ai-waisaka/market-insight', [AiWaisakaInsightController::class, 'marketInsight']);
    Route::post('/ai-waisaka/chat', [AiWaisakaSearchController::class, 'chat']);

    // Property Routes - IMPORTANT: Specific routes MUST come before {id} wildcard route!
    Route::get('/mobile/properties', [MobilePropertyController::class, 'index']);
    Route::get('/mobile/properties/categories', [MobilePropertyController::class, 'getCategories']);
    Route::get('/mobile/properties/search', [MobilePropertyController::class, 'search']);
    Route::get('/mobile/properties-all', [MobilePropertyController::class, 'getPropertiesAll']);
    // AI Insight routes - {id} parameter at the END to avoid conflicts
    Route::get('/mobile/property/ai-debug/{id}', [MobilePropertyController::class, 'debugAiInsights']);
    Route::post('/mobile/property/ai/{id}', [MobilePropertyController::class, 'generatePropertyAiInsight']);
    // Wildcard {id} route MUST be last to avoid catching specific routes above
    Route::get('/mobile/properties/{id}', [MobilePropertyController::class, 'show']);





    // Mobile Authentication Routes 
    Route::post('/mobile/login', [MobileLoginController::class, 'login']);
    Route::post('/mobile/signup', [MobileSignupController::class, 'signup']);
    Route::post('/mobile/forgot-password', [MobileLoginController::class, 'forgotPassword']);
    Route::post('/mobile/resend-verification', [MobileSignupController::class, 'resendVerification']);
    Route::get('/mobile/resend-verification-web/{email}', [MobileSignupController::class, 'resendVerificationWeb']);  // Web resend for email links
    Route::post('/mobile/logout', [MobileLoginController::class, 'logout']);
    Route::get('/mobile/verify-email', [MobileSignupController::class, 'verifyEmailWeb']);  // Web verification for email links
    Route::post('/mobile/reset-password', [MobileLoginController::class, 'resetPassword']);

    // Storage Routes (untuk sync data login ke database)
    Route::post('/mobile/storage/create-or-update', [MobileStorageController::class, 'createOrUpdate']);
    Route::post('/mobile/storage/delete', [MobileStorageController::class, 'deleteByToken']);
    Route::get('/mobile/storage/user/{userId}', [MobileStorageController::class, 'getByUserId']);

    Route::middleware('auth.token')->group(function () {
        Route::get('/mobile/control-panel/staff/profile/{id}', [MobileStaffController::class, 'getProfile']);
        Route::put('/mobile/control-panel/staff/profile/{id}', [MobileStaffController::class, 'updateProfile']);
        Route::post('/mobile/control-panel/staff/profile/{id}', [MobileStaffController::class, 'updateProfile']);
        Route::get('/mobile/control-panel/staff/stats/{id}', [MobileStaffController::class, 'getStats']);

        Route::get('/mobile/control-panel/dashboard/stats/{id}', [MobileDashboardController::class, 'getStats']);
        Route::get('/mobile/control-panel/dashboard/activities/{id}', [MobileDashboardController::class, 'getRecentActivities']);
        Route::get('/mobile/control-panel/dashboard/packages/{id}', [MobileDashboardController::class, 'getMyPackages']);
        Route::get('/mobile/control-panel/dashboard/advertisements/{id}', [MobileDashboardController::class, 'getMyAdvertisements']);

        Route::get('/mobile/control-panel/packages/my-packages/{id}', [MobilePackageController::class, 'getMyPackages']);
        Route::get('/mobile/control-panel/packages/available', [MobilePackageController::class, 'getAvailablePackages']);
        Route::post('/mobile/control-panel/packages/buy', [MobilePackageController::class, 'buyPackage']);
        Route::get('/mobile/control-panel/packages/transactions/{id_user}', [MobilePackageController::class, 'getTransactions']);
        Route::put('/mobile/control-panel/packages/update/{id}', [MobilePackageController::class, 'updateTransaction']);
        Route::post('/mobile/control-panel/packages/reupload-proof', [MobilePackageController::class, 'reuploadPaymentProof']);

        Route::get('/mobile/control-panel/properties/properties/{id}', [MobilePropertyController::class, 'getProperties']);
        Route::post('/mobile/control-panel/properties/create', [MobilePropertyController::class, 'createProperty']);
        Route::put('/mobile/control-panel/properties/update/{id}', [MobilePropertyController::class, 'updateProperty']);
        Route::delete('/mobile/control-panel/properties/delete/{id}', [MobilePropertyController::class, 'deleteProperty']);
        Route::get('/mobile/control-panel/properties/master-data', [MobilePropertyController::class, 'getMasterData']);
        Route::get('/mobile/control-panel/properties/kabupaten/{provinsiId}', [MobilePropertyController::class, 'getKabupaten']);
        Route::get('/mobile/control-panel/properties/kecamatan/{kabupatenId}', [MobilePropertyController::class, 'getKecamatan']);
        Route::post('/mobile/control-panel/properties/search', [MobilePropertyController::class, 'searchProperties']);
    });

    // Routes ini dihapus karena sudah ada di control-panel/properties/... dan tidak sesuai dengan struktur


    // ========================================
    // ADMIN DASHBOARD ROUTES (Mobile Admin Panel)
    // ========================================

    Route::middleware('auth.token')->group(function () {
        Route::get('/mobile/admin/stats', [AdminDashboardController::class, 'getAdminStats']);
        Route::get('/mobile/admin/transactions', [AdminDashboardController::class, 'getAllTransactions']);
        Route::put('/mobile/admin/transactions/{id}', [AdminDashboardController::class, 'updateTransaction']);
        Route::post('/mobile/admin/transactions/{id}/confirm', [AdminDashboardController::class, 'updateTransaction']);
        Route::post('/mobile/admin/transactions/{id}/reject', [AdminDashboardController::class, 'updateTransaction']);
        Route::get('/mobile/admin/articles', [AdminDashboardController::class, 'getAllArticles']);
        Route::post('/mobile/admin/articles', [AdminDashboardController::class, 'createArticle']);
        Route::put('/mobile/admin/articles/{id}', [AdminDashboardController::class, 'updateArticle']);
        Route::delete('/mobile/admin/articles/{id}', [AdminDashboardController::class, 'deleteArticle']);
        Route::get('/mobile/admin/articles/categories', [AdminDashboardController::class, 'getArticleCategories']);
        Route::get('/mobile/admin/packages', [AdminDashboardController::class, 'getAllPackages']);
        Route::post('/mobile/admin/packages', [AdminDashboardController::class, 'createPackage']);
        Route::put('/mobile/admin/packages/{id}', [AdminDashboardController::class, 'updatePackage']);
        Route::delete('/mobile/admin/packages/{id}', [AdminDashboardController::class, 'deletePackage']);
    });

});