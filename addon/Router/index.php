<?php

use App\Core\Http\Request;
use App\Core\Http\Response;
use Addon\Controllers\AuthController;
use Addon\Controllers\DashboardController;
use Addon\Controllers\Admin\MasterController;
use Addon\Controllers\RegistrationController;
use Addon\Controllers\DocumentController;
use Addon\Controllers\PaymentController;
use Addon\Controllers\SelectionController;
use Addon\Controllers\AnnouncementController;
use Addon\Controllers\ReRegistrationController;
use Addon\Controllers\Admin\RegistrantController;
use Addon\Controllers\Admin\ReportController;
use Addon\Models\UserModel;
use App\Services\SessionService;

/** @var \App\Core\Routing\Router $router */

// Guest routes (login, register, password reset, OTP verification)
$router->group(['middleware' => ['csrf', 'guest']], function () use ($router) {
    // Login
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    
    // Register
    $router->get('/register', [AuthController::class, 'showRegister']);
    $router->post('/register', [AuthController::class, 'register']);
    
    // OTP Verification
    $router->get('/verify-otp', [AuthController::class, 'showVerifyOtp']);
    $router->post('/verify-otp', [AuthController::class, 'verifyOtp']);
    $router->get('/resend-otp', [AuthController::class, 'resendOtp']);
    $router->get('/otp-sent', [AuthController::class, 'showOtpSent']);
    
    // Password reset
    $router->get('/password/forgot', [AuthController::class, 'showForgotPassword']);
    $router->post('/password/forgot', [AuthController::class, 'sendResetLink']);
    $router->get('/password/reset', [AuthController::class, 'showResetPassword']);
    $router->post('/password/reset', [AuthController::class, 'resetPassword']);
});

// Auth routes (require login)
$router->group(['middleware' => ['csrf', 'auth']], function () use ($router) {
    // Dashboard
    $router->get('/dashboard', [DashboardController::class, 'index']);
    $router->post('/dashboard/simulate-state', [DashboardController::class, 'simulateState']);
    
    // Logout
    $router->post('/logout', [AuthController::class, 'logout']);

    // Profile
    $router->get('/profile', [AuthController::class, 'showProfile']);
    $router->post('/profile', [AuthController::class, 'updateProfile']);

    // Registration Form
    $router->get('/pendaftaran', [RegistrationController::class, 'showForm']);
    $router->post('/pendaftaran/save', [RegistrationController::class, 'saveDraft']);
    $router->post('/pendaftaran/submit', [RegistrationController::class, 'submit']);
    $router->post('/pendaftaran/step', [RegistrationController::class, 'updateActiveStep']);

    // Document Upload & View
    $router->get('/pendaftaran/dokumen', [DocumentController::class, 'showUploadPage']);
    $router->post('/pendaftaran/dokumen/upload', [DocumentController::class, 'upload']);
    $router->get('/documents/view', [DocumentController::class, 'viewFile']);

    // Payment Upload & View
    $router->post('/pendaftaran/pembayaran/upload', [PaymentController::class, 'uploadPayment']);
    $router->get('/payments/view', [PaymentController::class, 'viewFile']);

    // PDF Selection Result Letter Download
    $router->get('/pendaftaran/kelulusan/download', [AnnouncementController::class, 'downloadLetter']);

    // Re-registration
    $router->get('/pendaftaran/daftar-ulang', [ReRegistrationController::class, 'showReRegistrationForm']);
    $router->post('/pendaftaran/daftar-ulang/submit', [ReRegistrationController::class, 'submitReRegistration']);
    $router->get('/re-registrations/view', [ReRegistrationController::class, 'viewFile']);

    // Student Exam Card & Form Downloads
    $router->get('/pendaftaran/kartu-ujian', [RegistrantController::class, 'downloadExamCard']);
    $router->get('/pendaftaran/formulir', [RegistrantController::class, 'downloadRegistrationForm']);
    $router->get('/notifications/mark-read', [DashboardController::class, 'markNotificationsRead']);

    // Admin Registrant Management
    $router->get('/admin/registrants', [RegistrantController::class, 'listRegistrants']);
    $router->get('/admin/registrants/detail', [RegistrantController::class, 'showDetail']);
    $router->post('/admin/registrants/exam-stage/save', [RegistrantController::class, 'saveExamStageStatus']);
    $router->get('/admin/registrants/edit', [RegistrantController::class, 'editRegistrantForm']);
    $router->post('/admin/registrants/update', [RegistrantController::class, 'updateRegistrant']);
    $router->get('/admin/registrants/export/pdf', [RegistrantController::class, 'exportPdf']);
    $router->get('/admin/registrants/export/csv', [RegistrantController::class, 'exportCsv']);
    $router->get('/admin/registrants/pdf/kartu-ujian', [RegistrantController::class, 'downloadExamCardAdmin']);
    $router->get('/admin/registrants/pdf/formulir', [RegistrantController::class, 'downloadRegistrationFormAdmin']);

    // Admin Reports & Statistics
    $router->get('/admin/reports', [ReportController::class, 'index']);
    $router->get('/admin/reports/export/finance', [ReportController::class, 'exportFinance']);
    $router->get('/admin/reports/export/selection', [ReportController::class, 'exportSelection']);
    $router->get('/admin/reports/export/re-registrations', [ReportController::class, 'exportReRegistration']);
});

