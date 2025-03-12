<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\BedController;
use App\Http\Controllers\MedicalOrderController;
use App\Http\Controllers\MedicalOrderDetailController;
use App\Http\Controllers\MedicationNotificationController;
use App\Http\Controllers\MedicationRecordController;
use App\Http\Controllers\MedicationRecordDetailController;
use App\Http\Controllers\NurseRecordController;
use App\Http\Controllers\NurseRecordDetailController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TemperatureDetailController;
use App\Http\Controllers\TemperatureRecordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DrugController;
use App\Http\Controllers\DietController;
use App\Http\Controllers\ReportController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        // 'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});
Route::get('/register', function () {
    abort(404);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/admissions/filter', [AdmissionController::class, 'getFilteredAdmissions'])->name('admissions.filter');
    Route::resource('admissions', AdmissionController::class);
    Route::put('/admissions/{admission}/restore', [AdmissionController::class, 'restore'])->name('admissions.restore');

    Route::resource('medicationRecords', MedicationRecordController::class);
    Route::resource('medicationRecordDetails', MedicationRecordDetailController::class);
    Route::resource('medicationNotification', MedicationNotificationController::class);

    Route::resource('nurseRecords', NurseRecordController::class);
    Route::resource('nurseRecordDetails', NurseRecordDetailController::class);

    Route::get('/medication-record-details/create/{medicationRecordId}', [MedicationRecordDetailController::class, 'create'])
    ->name('medicationRecordDetails.create');
    Route::resource('medicalOrders', MedicalOrderController::class);
    Route::resource('medicalOrderDetails', MedicalOrderDetailController::class);

    Route::resource('temperatureRecords', TemperatureRecordController::class);
    Route::resource('temperatureDetails', TemperatureDetailController::class);

    Route::get('/patients/filter', [PatientController::class, 'filterPatients'])->name('patients.filter');
    Route::resource('patients', PatientController::class);

    Route::resource('Drugs', DrugController::class);
    Route::resource('Diet', DietController::class);
    Route::resource('beds', BedController::class);

    Route::get('/users/filter', [UserController::class, 'filterUsers'])->name('users.filter');
    Route::group(['middleware' => ['role:admin']], function () {
        Route::resource('users', UserController::class);
    });

    // REPORTES
    Route::get( '/reports/temperatureRecord/{id}', [ReportController::class, 'temperatureRecordReport'])
    ->name('reports.temperatureRecord');
    Route::get( '/reports/nurseRecord/{id}', [ReportController::class, 'nurseRecordReport'])
    ->name('reports.nurseRecord');
});
