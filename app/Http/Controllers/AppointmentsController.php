<?php

namespace App\Http\Controllers;

use App\Http\Resources\AppointmentsResource;
use App\Http\Resources\BookedOrCompletedAppointmentResource;
use App\Http\Resources\AppointmentDetailsResources;
use App\Http\Resources\AllAppointmentsResources;
use App\Models\Appointment;
use App\Models\Booked_Appointment;
use App\Models\Booking;
use App\Models\Doctor_Diagnosis;
use App\Models\Completed_Appointment;
use App\Models\Patient;
use App\Models\Patient_File;
use App\Models\Patients_Health_Information;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\Api_Response_Trait;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentBookedMail;
use App\Mail\AppointmentUpdatedMail;
use App\Mail\AppointmentCancelMail;
use App\Models\Patient_Health_Information;

class AppointmentsController extends Controller
{   
    use Api_Response_Trait;
    
    public function SaveBooking(Appointment $appointment, $doctor_id,  $patient_id, $patient_health_information_id,)
    {
      try{

         //dd( $patient, $patient_health_information_id,  $appointment, $doctor_id);
        $booking_create= Booked_Appointment::create([
          'appointment_id'=> $appointment->id,
          'doctor_id' => $doctor_id,
          'patient_id' => $patient_id,
          'patient_health_information_id' => $patient_health_information_id,
        ]); 
  
        if(!$booking_create)
        {
          return $this->api_response(
            'False',
            'The appointment has not been booked for the patient.',
            null,
            400 
          );  
        }

        $appointment->update(['status'=>'Booked']);

        $email_patient =  Patient::find($patient_id)->email;

        Mail::to($email_patient)->send(new AppointmentBookedMail($appointment));

        return $this->api_response(
          'True',
          'Booking created successfully.',
          null,
          201,
        );
      }
      catch(\Exception $erorr){
        
        return $this->api_response(
            'False',
            'An error occurred while booking appointment the patient: ' . $erorr->getMessage(),
            null,
            500 
        );
      }  
        
    }

    public function Show_Patient_Bookings(Request $request)
    {
      $patientId =$request->session()->get('patient_id');
      
      $my_bookings = Booked_Appointment::where('patient_id','=',$patientId)->get();

      if($my_bookings->isEmpty())
      {
          return $this->api_response(
            'False',
            'Patient bookings  not found.',
            null,
            404 
          );
      }
 
      return $this->api_response(
          'True',
          'The data has been restored successfully',
          $my_bookings,
          200,
      ); 
    }

    public function Add_Appointment(Request $request)
    {
      return view('doctors.add_appointment');
    }

    public function StoreAppointment(Request $request)
    {
      
      $doctor_id = Auth::guard('doctor')->user()->doctor_id;

      $request->validate([

        'time' => 'required',
        'status' => 'required|string|max:12',
       
        'date' => [
            'required',
            'date',
            'after_or_equal:today',
            function ($value, $fail) use ($request) {
                $exists = Appointment::where('date', $value)
                    ->where('time', $request->time)
                    ->exists();
    
                if ($exists) {
                  return $this->api_response(
                    'False',
                    'The selected time is already booked for another appointment.',
                    404 
                  );
                    
                }
            }
        ]
      ]);

      try{

        $existingAppointment = Appointment::where('doctor_id','=' ,$doctor_id)
                              ->where('date', 'Like',$request->date)
                              ->where('time','Like' ,$request->time)
                              ->first();

        if($existingAppointment != null){
 
            return $this->api_response(
              'False',
              'The appointment already exists. Please re-add another appointment.',
              null,
              404 
            );
        }
      
        $appointment_create = Appointment::create([
            'doctor_id'=> $doctor_id,
            'date'=>$request->date,
            'time'=>$request->time,
            'status'=>$request->status,
        ]);

        if(!$appointment_create){
          return $this->api_response(
              'False',
              'The appointment has not been created.',
              null,
              400
          );
        }
      
        return $this->api_response(
            'True',
            'Appointment created successfully.',
            null,
            201,
        );
      }
      catch(\Exception $erorr){
        
        return $this->api_response(
            'False',
            'An error occurred while Add Appointment : ' . $erorr->getMessage(),
            [],
            500 
        );
      }
    }

