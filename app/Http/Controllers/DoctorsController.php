<?php

namespace App\Http\Controllers;

use App\Http\Resources\DoctorsResource;
use App\Http\Resources\DoctorResource;
use App\Http\Resources\DoctorProfileResource;
use App\Http\Resources\AppointmentsResource;
use App\Http\Resources\PatientFilesResources;
use App\Http\Resources\SpecialtiesResource;
use App\Http\Resources\BookedOrCompletedAppointmentResource;
use App\Models\Appointment;
use App\Models\Booked_Appointment;
use App\Models\Doctor;
use App\Models\Doctor_Account;
use App\Models\Doctor_Diagnosis;
use App\Models\Completed_Appointment;
use App\Models\Patient;
use App\Models\Patient_File;
use App\Models\Patient_Health_Information;
use App\Models\Specialty;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Symfony\Component\VarDumper\Caster\DoctrineCaster;
use App\Traits\Api_Response_Trait;
use Exception;

class DoctorsController extends Controller
{
  use Api_Response_Trait;
  
  public function ShowSpecialties(Request $request )
  {

    try{

        $Specialties =  Specialty::all();

        if ($Specialties->isEmpty()) 
        {
          return $this->api_response(
            'False',
            'Specialties not found.',
            null,
            404 
          );
        }
        return $this->api_response(
            'True',
            'The data has been restored successfully.',
            SpecialtiesResource::collection($Specialties),
            200,
        );
    }
    catch (\Exception $e) {
      return $this->api_response(
          'False',
          'An error occurred while show the specialties : ' . $e->getMessage(),
          null,
          500
      );
    }

    
  }
  
  public function ShowDoctors( $specialty_id)
  { 
    try{
        $doctors = Doctor::where('specialty_id',"=", $specialty_id)
                           ->paginate(5);

        if ($doctors->isEmpty()) 
        {
          return $this->api_response(
              'False',
              'Doctors not found.',
              null,
              404 
          );
        }
    
        return $this->api_response(
            'True',
            'The data has been restored successfully.',
            DoctorsResource::collection($doctors),
            200,
        );
      }
    
      catch (\Exception $e) {
        return $this->api_response(
            'False',
            'An error occurred while show the doctors : ' . $e->getMessage(),
            null,
            500
        );
      }
  }

  public function ShowPageDoctor(Doctor $doctor)
  {
    try{

      $doctor_profile = Doctor::find($doctor->id);

      if ($doctor_profile === null) 
      {
        return $this->api_response(
            'False',
            'Page doctor profile not found.',
            null,
            404 
        );
      }
    
      $appointments = Appointment::where('doctor_id','=',$doctor->id)
                    ->where('status','like','Available')
                    ->OrderByDesc('date')
                    ->paginate(5);

      if ($appointments->isEmpty()) 
      {
        return $this->api_response(
            'True',
            'Available appointments doctor not found.',
            new DoctorResource($doctor_profile),
            200 
        );
      } 
      return $this->api_response(
          'True',
          'The data has been restored successfully.',
          [
            new DoctorResource($doctor_profile),
            AppointmentsResource::Collection($appointments)
          ],
          200,
      );  

    }
    catch (\Exception $e) {
      return $this->api_response(
          'False',
          'An error occurred while show the page doctor : ' . $e->getMessage(),
          null,
          500
      );
    }
    
    
  }
  
  public function ShowProfile()
  {
    try{
        $doctor_profile = Auth::guard('doctor')->user();
        
        $appointments = Appointment::where('doctor_id','=',$doctor_profile->doctor_id)
                                    ->where('status','Like','Available')
                                    ->orderByDesc('date')
                                    ->paginate(5);

        if ($appointments->isEmpty()) 
        {
          return $this->api_response(
            'True',
            'The data has been restored successfully,and Available Appointments not found.',
            new DoctorProfileResource($doctor_profile) ,
            200 
          );
        }

        return $this->api_response(
            'True',
            'The data has been restored successfully.',
            [
              new DoctorProfileResource($doctor_profile),
             AppointmentsResource::collection($appointments),
            ],
            200,
        ); 
    }
    catch (\Exception $e) {
      return $this->api_response(
          'False',
          'An error occurred while show profile : ' . $e->getMessage(),
          null,
          500
      );
    }  
  }
  public function ShowAccountInformation(Doctor $doctor)
  {
      $doctor = Auth::guard('doctor')->user(); 

      return $this->api_response(
        'True',
        'The data has been restored successfully.',
        new DoctorResource($doctor),
        200 
      );
  }
  
