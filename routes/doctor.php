<?php

use App\Http\Controllers\AppointmentsController;
use App\Http\Controllers\DoctorsController;
use App\Http\Controllers\PatientsController;
use App\Models\Doctor;
use Illuminate\Support\Facades\Route;


Route::prefix('doctor')->name('doctor.')->group(function(){

    //To access this page, you must log in 
    //and guard=driver, so a middleware is created
    Route::middleware('IsDoctor')->group(function(){

          Route::get('/profile',[DoctorsController::class,'Showprofile'])
               ->name('profile');

          Route::get('/account/information',[DoctorsController::class,'ShowAccountInformation'])
               ->name('account.information');

          // Route::get('/add/appointment',[AppointmentsController::class,'Add_Appointment'])
          //      ->name('add.appointment');

          Route::post('/store/appointment',[AppointmentsController::class,'StoreAppointment'])
               ->name('store.appointment')
               ->middleware('check_appointments_created');

          Route::get('/edit/appointment/{appointment}',[AppointmentsController::class,'ShowAppointment'])
               ->name('edit.appointment');
               
          Route::put('/update/appointment/{appointment}',[AppointmentsController::class,'ShowAppointment'])
               ->name('update.appointment');
 
          Route::DELETE('/delete/appointment/{type}/{id}',[AppointmentsController::class,'DeleteAppointment'])
               ->name('delete.appointment');

          Route::get('/booked/appointments',[AppointmentsController::class,'ShowBookedAppointments'])
               ->name('booked.appointments');
                   
          Route::get('/booked/appointments/today',[AppointmentsController::class,'ShowBookedAppointmentsToday'])
               ->name('booked.appointments.today'); 
          
          Route::get('/add/diagnosis/{booked_appointment}',[PatientsController::class,'AddDiagnosis'])
               ->name('add.diagnosis'); 

          Route::post('/store/diagnosis/{booked_appointment}',[PatientsController::class,'StoreDiagnosis'])
               ->name('store.diagnosis')
               ->middleware('check_appointments_completed'); 
               
          Route::get('/completed/appointments',[AppointmentsController::class,'ShowCompletedAppointments'])
               ->name('completed.appointments');       
               

          Route::get('/edit/doctor/diagnosis/{doctor_diagnosis}',[PatientsController::class,'EditDoctorDiagnosis'])
               ->name('edit.doctor.diagnosis');

          Route::put('/update/doctor/diagnosis/{doctor_diagnosis}',[PatientsController::class,'UpdateDoctorDiagnosis'])
               ->name('update.doctor.diagnosis');
               
          Route::get('/appointment/details/{type}/{id}', [AppointmentsController::class, 'ShowAppointmentDetails'])
               ->name('appointment.details');     
               
          Route::get('/patient/files',[PatientsController::class,'ShowPatientFiles'])
               ->name('patient.files');

          Route::delete('/delete/patient/file/{patient_file}',[PatientsController::class,'DeletePatientFile'])
               ->name('delete.patient.file');     
  
          Route::get('/patient/file/{patient_file}',[PatientsController::class,'ShowPatient'])
               ->name('patient.file');

          Route::get('/search/patient',[DoctorsController::class,'SearchPatient'])
               ->name('search.patient'); 
               
          Route::get('/search/booked/appointment',[DoctorsController::class,'SearchBookedAppointment'])
               ->name('search.booked');

          Route::get('/search/booked/appointment/today',[DoctorsController::class,'SearchBookedAppointmentToday'])
               ->name('search.booked.today');

          Route::get('/search/completed/appointment',[DoctorsController::class,'searchCompletedAppointment'])
               ->name('search.completed');

          Route::get('/filter/by/date',[DoctorsController::class,'FilterByDate'])
               ->name('filter.by.date'); 

          Route::get('/filter/by/booked',[DoctorsController::class,'FilterByBooked'])
               ->name('filter.by.booked');

          Route::get('/filter/by/booked/today',[DoctorsController::class,'FilterByBookedToday'])
               ->name('filter.by.booked.today');

          Route::get('/filter/by/completed',[DoctorsController::class,'FilterByCompleted'])
               ->name('filter.by.completed');     
               
          Route::get('/recycle-bin',[DoctorsController::class,'RecycleBin'])
               ->name('recycle.bin');          

          
               
    });

});