// Admin routes (require login & manage_users permission)
$router->group(['middleware' => ['auth', 'permission:manage_users']], function () use ($router) {
    $router->get('/admin/users', [AuthController::class, 'listUsers']);
    $router->post('/admin/users/update', [AuthController::class, 'updateUser']);
});

// Admin payments verification (require login & verify_payment permission)
$router->group(['middleware' => ['auth', 'permission:verify_payment']], function () use ($router) {
    $router->get('/admin/payments', [PaymentController::class, 'listPayments']);
    $router->post('/admin/payments/verify', [PaymentController::class, 'verifyPayment']);

    // Admin re-registration verification
    $router->get('/admin/re-registrations', [ReRegistrationController::class, 'listReRegistrations']);
    $router->get('/admin/re-registrations/detail', [ReRegistrationController::class, 'showDetail']);
    $router->post('/admin/re-registrations/verify', [ReRegistrationController::class, 'verifyReRegistration']);
    $router->get('/admin/re-registrations/generate-nim', [ReRegistrationController::class, 'apiGenerateNim']);
});

// Admin document verification (require login & verify_document permission)
$router->group(['middleware' => ['auth', 'permission:verify_document']], function () use ($router) {
    $router->get('/admin/verifications', [DocumentController::class, 'listVerifications']);
    $router->get('/admin/verifications/detail', [DocumentController::class, 'showVerificationDetail']);
    $router->post('/admin/verifications/verify-document', [DocumentController::class, 'verifyDocument']);
});

// Admin selection & quota management (require login & manage_selection permission)
$router->group(['middleware' => ['auth', 'permission:manage_selection']], function () use ($router) {
    $router->get('/admin/selection', [SelectionController::class, 'listCandidates']);
    $router->post('/admin/selection/quota', [SelectionController::class, 'updateQuota']);
    $router->post('/admin/selection/save', [SelectionController::class, 'saveScoresAndStatus']);
    $router->post('/admin/selection/publish', [SelectionController::class, 'publishStatus']);
    $router->post('/admin/selection/publish-all', [SelectionController::class, 'publishAll']);

    // Announcements CRUD
    $router->get('/admin/announcements', [AnnouncementController::class, 'listAnnouncements']);
    $router->post('/admin/announcements/save', [AnnouncementController::class, 'saveAnnouncement']);
    $router->post('/admin/announcements/delete', [AnnouncementController::class, 'deleteAnnouncement']);
});

// Admin routes (require login & manage_settings permission)
$router->group(['middleware' => ['auth', 'permission:manage_settings']], function () use ($router) {
    $router->get('/admin/master', [MasterController::class, 'index']);
    $router->post('/admin/master/create', [MasterController::class, 'create']);
    $router->post('/admin/master/update', [MasterController::class, 'update']);
    $router->post('/admin/master/delete', [MasterController::class, 'delete']);
    $router->get('/admin/master/wave-detail', [MasterController::class, 'waveDetail']);
    $router->post('/admin/master/wave-detail/save', [MasterController::class, 'saveWaveDetail']);
    $router->post('/admin/master/registration-fee/save', [MasterController::class, 'saveRegistrationFee']);

    // System Settings
    $router->get('/admin/settings', [\Addon\Controllers\Admin\SettingController::class, 'index']);
    $router->post('/admin/settings/general', [\Addon\Controllers\Admin\SettingController::class, 'updateGeneral']);
    $router->post('/admin/settings/academic-year', [\Addon\Controllers\Admin\SettingController::class, 'updateAcademicYear']);
    $router->post('/admin/settings/wave', [\Addon\Controllers\Admin\SettingController::class, 'updateWave']);
});

// Home route
$router->get('/', [\Addon\Controllers\DashboardController::class, 'index']);