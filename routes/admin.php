<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\DoctorsController;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\DoctorAuth\AuthenticatedSessionController;
use App\Models\Doctor;
use Illuminate\Support\Facades\Route;


Route::prefix('admin')->name('admin.')->group(function(){

    //To access this page, you must log in 
    //and guard=driver, so a middleware is created
    Route::middleware('IsAdmin')->group(function(){

        Route::get('/dashboard', [AdminController::class,'Dashboard'])
             ->name('dashboard');
        
        Route::get('/weekly/report', [AdminController::class,'WeeklyReport'])
             ->name('weekly.report');
        
        Route::get('/monthly/report', [AdminController::class,'MonthlyReport'])
             ->name('monthly.report');


        Route::get('/doctors', [DoctorsController::class,'ShowAllDoctors'])
             ->name('doctors');

        Route::get('/doctor/profile/{doctor}', [DoctorsController::class,'ShowDoctorProfile'])
             ->name('doctor.profile'); 
             
        Route::get('/edit/doctor/profile/{doctor}', [DoctorsController::class,'EditDoctorProfile'])
             ->name('edit.doctor.profile');

        Route::put('/update/doctor/profile/{doctor}', [DoctorsController::class,'UpdateDoctorProfile'])
             ->name('update.doctor.profile');  
             
        Route::delete('/delete/doctor/profile/{id}', [DoctorsController::class,'DeleteDoctorProfile'])
             ->name('delete.doctor.profile');

       Route::get('/restoration/doctor/profile/{id}', [DoctorsController::class,'RestorationDoctorProfile'])
             ->name('restoration.doctor.profile');

       Route::delete('/delete/permanently/doctor/profile/{id}', [DoctorsController::class,'DeletePermanentlyDoctorProfile'])
             ->name('delete.permanently.doctor.profile');

        Route::get('/patient/files', [PatientsController::class,'ShwoAllPatientFiles'])
             ->name('patient.files');
             
        Route::get('/restoration/patient/file/{id}', [PatientsController::class,'RestorationPatientFile'])
             ->name('restoration.patient.file');

        Route::delete('/delete/patient/file/{id}', [PatientsController::class,'DeletePermanentlyPatientFile'])
             ->name('delete.permanently.patient.file'); 

        Route::get('/show/patient/file/{id}', [PatientsController::class,'ShowPatientFile'])
             ->name('show.patient.file');
       
        Route::get('/appointment/details/{type}/{id}', [AppointmentsController::class, 'ShowAppointmentDetails'])
             ->name('appointment.details');
              

        Route::get('/restoration/appointment/{type}/{id}', [AppointmentsController::class,'RestorationAppointment'])
             ->name('restoration.appointment');

        Route::delete('/delete/permanently/appointment/{type}/{id}', [AppointmentsController::class,'DeletePermanentlyAppointment'])
             ->name('delete.permanently.appointment');

        Route::get('/appointments', [AppointmentsController::class,'ShowAllAppointments'])
             ->name('appointments'); 

        Route::get('/search/appointment', [AdminController::class,'SearchAppointment'])
             ->name('search.appointment');     

        Route::get('/filter/by/status', [AdminController::class,'FilterByStatus'])
             ->name('filter.by.status');  

        Route::get('/search/doctor', [AdminController::class,'SearchDoctor'])
             ->name('filter.doctor');  

        Route::get('/filter/by/governorate', [AdminController::class,'FilterByGovernorate'])
             ->name('filter.by.governorate');     
             
        Route::get('/search/patient/files', [AdminController::class,'SearchPatientFiles'])
             ->name('filter.patient.files'); 

        Route::get('/filter/by/date', [AdminController::class,'FilterByDate'])
             ->name('search.by.date');
             
   
    });
});
