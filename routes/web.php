<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\RegistrationController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registration Routes
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// CSRF Token Endpoint
Route::get('/csrf-token', function () {
    return response()->json(['token' => csrf_token()]);
});

// Protected Routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Account / Profile Management
    Route::get('/account', [App\Http\Controllers\AccountController::class, 'index'])->name('account.index');
    Route::get('/account/edit', [App\Http\Controllers\AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [App\Http\Controllers\AccountController::class, 'update'])->name('account.update');
    Route::delete('/account/file', [App\Http\Controllers\AccountController::class, 'deleteFile'])->name('account.delete-file');
    
    // File download routes
    Route::get('/account/file/certificate', [App\Http\Controllers\AccountController::class, 'downloadCertificate'])->name('account.download-certificate');
    Route::get('/account/file/resume', [App\Http\Controllers\AccountController::class, 'downloadResume'])->name('account.download-resume');
    
    // Events browsing
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    
    // Event registrations
    Route::get('/events/{event}/register', [RegistrationController::class, 'create'])->name('registrations.create');
    Route::post('/events/{event}/register', [RegistrationController::class, 'store'])->name('registrations.store');
    Route::get('/my-registrations', [RegistrationController::class, 'index'])->name('registrations.index');
    Route::get('/registrations/{registration}/edit', [RegistrationController::class, 'edit'])->name('registrations.edit');
    Route::put('/registrations/{registration}', [RegistrationController::class, 'update'])->name('registrations.update');
    Route::delete('/registrations/{registration}', [RegistrationController::class, 'destroy'])->name('registrations.destroy');
    
    // Event Dashboard (role-based)
    Route::get('/events/{event}/dashboard/{registration}', [App\Http\Controllers\EventDashboardController::class, 'show'])->name('event.dashboard');
    
    // Manual Check-In Route
    Route::post('/events/{event}/check-in/{registration}', [App\Http\Controllers\EventDashboardController::class, 'manualCheckIn'])->name('event.check-in');

    // Paper Management Routes
    Route::post('/events/{event}/paper/{registration}', [App\Http\Controllers\EventDashboardController::class, 'updatePaper'])->name('event.paper.update');
    
    // Reviewer Routes
    Route::post('/events/{event}/submit-review/{registration}', [App\Http\Controllers\EventDashboardController::class, 'submitReview'])->name('event.submit-review');
    
    // Jury Evaluation Routes
    Route::get('/jury/evaluate/{juryMapping}', [App\Http\Controllers\EventDashboardController::class, 'showJuryEvaluationForm'])->name('jury.evaluate');
    Route::post('/jury/evaluate/{juryMapping}', [App\Http\Controllers\EventDashboardController::class, 'submitJuryEvaluation'])->name('jury.evaluate.submit');
    Route::get('/events/jury-dashboard/{registration}', [App\Http\Controllers\EventDashboardController::class, 'juryDashboard'])->name('events.jury-dashboard');
    
    // Feedback Routes
    Route::get('/feedback', [App\Http\Controllers\FeedbackController::class, 'index'])->name('feedback.index');
    Route::get('/feedback/{registration}/create', [App\Http\Controllers\FeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/feedback/{registration}', [App\Http\Controllers\FeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/feedback/{registration}', [App\Http\Controllers\FeedbackController::class, 'show'])->name('feedback.show');
});

// ============================================================================
// QR CODE CHECK-IN ROUTES
// ============================================================================

// QR Code Check-In Routes (Public - no auth required for scanning)
Route::prefix('check-in')->name('qr.scan.')->group(function () {
    Route::get('/{qrCode}', [App\Http\Controllers\QrCheckInController::class, 'scan'])->name('registration');
    Route::post('/{qrCode}', [App\Http\Controllers\QrCheckInController::class, 'checkIn'])->name('process');
});