    public function ShowAppointment(Appointment $appointment)
    { 
      return $this->api_response(
        'True',
        'The data has been restored successfully.',
         new AppointmentsResource ($appointment),
        200,
        );
    }


    public function UpdateAppointment(Request $request,Appointment $appointment)
    {
      $request->validate([

        'time' => 'required',
        'status' => 'required|string|max:12',  
        'date' => [
            'required',
            'date',
            'after_or_equal:today',

            function ($attribute, $value, $fail) use ($request,$appointment) {
                $exists = Appointment::where('id','!=',$appointment->id)
                    ->where('date', $value)
                    ->where('time', $request->time)
                    ->exists();

                if($exists) {
                  $fail('The selected time is already booked for another appointment.');
                }     
            },
            function ($attribute, $value, $fail) use ($request,$appointment) {
              $no_modification = Appointment::where('id', $appointment->id)
                  ->where('date', $value)
                  ->where('time', $request->time)
                  ->exists();

              if($no_modification){
                $fail('You have not made any changes to the appointment details.');
              } 
            }
        ]
      ]);

        $appointment_data = [
            'date' => $request['date'],
            'time' => $request['time'],
            'status' => $request['status'],
        ];

        $appointment_update = $appointment->update($appointment_data);

        if(!$appointment_update)
        {
          return $this->api_response(
            'False',
            'The appointment has not been modified',
            400 
          );
        }

        if($appointment->status ==='Booked'){
              Mail::to($appointment->booked_appointment->patient->email)->send(new AppointmentUpdatedMail($appointment));
        }
        return $this->api_response(
            'True',
            'Appointment updated successfully.',
            null,
            200,
        );  
    }

    public function DeleteAppointment($type , $id)
    {      
      try{
        if ($type === 'Completed') 
        {
            $completed_appointment =  Completed_Appointment::find($id); 

            if($completed_appointment === null)
            {
              return $this->api_response(
                'False',
                'Completed appointment not found',
                null,
                404 
              );
            } 
            

            $patient_id = $completed_appointment->patient_id;
            $doctor_id = $completed_appointment->doctor_id;

            $other_booked_appointments = Booked_Appointment::where('patient_id', '=', $patient_id)
                                                            ->where('doctor_id', '=',  $doctor_id)
                                                            ->exists();

            $other_completed_appointments = Completed_Appointment::where('id', '!=', $completed_appointment->id)
                                                              ->where('patient_id', '=', $patient_id)
                                                              ->where('doctor_id', '=',  $doctor_id)
                                                              ->exists();

            if (!$other_booked_appointments && !$other_completed_appointments) {
                Patient_File::where('patient_id', '=', $patient_id)
                              ->where('doctor_id', '=',  $doctor_id)
                              ->delete();
            }

            $completed_appointment->patient_health_information->delete();
            $completed_appointment->doctor_diagnosis->delete();

        } 
        elseif ($type === 'Booked')
        { 
           $booked_appointment = Booked_Appointment::find($id);

           if($booked_appointment === null)
            {
              return $this->api_response(
                'False',
                'Booked appointment not found',
                null,
                404 
              );
            } 
            $email_patient = $booked_appointment->patient->email;
       
            
            Mail::to($email_patient)->send(new AppointmentCancelMail($booked_appointment));
            
            $patient_id = $booked_appointment->patient_id;
            $doctor_id = $booked_appointment->doctor_id;

            $patient_file = Patient_File::where('patient_id', '=', $patient_id)
                              ->where('doctor_id', '=',  $doctor_id)
                              ->exists();

            if($patient_file){

                $other_booked_appointments = Booked_Appointment::where('id', '!=', $booked_appointment->id)
                                                            ->where('patient_id', '=', $patient_id)
                                                            ->where('doctor_id', '=',  $doctor_id)
                                                            ->exists();

                $other_completed_appointments = Completed_Appointment::where('patient_id', '=', $patient_id)
                                                              ->where('doctor_id', '=',  $doctor_id)
                                                              ->exists();

                if (!$other_booked_appointments && !$other_completed_appointments) {

                    Patient_File::where('patient_id', '=', $patient_id)
                                  ->where('doctor_id', '=',  $doctor_id)
                                  ->delete();
                }

            }
            
            $booked_appointment->patient_health_information->delete();
            $booked_appointment->delete();
        }
        else
        {
          $appointment=Appointment::find($id);
           if($appointment === null)
            {
              return $this->api_response(
                'False',
                'Appointment not found',
                null,
                404 
              );
            } 
            $appointment->delete();
        }
        return $this->api_response(
          'True',
          'Appointment deleted successfully.',
          null,
          200,
        );
      }
      catch(\Exception $e){
        
        return $this->api_response(
            'False',
            'An error occurred while delete  the appointment: ' . $e->getMessage(),
            null,
            500 
          );
      }  
        
    }

