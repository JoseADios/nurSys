<?php

use App\Http\Controllers\AdmissionController;
use App\Http\Controllers\MedicalOrderController;
use App\Http\Controllers\MedicalOrderDetailController;
use App\Http\Controllers\MedicationRecordController;
use App\Http\Controllers\NurseRecordController;
use App\Http\Controllers\NurseRecordDetailController;
use App\Http\Controllers\TemperatureRecordController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::resource('admissions', AdmissionController::class);
    Route::resource('medicationRecords', MedicationRecordController::class);
    Route::resource('nurseRecords', NurseRecordController::class);
    Route::resource('nurseRecordDetails', NurseRecordDetailController::class);
    Route::resource('medicalOrders', MedicalOrderController::class);
    Route::resource('medicalOrderDetails', MedicalOrderDetailController::class);
    Route::resource('temperatureRecords', TemperatureRecordController::class);

});