  public function SearchPatient(Request $request)
  {
    try{

      $request->validate([
        "search"=> "required|string|min:3|max:50"
      ]);

      $search= $request->search;

      $doctor_id = Auth::guard('doctor')->user()->doctor_id;
 
      $patient_files = Patient_File::Where('doctor_id', '=', $doctor_id )
                                    ->WhereHas('Patient',function($query) use ($search) {
                                           $query->where('name', 'like', '%' . $search . '%')
                                           ->orWhere('age', 'like', '%' . $search . '%')
                                           ->orWhere('phone', 'like', '%' . $search . '%')
                                           ->orWhere('email', 'like', '%' . $search . '%')
                                           ->orWhere('address', 'like', '%'. $search . '%'); 
                                    })
                                    ->paginate(10);
      if ($patient_files->isEmpty()) 
      {
          return $this->api_response(
            'False',
            'There are no results matching your search.',
            null,
            404 
         );
      } 

        return $this->api_response(
          'True',
          'The data has been restored successfully.',
          [
            'patient_files' => PatientFilesResources::collection($patient_files) ,
            'search' =>$search
          ],
          200,
        ); 
    } 
    catch (\Exception $e) {
      return $this->api_response(
          'False',
          'An error occurred while searching: ' . $e->getMessage(),
          null,
          500
      );
    }    
                             
     
     
  }
  public function searchBookedAppointment(Request $request)
  {
   try{

      $request->validate([
        "search"=> "required|string|min:3|max:50"
      ]);

      $search = $request->search;
      $doctorId = Auth::guard('doctor')->user()->doctor_id;

      $booked_appointments = Booked_Appointment::where('doctor_id','=',$doctorId)
                                  ->where(function($query) use ($search) {
                                      $query->WhereHas('appointment', function($q) use ($search) {
                                        $q->where('date', 'like', '%' . $search . '%')
                                          ->orWhere('time', 'like', '%' . $search . '%');
                                      })

                                        ->orWhereHas('patient', function($q) use ($search) {
                                                $q->where('name', 'like', '%' . $search . '%')
                                                  ->orWhere('age', 'like', '%' . $search . '%')
                                                  ->orWhere('phone', 'like', '%' . $search . '%')
                                                  ->orWhere('email', 'like', '%' . $search . '%')
                                                  ->orWhere('address', 'like', '%' . $search . '%');
                                                });
                                  })
                                  ->paginate(10);

      if ($booked_appointments->isEmpty()) 
      {
        return $this->api_response(
            'False',
            'There are no results matching your search.',
            null,
            404 
        );
      } 
 
      return $this->api_response(
        'True',
        'The data has been restored successfully.',
        [
            'Booked Appointments' => BookedOrCompletedAppointmentResource::collection($booked_appointments),
            'search' =>$search
        ],
          200,
      );
   }
   catch (\Exception $e) {
      return $this->api_response(
        'False',
        'An error occurred while searching by booked: ' . $e->getMessage(),
        null,
        500
      );
   }    
    

  }
  public function searchBookedAppointmentToday(Request $request)
{
  $request->validate([
    "search"=> "required|string|min:3|max:50"
  ]); 

  try{
      $search = $request->search;
      $doctorId = Auth::guard('doctor')->user()->doctor_id;

      $appointments_booked_today = Booked_Appointment::where('doctor_id', '=', $doctorId)
          ->whereHas('appointment', function($q) {
              $q->whereDate('date', '=', today()); 
          })

          ->where(function($query) use ($search) {
              $query->WhereHas('appointment', function($q) use ($search) {
                  $q->where('time', 'like', '%' . $search . '%'); 
              })

          ->orWhereHas('patient', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                          ->orWhere('age', 'like', '%' . $search . '%')
                          ->orWhere('phone', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%')
                          ->orWhere('address', 'like', '%' . $search . '%');
                        });

          })
          ->paginate(10);

      if ($appointments_booked_today->isEmpty()) 
      {
        return $this->api_response(
          'False',
          'There are no results matching your search.',
          null,
          404 
        );
      } 
      
      
      return $this->api_response(
          'True',
          'The data has been restored successfully.',
          [
            'appointments_booked_today' => BookedOrCompletedAppointmentResource::collection($appointments_booked_today),
            'search' =>$search
          ],
          200,
      );
  }
  catch (\Exception $e) {
    return $this->api_response(
      'False',
      'An error occurred while searching : ' . $e->getMessage(),
      null,
      500
    );
  }      
 
}
  public function SearchCompletedAppointment(Request $request)
  {
    $request->validate([
      "search"=> "required|string|min:3|max:50"
    ]);

    try{
   
      $search= $request->search;
      $doctorId = Auth::guard('doctor')->user()->doctor_id;

      $completed_appointments =   Completed_Appointment::where('doctor_id','=',$doctorId)
          ->where(function($query) use ($search) {
              $query->WhereHas('appointment', function($q) use ($search) {
                $q->where('date', 'like', '%' . $search . '%')
                  ->orWhere('time', 'like', '%' . $search . '%');
              })

                ->orWhereHas('patient', function($q) use ($search) {
                        $q->where('name', 'like', '%' . $search . '%')
                          ->orWhere('age', 'like', '%' . $search . '%')
                          ->orWhere('phone', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%')
                          ->orWhere('address', 'like', '%' . $search . '%');
                        })
                ->orWhereHas('patient_health_information', function($q) use ($search) {
                        $q->where('chronic_diseases', 'like', '%' . $search . '%')
                          ->orWhere('previous_surgeries', 'like', '%' . $search . '%')
                          ->orWhere('permanent_medications', 'like', '%' . $search . '%')
                          ->orWhere('current_disease_symptoms', 'like', '%' . $search . '%')
                          ->orWhere('visit_type', 'like', '%' . $search . '%');
                        });
          })
          ->paginate(10);

      if ($completed_appointments->isEmpty()) 
      {
        return $this->api_response(
          'False',
          'There are no results matching your search.',
          null,
          404 
        );
      } 

      return $this->api_response(
          'True',
          'The data has been restored successfully.',
          [
            'completed_appointments' =>BookedOrCompletedAppointmentResource::collection($completed_appointments),
            'search' =>$search
          ],
          200,
      );
    }
    catch (\Exception $e) {
      return $this->api_response(
        'False',
        'An error occurred while searching : ' . $e->getMessage(),
        null,
        500
      );
    }  
  }
  
  public function FilterByDate(Request $request)
  {
    $request->validate([
      "filter"=> "required|string|min:3|max:50"
    ]);

    try{
          $filter = $request->input('filter');
          $doctorId = Auth::guard('doctor')->user()->doctor_id;
      
          switch($filter){

            case 'Last 5 Dayago':
              $fiveDaysAgo = Carbon::now()->subDays(5);
              $patient_files = Patient_File::where('doctor_id','=',$doctorId)
                                          ->whereDate('created_at', '>=', $fiveDaysAgo)->paginate(10);
            break;
            case 'Last 10 Dayago':
              $teneDaysAgo = Carbon::now()->subDays(10);
              $patient_files = Patient_File::where('doctor_id','=',$doctorId)
                                            ->whereDate('created_at', '>=', $teneDaysAgo)->paginate(10);
            break;
            case 'Last 30 Dayago':
              $monthDaysAgo = Carbon::now()->subDays(30);
              $patient_files = Patient_File::where('doctor_id','=',$doctorId)
                                          ->whereDate('created_at', '>=', $monthDaysAgo)->paginate(10);
            break;
            default:
              return $this->api_response(
                'False',
                'Invalid status provided.',
                null,
                400
              );                                       
          }

          if ($patient_files->isEmpty()) 
          {
            return $this->api_response(
              'False',
              'There are no results matching your search.',
              null,
              404 
            );
          } 
        
        return $this->api_response(
            'True',
            'The data has been restored successfully.',
            [
              'patient_files' => PatientFilesResources::collection($patient_files),
              'filter' =>$filter
            ],
            200,
        );   
    }
    catch(\Exception $erorr){
        
      return $this->api_response(
          'False',
          'An error occurred while search by date: ' . $erorr->getMessage(),
          null,
          500
      );
    }
  }
  public function FilterByBooked(Request $request)
  {
    $request->validate([
      "filter"=> "required|string|min:3|max:50"
    ]);

    try{
      $filter = $request->input('filter');
      $doctorId = Auth::guard('doctor')->user()->doctor_id;
      $Today = Carbon::now();

       ///////////////
      switch($filter){

        case 'In the next 5 days':
            $fiveDaysNext = Carbon::now()->addDays(5);
            $booked_appointments = Booked_Appointment::where('doctor_id','=',$doctorId)
                  ->where(function($query)  use ($Today,$fiveDaysNext) {
                        $query->orWhereHas('appointment', function($q) use ($Today,$fiveDaysNext) {
                            $q->whereBetween('date', [$Today,$fiveDaysNext]);
                         });
                  })->paginate(10);
        break;
        case 'In the next 10 days':
            $teneDaysNext = Carbon::now()->addDays(10);
            $booked_appointments = Booked_Appointment::where('doctor_id','=',$doctorId)
                ->where(function($query)  use ($Today,$teneDaysNext) {
                      $query->orWhereHas('appointment', function($q) use ($Today,$teneDaysNext) {
                          $q->whereBetween('date', [$Today,$teneDaysNext]);
                       });
                })->paginate(10);
        break;
        case 'In the next 15 days':
            $FifteenDaysNext = Carbon::now()->addDays(15);
            $booked_appointments = Booked_Appointment::where('doctor_id','=',$doctorId)
                ->where(function($query)  use ($Today,$FifteenDaysNext) {
                      $query->orWhereHas('appointment', function($q) use ($Today,$FifteenDaysNext) {
                          $q->whereBetween('date', [$Today,$FifteenDaysNext]);
                       });
                })->paginate(10);
        break;
        case 'In the next 30 days':
          $monthDaysNext = Carbon::now()->addDays(30);
          $booked_appointments = Booked_Appointment::where('doctor_id','=',$doctorId)
                ->where(function($query)  use ($Today,$monthDaysNext) {
                    $query->orWhereHas('appointment', function($q) use ($Today,$monthDaysNext) {
                        $q->whereBetween('date', [$Today,$monthDaysNext]);
                     });
                })->paginate(10);
        break;
        default:
          return $this->api_response(
            'False',
            'Invalid status provided.',
            null,
            400
          );                                       
      }
      //////
      
      if ($booked_appointments->isEmpty()) 
      {
        return $this->api_response(
          'False',
          'There are no results matching your search..',
          null,
          404 
        );
      } 
     
      return $this->api_response(
          'True',
          'The data has been restored successfully.',
          [
            'booked_appointments' => BookedOrCompletedAppointmentResource::collection($booked_appointments),
            'filter' =>$filter
          ],
          200,
      ); 
     
    }
    catch(\Exception $erorr){
        
      return $this->api_response(
          'False',
          'An error occurred while search by booked:: ' . $erorr->getMessage(),
          null,
          500 
      );
    }
  }
  public function FilterByBookedToday(Request $request)
  {
    $request->validate([
      "filter"=> "required|string|min:3|max:50"
    ]);

    try{
      $filter = $request->input('filter');
      $doctorId = Auth::guard('doctor')->user()->doctor_id;
      
      
      $appointments_booked_today = Booked_Appointment::where('doctor_id','=',$doctorId)
                                    ->whereHas('appointment', function ($query) {
                                      $query->whereDate('date', Today());
                                    })
                                    ->whereHas('patient_health_information', function ($query) use($filter) {
                                      $query->where('visit_type', 'like', $filter);
                                    })
                                    ->paginate(10);
      
      if ($appointments_booked_today->isEmpty()) 
      {
        return $this->api_response(
          'False',
          'There are no results matching your search..',
          null,
          404 
        );
      } 
        return $this->api_response(
          'True',
          'The data has been restored successfully.',
          [
            'appointments_booked_today' => BookedOrCompletedAppointmentResource::collection($appointments_booked_today),
            'search_by_booked_today' =>$filter
          ],
          200,
        );
    }
    catch(\Exception $erorr){
        
      return $this->api_response(
          'False',
          'An error occurred while search by booked today: ' . $erorr->getMessage(),
          null,
          500 
      );
    }
  }
  public function FilterByCompleted(Request $request)
  {
    $request->validate([
      "filter"=> "required|string|min:3|max:50"
    ]);

    try{
      $filter = $request->input('filter');
      $doctorId = Auth::guard('doctor')->user()->doctor_id;
      $Today = Carbon::now();
       /////////
      switch($filter){

          case 'Last 5 Dayago':
              $fiveDaysAgo = Carbon::now()->subDays(5);
              $completed_appointments = Completed_Appointment::where('doctor_id','=',$doctorId)
              ->where(function($query)  use ($Today,$fiveDaysAgo) {
                    $query->orWhereHas('appointment', function($q) use ($Today,$fiveDaysAgo) {
                        $q->whereBetween('date', [$Today,$fiveDaysAgo]);
                    });
              })->paginate(10);
          break;
          case 'Last 10 Dayago':
              $teneDaysAgo = Carbon::now()->subDays(10);
              $completed_appointments =   Completed_Appointment::where('doctor_id','=',$doctorId)
                    ->where(function($query)  use ($Today,$teneDaysAgo) {
                          $query->orWhereHas('appointment', function($q) use ($Today,$teneDaysAgo) {
                              $q->whereBetween('date', [$Today,$teneDaysAgo]);
                          });
                    })->paginate(10);
          break;
          case 'Last 15 Dayago':
              $FifteenDaysAgo = Carbon::now()->subDays(10);
              $completed_appointments = Completed_Appointment::where('doctor_id','=',$doctorId)
                    ->where(function($query)  use ($Today,$FifteenDaysAgo) {
                          $query->orWhereHas('appointment', function($q) use ($Today,$FifteenDaysAgo) {
                              $q->whereBetween('date', [$Today,$FifteenDaysAgo]);
                          });
                    })->paginate(10);
          break;
          case 'Last 30 Dayago':

            $monthDaysAgo = Carbon::now()->subDays(30);
            $completed_appointments = Completed_Appointment::where('doctor_id','=',$doctorId)
              ->where(function($query)  use ($Today,$monthDaysAgo) {
                $query->orWhereHas('appointment', function($q) use ($Today,$monthDaysAgo) {
                    $q->whereBetween('date', [$Today,$monthDaysAgo]);
                });
              })->paginate(10);

          break;
          default:
            return $this->api_response(
              'False',
              'Invalid status provided.',
              null,
              400
            );                                       
      }
       //////////
      
      if ($completed_appointments->isEmpty()) 
      {
        return $this->api_response(
          'False',
          'There are no results matching your search.',
          null,
          404 
        );
      } 
    
        return $this->api_response(
          'True',
          'The data has been restored successfully.',
          [
            'completed_appointments' =>BookedOrCompletedAppointmentResource::collection($completed_appointments),
            'filter' =>$filter
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

  public function Add_Doctor(Request $request)
  {
    $specialties = Specialty::all();
  
    return $this->api_response(
      'True',
      'The data has been restored successfully.',
      $specialties,
      200,
    );
  }
  
  public function ShowAllDoctors(Request $request)
  { 
      $doctors = Doctor::withTrashed()->OrderByDesc('created_at')->paginate(10);
      if ($doctors->isEmpty()) 
      {
        return $this->api_response(
          'False',
          "Doctors not found.",
          null,
          401
        );
      } 

     
      return $this->api_response(
        'True',
        'The data has been restored successfully.',
        DoctorsResource::collection($doctors),
        200,
      );

     
  }
  public function ShowDoctorProfile(Doctor $doctor)
  { 
    try{
        
        $appointments = Appointment::withTrashed()->where('doctor_id',"=",$doctor->id)
                                          ->paginate(5);

        $available_count = Appointment::where('doctor_id','=',$doctor->id)
                                  ->where('status', 'like', 'Available')->count();
        $booking_count =  Appointment::where('doctor_id','=',$doctor->id)
                                  ->where('status', 'like', 'Booked')->count();
        $completed_count = Appointment::where('doctor_id','=',$doctor->id)
                                  ->where('status', 'like', 'Completed')->count();
        $patient_file_count  = Patient_File::where('doctor_id','=',$doctor->id)->count();
              
        return $this->api_response(
            'True',
            'The data has been restored successfully.',
            [
              'Doctor' => new DoctorResource($doctor),
              'Appointments' => AppointmentsResource::collection($appointments),
              'Available Count' => $available_count,
              'Booking Count' => $booking_count,
              'Completed Count' => $completed_count,
              'Patient File count' => $patient_file_count,
            ],
            200,
        );
      }   
      catch (\Exception $e) {
        return $this->api_response(
            'False',
            "An error occurred while viewing the doctor's profile:" . $e->getMessage(),
            null,
            500
        );
      }

  }

  public function EditDoctorProfile(Doctor $doctor)
  { 
   
      return $this->api_response(
        'True',
        'The data has been restored successfully.',
        new DoctorResource($doctor),
        200
      );
  }
  public function UpdateDoctorProfile(Request $request,Doctor $doctor)
  { 
    try{
        $doctor->fill([
          'name'=>$request->name,
          'age'=>$request->age,
          'phone'=>$request->phone,
          'email'=>$request->email,
          'governorate'=>$request->governorate,
          'specialty_id'=>$request->specialty,
          'academic_certificates'=>$request->academic_certificates,
          'hospital'=>$request->hospital,
          'experience'=>$request->experience,
          'clinic_location'=>$request->clinic_location,
        ]);  
        
        if ($doctor->isDirty()) {
  
          $request->validate([
            'name' => 'required|string|min:3|max:15',
            'age' =>  'required|integer|min:1|max:90',
            'phone' => 'required|numeric|',
            'email' =>  [
                          'required',
                          'email',
                          'max:50',
                         
                          Rule::unique('doctors', 'email')->ignore($doctor->id),
                        ],
            'governorate' => 'required|string|min:2|max:50',
            //'specialty' => 'required|string',
            'academic_certificates' => 'required|string|min:2|max:50',
            'hospital' => 'required|string|min:2|max:50',
            'experience' => 'required|string|min:2|max:50',
            'clinic_location' => 'required|string|min:2|max:50',
            
          ]);
          
           $doctor->save();

           $doctor_account = Doctor_Account::Where('docotr_id','=',$doctor->id)->first();
           $doctor_account->update(['email'=>$request->email]);

           return $this->api_response(
            'True',
            'Data updated successfully.',
            null,
            200,
           );
         
        } 
        else {
         
          return $this->api_response(
            'True',
            'No changes were made.',
            null,
            200,
           );
        }
    }
    catch (\Exception $e) {
      return $this->api_response(
          'False',
          "An error occurred while updateing the doctor's profile:" . $e->getMessage(),
          null, 
          500
      );
    } 
      
  }
  public function DeleteDoctorProfile($id)
  {
   
    $doctor = Doctor::find($id);

    if ($doctor === null)   
    {
      return $this->api_response(
        'False',
        "Doctor  not found.",
        null,
        401 
      );
    } 
    try {

       
        Appointment::where('doctor_id', '=', $id)->delete();
        Booked_Appointment::where('doctor_id', '=', $id)->delete();
        Completed_Appointment::where('doctor_id', '=', $id)->delete();
        Patient_Health_Information::where('doctor_id', '=', $id)->delete();
        Patient_File::where('doctor_id', '=', $id)->delete();
        Doctor_Diagnosis::where('doctor_id', '=', $id)->delete();
        Doctor_Account::where('doctor_id', '=', $id)->delete();
        $doctor->delete();

        return $this->api_response(
            'True',
            'Doctor profile deleted Successfully.',
            null,
            200 
        );

    } catch (\Exception $erorr) {
        
        return $this->api_response(
            'False',
            'An error occurred while deleting the doctor profile: ' . $erorr->getMessage(),
            null,
            500 
        );
    }
   
  }
  
  public function RestorationDoctorProfile( $id)
  {
    
    $doctor = Doctor::onlyTrashed()->find($id);
    if ($doctor === null) 
    {
      return $this->api_response(
        'False',
        "Doctor not found.",
        null,
        401 
      );
    } 
    try{
        
        Appointment::onlyTrashed()->where('doctor_id','=',$id)->restore();
        Booked_Appointment::onlyTrashed()->where('doctor_id','=',$id)->restore();
        Completed_Appointment::onlyTrashed()->where('doctor_id','=',$id)->restore();
        Patient_Health_Information::onlyTrashed()->where('doctor_id','=',$id)->restore();
        Patient_File::onlyTrashed()->where('doctor_id','=',$id)->restore();
        Doctor_Diagnosis::onlyTrashed()->where('doctor_id','=',$id)->restore();
        Doctor_Account::onlyTrashed()->where('doctor_id','=',$id)->restore();
        $doctor->restore();

        return $this->api_response(
          'True',
          "The doctor's account has been successfully restored.",
          null,
          200,
        );
    }
    catch(\Exception $erorr){
        
        return $this->api_response(
            'False',
            'An error occurred while  restoration the doctor profile: ' . $erorr->getMessage(),
            null,
            500 
        );
    }
   
  }

  public function DeletePermanentlyDoctorProfile( $id)
  {
    $doctor = Doctor::onlyTrashed()->find($id); 

    if ($doctor=== null ) 
    {
      return $this->api_response(
        'False',
        "Doctor not found.",
        null,
        401 
      );
    } 
    try{

      
      Appointment::onlyTrashed()->where('doctor_id','=',$id)->forceDelete();
      Booked_Appointment::onlyTrashed()->where('doctor_id','=',$id)->forceDelete();
      Completed_Appointment::onlyTrashed()->where('doctor_id','=',$id)->forceDelete();
      Patient_Health_Information::onlyTrashed()->where('doctor_id','=',$id)->forceDelete();
      Patient_File::onlyTrashed()->where('doctor_id','=',$id)->forceDelete();
      Doctor_Diagnosis::onlyTrashed()->where('doctor_id','=',$id)->forceDelete();
      Doctor_Account::onlyTrashed()->where('doctor_id','=',$id)->forceDelete();
      $doctor->forceDelete();
      
      return $this->api_response(
        'True',
        "The doctor's account has been successfully Delete permanently.",
        null,
        200,
      );
    }
    catch(\Exception $erorr){
        
      return $this->api_response(
          'False',
          'An error occurred while delete permanently the doctor profile: ' . $erorr->getMessage(),
          null,
          500 
        );
    }
  }
  public function RecycleBin()
  {
    try{

      $doctor_id = Auth::guard('doctor')->user()->doctor_id;

      $deleted_appointments =  Appointment::onlyTrashed()->where('doctor_id','=',$doctor_id)->get();
      $deleted_patients_files =  Patient_File::onlyTrashed()->where('doctor_id','=',$doctor_id)->get();
      
      if($deleted_appointments->isEmpty() && $deleted_patients_files->isEmpty()){
        return $this->api_response(
          'True',
          "It does not store deleted data.",
          null,
          200,
        );
      }
      return $this->api_response(
        'True',
        "The deleted data was retrieved successfully.",
        [
         'Deleted Appointments'=> AppointmentsResource::collection($deleted_appointments),
         'Deleted Patients Files'=> PatientFilesResources::collection($deleted_patients_files),
        ],
        200,
      );
    }
    catch(\Exception $erorr){
        
      return $this->api_response(
          'False',
          'An error occurred while deleted data was retrieved: ' . $erorr->getMessage(),
          null,
          500 
        );
    }
  }

}