    public function ShowBookedAppointments()
    {
      try{

        $doctor_id = Auth::guard('doctor')->user()->doctor_id;
        
        $booked_appointments = Booked_Appointment::where('doctor_id', '=', $doctor_id)
                                              ->whereHas('appointment', function ($query) {
                                                  $query->whereDate('date', '!=', today());
                                              })
                                              ->orderByDesc(Appointment::select('date')
                                                  ->whereColumn('appointments.id', 'booked_appointments.appointment_id')
                                              )
                                              ->paginate(10);
        if($booked_appointments->isEmpty())
        {
          return $this->api_response(
            'False',
            'Booked appointment not found',
            null,
            404 
          );
        }
        
        return $this->api_response(
            'True',
            'The data has been restored successfully.',
            BookedOrCompletedAppointmentResource::collection($booked_appointments),
            200,
        ); 
         
      }
      catch (\Exception $e) {
        return $this->api_response(
            'False',
            'An error occurred while viewing booked appointments:' . $e->getMessage(),
            null,
            500
        );
      }
    }

    public function ShowAppointmentDetails($type ,$id)
    
    {   
        try{
                             
            switch($type){
              case 'Completed':
                $appointment = Completed_Appointment::withTrashed()->findOrFail($id);
                break;
              case 'Booked':
                $appointment = Booked_Appointment::withTrashed()->findOrFail($id);
                break; 
              case 'Available':
                  return $this->api_response(
                    'False',
                    'There are no details to display..',
                    null,
                     404 
                  );
                  break; 
              default:
                  return $this->api_response(
                      'False',
                      'Invalid status provided.',
                      null,
                      400
                  );  
            }
            
            if ($appointment === null) 
            {
             
                return $this->api_response(
                  'False',
                  'Appointment not found.',
                  null,
                  404 
                );
            }
            if ($appointment->trashed()) 
            {
            
                return $this->api_response(
                  'False',
                  'To protect patient privacy, you cannot see details of an appointment deleted by a doctor.',
                  null,
                  404 
                );
            }

            return $this->api_response(
              'True',
              'The data has been restored successfully.',
              new AppointmentDetailsResources($appointment), 
              200,
            ); 
        }                       
        catch (\Exception $error) {

          return $this->api_response(
              'False',
              'An error occurred while displaying the appointment details: ' . $error->getMessage(),
              null,
              500
          );
        }
    }
    public function RestorationAppointment($type ,$id)
    {
      try{
        
        if ($type === 'Completed') 
        {
            $completed_appointment = Completed_Appointment::onlyTrashed()->find($id);  
            
            if ($completed_appointment === null) 
            {
                return $this->api_response(
                  'False',
                  'Completed appointment not found.',
                  null,
                  404 
                );
            }
            
            $completed_appointment->patients_health_information->restore();
           
            $completed_appointment->doctor_diagnosis->restore();
  
            
            $patient_id = $completed_appointment->patient_id;
            $doctor_id = $completed_appointment->doctor_id;
  
            $patient_file = Patient_File::onlyTrashed()
                          ->where('patient_id', '=', $patient_id)
                          ->where('doctor_id', '=',  $doctor_id)
                          ->restore();
             
            $completed_appointment->restore();
  
        } 
        elseif ($type === 'Booked')
        { 
           $booked_appointment = Booked_Appointment::onlyTrashed()->find($id);
           
           if ($booked_appointment === null) 
           {
              
               return $this->api_response(
                 'False',
                 'Booked appointment not found.',
                 null,
                 404 
               );
           }
  
          
           $booked_appointment->patient_health_information->restore();;
           $booked_appointment->restore();

           $patient_id = $booked_appointment->patient_id;
           $doctor_id = $booked_appointment->doctor_id;
  
            $patient_file = Patient_File::onlyTrashed()
                          ->where('patient_id', '=', $patient_id)
                          ->where('doctor_id', '=',  $doctor_id)
                          ->restore();
        }
        else
        {
           $appointment = Appointment::onlyTrashed()->find($id);
           if ($appointment === null) 
           {
              return $this->api_response(
                 'False',
                 'Appointment not found.',
                 null,
                 404 
              );
           }
           $appointment->restore();
        }
  
       
        return $this->api_response(
          'True',
          'The Appointment has been successfully restored.',
          null,
           200,
        );
      }
      catch(\Exception $erorr){
        
        return $this->api_response(
            'False',
            'An error occurred while restoration the appointment: ' . $erorr->getMessage(),
            null,
            500 
        );
      }
    }
  
