<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\StaffAuthController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DoctorProfileController;
use App\Http\Controllers\AppointmentBookingController;
use App\Http\Controllers\ManageAppointmentController;
use App\Http\Controllers\DoctorAppointmentController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\AdminAppointmentController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\AdminAvailabilityController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ## PUBLIC ROUTES ##
Route::get('/', function () {
    return view('homepage');
})->name('homepage');


// ## GUEST-ONLY ROUTES (PATIENT) ##
Route::middleware('guest_patient')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});


// ## GUEST-ONLY ROUTES (STAFF) ##
Route::prefix('staff')->name('staff.')->middleware('guest_staff')->group(function () {
    Route::get('/login', [StaffAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [StaffAuthController::class, 'login']);
    Route::get('/register', [StaffAuthController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [StaffAuthController::class, 'register']);
});


// ## AUTHENTICATED ROUTES (ALL ROLES) ##
Route::middleware('auth')->group(function () {
    
    // Universal Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');
    // --- PATIENT-ONLY ROUTES ---
    Route::prefix('patient')->middleware('is_patient')->name('patient.')->group(function () {
        Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        
        Route::get('/appointment-history', [ManageAppointmentController::class, 'history'])->name('appointments.history');
        Route::resource('/appointments', ManageAppointmentController::class)->except(['create', 'store', 'edit']);
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
        // In routes/web.php -> PATIENT-ONLY ROUTES block

        Route::get('/appointments/{appointment}/export-pdf', [ExportController::class, 'exportPatientConfirmation'])->name('appointments.export.patient');
        // In routes/web.php -> inside Route::prefix('patient')->...->group()

Route::get('/api/doctors/{doctor}/available-slots', [AppointmentBookingController::class, 'getAvailableSlots'])->name('api.doctors.slots');

        // Booking Flow
        Route::get('book-appointment/step-1', [AppointmentBookingController::class, 'createStepOne'])->name('book.create.step.one');
        Route::get('book-appointment/step-2', [AppointmentBookingController::class, 'createStepTwo'])->name('book.create.step.two');
        Route::post('book-appointment/step-2', [AppointmentBookingController::class, 'storeStepTwo'])->name('book.store.step.two');
        Route::get('book-appointment/step-3', [AppointmentBookingController::class, 'createStepThree'])->name('book.create.step.three');
        Route::post('book-appointment/step-3', [AppointmentBookingController::class, 'storeStepThree'])->name('book.store.step.three');
        Route::get('book-appointment/step-4', [AppointmentBookingController::class, 'createStepFour'])->name('book.create.step.four');
        Route::post('book-appointment/store', [AppointmentBookingController::class, 'store'])->name('book.store');
        Route::get('book-appointment/confirmation', [AppointmentBookingController::class, 'confirmation'])->name('book.confirmation');
    });

    // --- DOCTOR-ONLY ROUTES ---
    Route::prefix('doctor')->middleware('is_doctor')->name('doctor.')->group(function () {
        Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [DoctorProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [DoctorProfileController::class, 'update'])->name('profile.update');
        Route::get('/appointment-history', [DoctorAppointmentController::class, 'history'])->name('appointments.history');
        Route::get('/appointments', [DoctorAppointmentController::class, 'index'])->name('appointments.index');
        Route::patch('/appointments/{appointment}/status', [DoctorAppointmentController::class, 'updateStatus'])->name('appointments.updateStatus');
        Route::get('/api/appointments-by-date', [DoctorDashboardController::class, 'getAppointmentsForDate'])->name('api.appointments.by_date');
        Route::put('/password', [DoctorProfileController::class, 'updatePassword'])->name('password.update');
        Route::get('/schedule/export-pdf', [ExportController::class, 'exportDoctorSchedule'])->name('schedule.export.doctor');
        Route::get('/history/export-pdf', [ExportController::class, 'exportDoctorHistory'])->name('history.export.doctor');
        Route::get('/availability', [AvailabilityController::class, 'index'])->name('availability.index');
        Route::get('/availability/schedule', [AvailabilityController::class, 'getDailySchedule'])->name('availability.schedule');
        Route::post('/availability', [AvailabilityController::class, 'store'])->name('availability.store');
        Route::delete('/availability', [AvailabilityController::class, 'destroy'])->name('availability.destroy');
    });

    // --- ADMIN-ONLY ROUTES ---
    Route::prefix('admin')->middleware('is_admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::resource('/users', UserManagementController::class);
        Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show'); // For modal data
        Route::patch('/appointments/{appointment}', [AdminAppointmentController::class, 'update'])->name('appointments.update');
        Route::delete('/appointments/{appointment}', [AdminAppointmentController::class, 'destroy'])->name('appointments.destroy');
        Route::get('/api/available-slots', [AdminAppointmentController::class, 'getAvailableSlots'])->name('api.available_slots');
        // In routes/web.php -> ADMIN-ONLY ROUTES block
        Route::get('/appointment-history', [AdminAppointmentController::class, 'history'])->name('appointments.history');
        Route::get('/appointments/{appointment}/edit', [AdminAppointmentController::class, 'edit'])->name('appointments.edit');
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::get('/schedule/export-pdf', [ExportController::class, 'exportAdminSchedule'])->name('schedule.export.admin');
        Route::get('/history/export-pdf', [ExportController::class, 'exportAdminHistory'])->name('history.export.admin');
        Route::get('/doctors/{doctor}/availability', [AdminAvailabilityController::class, 'index'])->name('availability.index');
        Route::get('/doctors/{doctor}/availability/schedule', [AdminAvailabilityController::class, 'getDailySchedule'])->name('availability.schedule');
        Route::post('/doctors/{doctor}/availability', [AdminAvailabilityController::class, 'store'])->name('availability.store');
        Route::delete('/doctors/{doctor}/availability', [AdminAvailabilityController::class, 'destroy'])->name('availability.destroy');
    });

    // --- STAFF ROUTER (Redirects after login) ---
    Route::get('/staff/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard.router');

});