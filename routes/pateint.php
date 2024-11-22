<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\DoctorsController;
use App\Http\Controllers\PatientsController;
use App\Models\Doctor;
use Illuminate\Support\Facades\Route;



Route::middleware('Check_visits')->prefix('patient')->name('patient.')->group(function(){

      Route::get('/specialties',[DoctorsController::class,'ShowSpecialties'])
             ->name('specialties');

    Route::get('/show/doctors/{specialty_id}',[DoctorsController::class,'ShowDoctors'])
           ->name('show.doctors');

    Route::get('/search/doctors/{specialty_id}',[PatientsController::class,'SearchDoctors'])
           ->name('search.doctors');

    Route::get('/filter/by/governorate/{specialty_id}',[PatientsController::class,'FilterByGovernorate'])
           ->name('filter.by.governorate');


    Route::get('/page/doctor/{doctor}',[DoctorsController::class,'ShowPageDoctor'])
              ->name('page.doctor');

    Route::get('/create/appointment_patient/{appointment}',[PatientsController::class,'CreateAppointmentPatient'])
          ->name('create.appointment.patient');

    Route::post('/store/patient/{appointment}',[PatientsController::class,'StoreAppointmentPatient'])
           ->name('store.appointment.patient');

    Route::get('/save/booking/{appointment}/{doctor_id}/{patient_id}/{patient_health_information_id}', [AppointmentsController::class, 'SaveBooking'])
          ->name('save.booking')
          ->middleware('check_appointments_booked');

//     Route::get('/patient/bookings', [AppointmentsController::class, 'Show_Patient_Bookings'])
//             ->name('patient.bookings');

//     Route::get('/edit/patient/information/{booking}',[PatientsController::class,'Edit_Patient_Information'])
//     ->name('edit.patient.information');
    
//     Route::post('/update/patient/information/{booking}',[PatientsController::class,'Update_Patient_Information'])
//     ->name('update.patient.information');
    
//     Route::get('/patient/appointment/details/{booking}',[PatientsController::class,'Show_Patient_Appointment_Details'])
//     ->name('patient.appointment.details');
    //Route::resource('patients',PatientsController::class);
});