    public function DeletePermanentlyAppointment($type ,$id)
    {
      try{
        if ($type === 'Completed') 
        {
            $completed_appointment = Completed_Appointment::onlyTrashed()->find($id);  
            
            if ($completed_appointment === null) 
            {
               return $this->api_response(
                  'False',
                  'Completed appointment not found.',
                  null,
                  404 
               );
            }
            
            $completed_appointment->patients_health_information->forceDelete();    
            
            $completed_appointment->doctor_diagnosis->forceDelete();    
  
           
            $patient_id = $completed_appointment->patient_id;
            $doctor_id = $completed_appointment->doctor_id;
  
            $patient_file = Patient_File::onlyTrashed()
                          ->where('patient_id', '=', $patient_id)
                          ->where('doctor_id', '=',  $doctor_id)
                          ->forceDelete();    
             
            $completed_appointment->forceDelete();    
  
        } 
        elseif ($type === 'Booked')
        { 
           $booked_appointment = Booked_Appointment::onlyTrashed()->find($id);
  
           if ($booked_appointment === null) 
            {
               return $this->api_response(
                  'False',
                  'Booked appointment not found.',
                  null,
                  404 
               );
            }

           $patient_id = $booked_appointment->patient_id;
           $doctor_id = $booked_appointment->doctor_id;
  
           $patient_file = Patient_File::onlyTrashed()
                          ->where('patient_id', '=', $patient_id)
                          ->where('doctor_id', '=',  $doctor_id)
                          ->forceDelete();    
              
           $booked_appointment->patient->patient_health_information->forceDelete();    
           $booked_appointment->forceDelete();    
        }
        else
        {
           $appointment = Appointment::onlyTrashed()->find($id);  
           if ($appointment === null) 
            {
               return $this->api_response(
                  'False',
                  'Appointment not found.',
                  null,
                  404 
               );
            }  
            $appointment->forceDelete();  
        }
      
        return $this->api_response(
          'True',
          'The Appointment has been successfully delete permanently.',
           null,
           200,
        );
      }
      catch(\Exception $erorr){
        
        return $this->api_response(
            'False',
            'An error occurred while delete permanently the appointment: ' . $erorr->getMessage(),
            null,
            500 
        );
      }
    }

    public function ShowBookedAppointmentsToday()
    {  
      try{
          $doctor_id = Auth::guard('doctor')->user()->doctor_id;

        
          $appointments_booked_today = Booked_Appointment::where('doctor_id','=',$doctor_id)
                                      ->whereHas('appointment', function ($query) {
                                              $query->whereDate('date', today());
                                      })
                                      ->orderByDesc(Appointment::select('date')
                                                  ->whereColumn('appointments.id', 'booked_appointments.appointment_id')
                                              )
                                      ->with('appointment', 'patient')
                                      ->paginate(10);
  
          if ($appointments_booked_today->isEmpty()) 
          {
            return $this->api_response(
                'False',
                'Booked appointments today not found.',
                null,
                404 
            );
          }  
    
          $booked_first_type = Booked_Appointment::where('doctor_id','=',$doctor_id)
                                    ->whereHas('appointment', function ($query) {
                                      $query->whereDate('date', Today());
                                    })
                                      ->whereHas('patient_health_information', function ($query) {
                                        $query->where('visit_type', 'like', '%'.'استشارة'.'%');
                                    })
                                    ->get()->Count();
  
          $booked_Notfirst_type = Booked_Appointment::where('doctor_id','=',$doctor_id)
                                      ->whereHas('appointment', function ($query) {
                                        $query->whereDate('date', Today());
                                      })
                                        ->whereHas('patient_health_information', function ($query) {
                                          $query->where('visit_type', 'like', '%'.'فحص دوري'.'%');
                                      })
                                      ->get()->Count();
  
          $booked_review_type = Booked_Appointment::where('doctor_id','=',$doctor_id)
                                      ->whereHas('appointment', function ($query) {
                                        $query->whereDate('date', Today());
                                      })
                                        ->whereHas('patient_health_information', function ($query) {
                                          $query->where('visit_type', 'like', '%'.'مراجعة'.'%');
                                      })
                                      ->get()->Count();

          return $this->api_response(
            'True',
            'The data has been restored successfully.',
            [
              BookedOrCompletedAppointmentResource::collection($appointments_booked_today),
              'booked_first_type' => $booked_first_type,
              'booked_Notfirst_type' => $booked_Notfirst_type,
              'booked_review_type' => $booked_review_type,
            ],
            200,
          );
        }   
        catch(\Exception $erorr){
        
          return $this->api_response(
              'False',
              'An error occurred while show the booked  appointment today: ' . $erorr->getMessage(),
              null,
              500 
          );
        }
     }
     public function ShowCompletedAppointments()
     {
        try{
            $doctor_id = Auth::guard('doctor')->user()->doctor_id;

            $completed_appointments = Completed_Appointment::where('doctor_id','=',$doctor_id)
                                          ->orderByDesc(Appointment::select('date')
                                             ->whereColumn('appointments.id', 'completed_appointments.appointment_id')
                                          )
                                          ->paginate(10);
  
            if ($completed_appointments->isEmpty()) 
            {
              return $this->api_response(
                  'False',
                  'Completed appointment not found.',
                  null,
                  404 
              );
            }
            return $this->api_response(
                  'True',
                  'The data has been restored successfully.',
                  BookedOrCompletedAppointmentResource::collection($completed_appointments),
                  200,
            );
          }
          catch(\Exception $erorr){
        
            return $this->api_response(
                'False',
                'An error occurred while show the completed appointment : ' . $erorr->getMessage(),
                null,
                500 
            );
          }
      } 

     public function ShowAllAppointments()
     {
        try{
              $appointments = Appointment::withTrashed()->with('booked_appointment','completed_appointment')->OrderByDesc('created_at')->paginate(10);
            
              if ($appointments->isEmpty()) 
              {
                return $this->api_response(
                    'False',
                    'Appointments not found.',
                    null,
                    404 
                );
              }

              return $this->api_response(
                  'True',
                  'The data has been restored successfully.',
                  AllAppointmentsResources::collection($appointments),
                  200,
              );
              
          }
          catch (\Exception $e) {
            return $this->api_response(
                'False',
                'An error occurred while show the all appointments : ' . $e->getMessage(),
                null,
                500
            );
          }
      } 
       
}
