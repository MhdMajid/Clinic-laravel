<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Booking;
use App\Models\Doctor;
use App\Models\Patient;
use App\Http\Controllers\AppointmentsController;
use App\Http\Resources\DoctorsResource;
use App\Http\Resources\AppointmentsResource;
use App\Http\Resources\AppointmentDetailsResources;
use App\Http\Resources\PatientAppointmentsResource;
use App\Http\Resources\PatientFilesResources;
use App\Http\Resources\AllPatientFilesResources;
use App\Models\Booked_Appointment;
use App\Models\Doctor_Diagnosis;
use App\Models\Completed_Appointment;
use App\Models\Patient_File;
use App\Models\Patient_Health_Information;
use App\Models\Specialty;
use App\Traits\Api_Response_Trait;
use com_exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use PhpParser\Comment\Doc;
use PHPUnit\Framework\Constraint\IsEmpty;


class PatientsController extends Controller
{
     
    use Api_Response_Trait;

    // public function CreateAppointmentPatient(Appointment $appointment)
    // {
        
    //   //return view('patients.book_appointment', compact('appointment'));
    //   return $this->api_response(
    //     'True',
    //     'The data has been restored successfully.',
    //     new AppointmentsResource($appointment),
    //     200,
    //    );
    // }

    public function StoreAppointmentPatient(Request $request , Appointment $appointment )
    {
        //  
        $request->validate([
            'name' => 'required|string|min:3|max:20',
            'age' => 'required|integer|min:1|max:90', 
            'phone' => 'required|numeric|digits:10', 
            'email' => 'required|email|max:50', 
            'address' => 'required|string|min:4|max:50',
            'previous_surgeries' => 'nullable|string|min:4|max:50', 
            'permanent_medications' => 'nullable|string|min:4|max:50', 
            'current_disease_symptoms' => 'required|string|min:4|max:50',
            'visit_type' => 'required|string|min:4|max:50',
        ]);

        try {
            
            $patient_data = [
                'name' => $request['name'],
                'age' => $request['age'],
                'phone' => $request['phone'],
                'email' => $request['email'],
                'address' => $request['address']
            ];

            
            $patient_exists = Patient::where('email','Like',$request->email)->first();

            if (!$patient_exists) 
            {
                
                $patient = Patient::create($patient_data);
            }else{
                $patient = Patient::create($patient_data);
                return $this->api_response(
                    'false',
                    'opss !! somthing error',
                    null,
                    400
                );
            }

            
            $health_data = [
                'chronic_diseases' => $request['chronic_diseases'],
                'previous_surgeries' => $request['previous_surgeries'],
                'permanent_medications' => $request['permanent_medications'],
                'current_disease_symptoms' => $request['current_disease_symptoms'],
                'visit_type' => $request['visit_type'],
                'patient_id' =>  $patient->id ?? $patient_exists->id,
                'doctor_id' => $appointment->doctor_id
            ];

            
            $patient_health_information = Patient_Health_Information::create($health_data);
           
            // return redirect()->route('patient.save.booking', parameters: [
            //     'appointment' => $appointment->id,
            //     'doctor_id' => $appointment->doctor_id,
            //     'patient_id' => $patient->id ?? $patient_exists->id,
            //     'patient_health_information_id' => $patient_health_information->id,
            // ]);

            return $this->api_response(
                'True',
                'Patient information has been successfully saved.',
                null,
                201
            );
            

        } catch (\Exception $e) {
            
            return $this->api_response(
                'False',
                'An error occurred while saving patient information: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
  
    
    public function Show_Patient_Appointment_Details(Booked_Appointment $booking)
    {
        
        return $this->api_response(
            'True',
            'The data has been restored successfully.',
            $booking,
            200,
        );
        
    }

    
    // public function Edit_Patient_Information(Booked_Appointment $booking)
    // {
    //     //return view('patients.update_patient _information',compact('booking'));
    //     return $this->api_response(
    //         'True',
    //         'The data has been restored successfully.',
    //         $booking,
    //         200,
    //     );
    // }


    public function Update_Patient_Information(Request $request, Booked_Appointment $booking)
    {

      try{
           
            $booking->patient->fill( [
                'name' => $request['name'],
                'age' => $request['age'],
                'phone' => $request['phone'],
                'email' => $request['email'],
                'address' => $request['address']
                
            ]);
            
            $booking->patient_health_information->fill ([
                'chronic_diseases' => $request['chronic_diseases'],
                'previous_surgeries' => $request['previous_surgeries'],
                'permanent_medications' => $request['permanent_medications'],
                'current_disease_symptoms' => $request['current_disease_symptoms'],
                'visit_type' => $request['visit_type']
                ]);

            if( $booking->patient->isDirty() || $booking->patient_health_information->isDirty())
            {
            
                $request->validate([
                    'name' => 'required|string|min:3|max:20',
                    'age' => 'required|Integer|min:1|max:90',
                    'phone' => 'required|numeric|digits:10',
                    'email' =>  [
                                    'required',
                                    'email',
                                    'max:50',
                                    // استثناء البريد الإلكتروني الخاص بالمريض الحالي من الفريد
                                    Rule::unique('patients', 'email')->ignore($booking->patient->id),
                                ],
                    'address' => 'required|string|min:4|max:50',
                    'previous_surgeries' => 'nullable|string|min:4|max:50',
                    'permanent_medications' => 'nullable|string|min:4|max:50',
                    'current_disease_symptoms' => 'required|string|min:4|max:50',
                    'visit_type' => 'required|string|min:4|max:50',
                ]);
                $booking->patient->save();
                $booking->patient_health_information->save();

                
                return $this->api_response(
                    'True',
                    'Data updated successfully.',
                    null,
                    201,
                );
            
            }
            else
            {
                
                return $this->api_response(
                    'True',
                    'No changes were made.',
                    null,
                    200,
                );
            }    
        }
        catch(\Exception $erorr){
        
            return $this->api_response(
                'False',
                'An error occurred while modifying patient information: ' . $erorr->getMessage(),
                null,
                500 
            );
        }
       
    }



    public function SearchDoctors(Request $request, $specialty_id)
    {
        try{
            $request->validate([
                "search"=> "required|string|min:3|max:50"
            ]);

            $search = $request->search;
    
            $doctors = Doctor::where('specialty_id', '=', $specialty_id)
                               ->where(function($query) use ($search) {
                                    $query->where('name','like','%' . $search . '%')
                                        ->orWhere('phone','like', '%' . $search . '%')
                                        ->orWhere('email','like', '%' . $search . '%')
                                        // ->orWhere('academic_certificates', 'like', '%'. $search . '%')
                                        // ->orWhere('hospital','like', '%'. $search . '%') 
                                        ->orWhere('clinic_location','like', '%'. $search . '%');          
                               })
                               ->paginate(5);
            if($doctors->isEmpty())
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
                  DoctorsResource::collection($doctors) ,
                  'search' => $search
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
    public function FilterByGovernorate(Request $request ,$specialty_id)
    {
        try{

            $request->validate([
                "filter"=> "required"
            ]);

            $filter = $request->input('filter');
      
            $doctors = Doctor::where(function ($query) use ($filter,  $specialty_id) {
                $query->where('governorate','like',$filter)
                      ->where('specialty_id','=', $specialty_id);
            })
            ->paginate(5);
            
            
            if($doctors->isEmpty())
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
                    DoctorsResource::collection($doctors),
                  'filter' => $filter
                ],
                 200,
            );
        }
        catch (\Exception $e) {
            return $this->api_response(
                'False',
                'An error occurred while searching by governorate: ' . $e->getMessage(),
                null,
                500
            );
        }
    }

    public function AddDiagnosis(Booked_Appointment $booked_appointment)
    {
       
        return $this->api_response(
            'True',
            'The data has been restored successfully.',
            new AppointmentDetailsResources($booked_appointment) ,
            200,
        );
    }

    public function StoreDiagnosis(Request $request,Booked_Appointment $booked_appointment)
    {
        $request->validate([
            'diagnosis' => 'required|string|max:600',
            'treatment_plan' => 'required|string|max:600',
            'medical_tests' => 'Nullable|string|max:100',
            'radiographs' => 'Nullable|string|max:100',
        ]);
        $completed_appointments_data= [
            'appointment_id' => $booked_appointment->appointment_id,
            'patient_id' =>$booked_appointment->patient_id,
            'patient_health_information_id' =>$booked_appointment->patient_health_information_id,
            'doctor_id' => $booked_appointment->doctor_id,
        ];
        $completed_appointments = Completed_Appointment::create($completed_appointments_data);
        
        if(!$completed_appointments)
        {
          return $this->api_response(
              'False',
              'No completed appointment has been created',
              null,
              400
          );
        } 
        try{

            $patient_file_data= [
                'patient_id' =>$booked_appointment->patient_id,
                'doctor_id' => $booked_appointment->doctor_id,
            ];
            $create_patient_file = Patient_File::create($patient_file_data);
            
            if(!$create_patient_file)
            {
              return $this->api_response(
                  'False',
                  'No patient file has been created',
                  null,
                  400
              );
            } 
            $doctor_diagnosis_data = [
                'doctor_id' => $booked_appointment->doctor_id,
                'patient_id' =>$booked_appointment->patient_id,
                'patient_file_id' =>$create_patient_file->id,
                'completed_appointment_id' => $completed_appointments->id,
                'diagnosis' => $request['diagnosis'],
                'treatment_plan' => $request['treatment_plan'],
                'medical_tests' => $request['medical_tests'],
                'radiographs' => $request['radiographs']
                 ];
            $create_diagnosis = Doctor_Diagnosis::create($doctor_diagnosis_data);
            
            if(!$create_diagnosis)
            {
              return $this->api_response(
                  'False',
                  'No diagnosis has been created',
                  null,
                  400
              );
            } 
            
                
            Appointment::find($booked_appointment->appointment_id)->update(['status'=>'Completed']);
            Booked_Appointment::find($booked_appointment->id)->forceDelete();
                
            return $this->api_response(
                'True',
                'Add Diagnosis successfully and Patient file created successfully.',
                null,
                201,
            );

        }
        catch(\Exception $erorr){
        
            return $this->api_response(
                'False',
                'An error occurred while add diagnosis the patient: ' . $erorr->getMessage(),
                null,
                500 
            );
        }
    }

   
    public function EditDoctorDiagnosis(Doctor_Diagnosis $doctor_diagnosis)
    {   
        return $this->api_response(
            'True',
            'The data has been restored successfully.',
            $doctor_diagnosis,
            200,
        );
    }
    public function UpdateDoctorDiagnosis(Request $request,Doctor_Diagnosis $doctor_diagnosis)
    {
        
        $request->validate([

            'diagnosis' => 'required|string|max:600',
            'treatment_plan' => 'required|string|max:600',
            'medical_tests' => 'nullable|string|max:100',
            'radiographs' => 'nullable|string|max:100',
        ]);
        try{
           
            $doctor_diagnosis->fill([

                'diagnosis'=> $request->diagnosis,
                'treatment_plan'=> $request->treatment_plan,
                'medical_tests'=> $request->medical_tests,
                'radiographs'=> $request->radiographs,
            ]);

           
            if ($doctor_diagnosis->isDirty()) {
               
                $doctor_diagnosis->save();

                return $this->api_response(
                    'True',
                    'Data updated successfully.',
                    null,
                    200,
                );
            } 
            else {
                
                return $this->api_response(
                    'False',
                    'No changes were made.',
                    null,
                    400,
                );
            }

        }
        catch (\Exception $e) {
            return $this->api_response(
                'False',
                'An error occurred while updateing the doctor diagnosis: ' . $e->getMessage(),
                null,
                500
            );
        }
    }


    public function ShowPatientFiles()
    { 
        try{
            $doctor_id = Auth::guard('doctor')->user()->doctor_id;

            $patient_files = Patient_File::where('doctor_id',"=",$doctor_id)
                                           ->orderByDesc('created_at')
                                           ->paginate(5);
            if ($patient_files->isEmpty()) 
            {
                return $this->api_response(
                    'False',
                    'patient files not found.',
                    null,
                    404 
                );
            }
    
            $favorites_patients = Patient::withCount([
                'completed_appointment' => function ($query) use ($doctor_id) {
                    $query->where('doctor_id','=',$doctor_id);
                }
                ]) ->orderByDesc('completed_appointment_count')
                  ->take(3)
                  ->get();

            return $this->api_response(
                'True',
                'The data has been restored successfully.',
                
                [
                    'patient_files' => PatientFilesResources::collection($patient_files),
                    'favorites_patients' => PatientFilesResources::collection($favorites_patients),
                ],
                200,
            );
        }
        catch (\Exception $e) {
            return $this->api_response(
                'False',
                'An error occurred while showing by patient files: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    
    public function DeletePatientFile(Patient_File $patient_file)
    {

        if ($patient_file === null) 
        {
            return $this->api_response(
                'False',
                'Patient files not found.',
                null,
                404 
            );
        }
        try
        {
            $patient_id = $patient_file->patient->id;
            $doctor_id = $patient_file->doctor_id;
    
            Booked_Appointment::where('patient_id','=',$patient_id)
                                ->where('doctor_id','=',$doctor_id)
                                ->delete();
                                
            Completed_Appointment::where('patient_id','=',$patient_id)
                                 ->where('doctor_id','=',$doctor_id)
                                 ->delete();
    
            Doctor_Diagnosis::where('patient_id','=',$patient_id)
                              ->where('doctor_id','=',$doctor_id)
                              ->delete();
    
            Patient_Health_Information::where('patient_id','=',$patient_id)
                                        ->where('doctor_id','=',$doctor_id)
                                        ->delete();
            $patient_file->delete();
    
            return $this->api_response(
                'True',
                'Patient file deleted successfully.',
                null,
                200,
            );
        }
        catch(\Exception $erorr){
        
            return $this->api_response(
                'False',
                'An error occurred while delete  the doctor profile: ' . $erorr->getMessage(),
                null,
                500 
            );
        }
    }

    public function ShowPatient(Patient_File $patient_file)
    {
       try{
            $completed_appointments = Completed_Appointment::where('patient_id','=', $patient_file->patient_id)
                                                  ->where('doctor_id','=', $patient_file->doctor_id);
 
            $booked_appointments = Booked_Appointment::where('patient_id','=',$patient_file->patient_id)
                                                ->where('doctor_id','=',$patient_file->doctor_id);                      
            
            $all_appointments_patient  = $completed_appointments->union($booked_appointments) ->orderByDesc('created_at')->paginate(5);

            return $this->api_response(
                'True',
                'The data has been restored successfully.',
                [
                    'Patient File' =>new PatientFilesResources($patient_file),
                    'Appointments Patient' => PatientAppointmentsResource::collection($all_appointments_patient) 
                ],
                200,
            );
       }
       catch (\Exception $e) {
            return $this->api_response(
                'False',
                'An error occurred while show the patient: ' . $e->getMessage(),
                null,
                500
            );
        }
    }
    public function ShwoAllPatientFiles()
    {
        try{
            $patient_files = Patient_File::withTrashed()->OrderByDesc('created_at')->paginate(10);
        
            if ($patient_files->isEmpty()) 
            {
                return $this->api_response(
                    'False',
                    'Patient files not found.',
                    null,
                    404 
                );
            }

            return $this->api_response(
                'True',
                'The data has been restored successfully.',
                AllPatientFilesResources::collection($patient_files),
                200,
            );
        }
        catch (\Exception $e) {
            return $this->api_response(
                'False',
                'An error occurred while show all patient files: ' . $e->getMessage(),
                null,
                500
            );
        }
        
    }
    public function RestorationPatientFile($id)
    {
        $patient_file =  Patient_File::onlyTrashed()->find($id);

        if ($patient_file === null) 
        {
            return $this->api_response(
                'False',
                'Patient file not found.',
                null,
                404 
            );
        }
        try{
            $patient_id = $patient_file->patient->id;
            $doctor_id = $patient_file->doctor_id;
    
            Booked_Appointment::onlyTrashed()
                                ->where('patient_id','=',$patient_id)
                                ->where('doctor_id','=',$doctor_id)
                                ->restore();
                                
            Completed_Appointment::onlyTrashed()
                                 ->where('patient_id','=',$patient_id)
                                 ->where('doctor_id','=',$doctor_id)
                                 ->restore();
    
            Doctor_Diagnosis::onlyTrashed()
                              ->where('patient_id','=',$patient_id)
                              ->where('doctor_id','=',$doctor_id)
                              ->restore();
    
            Patient_Health_Information::onlyTrashed()
                                        ->where('patient_id','=',$patient_id)
                                        ->where('doctor_id','=',$doctor_id)
                                        ->restore();
            $patient_file->restore();
    
            return $this->api_response(
                'True',
                'The patient File has been successfully restored.',
                null,
                200,
            );

        }
        catch(\Exception $erorr){
        
            return $this->api_response(
                'False',
                'An error occurred while restoration the patient: ' . $erorr->getMessage(),
                null,
                500 
            );
        }
    }
  
    public function DeletePermanentlyPatientFile($id)
    {

        $patient_file =  Patient_File::onlyTrashed()->find($id);

        if ($patient_file === null) 
        {
            return $this->api_response(
                'False',
                'Patient file not found.',
                null,
                404 
            );
        }
        try{
            $patient_id = $patient_file->patient->id;
            $doctor_id = $patient_file->doctor_id;
    
            Booked_Appointment::onlyTrashed()
                                ->where('patient_id','=',$patient_id)
                                ->where('doctor_id','=',$doctor_id)
                                ->forceDelete();
                                
            Completed_Appointment::onlyTrashed()
                                 ->where('patient_id','=',$patient_id)
                                 ->where('doctor_id','=',$doctor_id)
                                 ->forceDelete();
    
            Doctor_Diagnosis::onlyTrashed()
                              ->where('patient_id','=',$patient_id)
                              ->where('doctor_id','=',$doctor_id)
                              ->forceDelete();
    
            Patient_Health_Information::onlyTrashed()
                                        ->where('patient_id','=',$patient_id)
                                        ->where('doctor_id','=',$doctor_id)
                                        ->forceDelete();
            $patient_file->forceDelete();
    
            return $this->api_response(
                'True',
                'The patient file has been successfully delete permanently.',
                null,
                200,
            ); 

        }
        catch(\Exception $erorr){
        
            return $this->api_response(
                'False',
                'An error occurred while delete permanently the patient: ' . $erorr->getMessage(),
                null,
                500 
            );
        }
    }

    public function delete_patient_files($id)
    {
        $patient_file = Patient_File::find($id);

        if ($patient_file->isEmpty()) 
        {
            return $this->api_response(
                'False',
                'Patient file not found.',
                null,
                404 
            );
        }

        $patient_file->delete();

        return $this->api_response(
            'True',
            'Patient file deleted successfully.',
            null,
            200,
        );
    }
    
    public function ShowPatientFile($id)
    {
        
        $patient_file = Patient_File::withTrashed()->where('id',"=",$id)
                                            ->first();
        if ($patient_file === null) 
        {
            return $this->api_response(
                'False',
                'patient file not found.',
                null,
                404 
            );
        }
        try{

            if( $patient_file->trashed()){
                
                return $this->api_response(
                     'False',
                     'To protect patient privacy, you cannot see account details deleted by the doctor.',
                     null,
                     400,
                 );
             
            }
            else{
                 $completed_appointments = Completed_Appointment::withTrashed()
                                                     ->where('patient_id', $patient_file->patient_id)
                                                     ->where('doctor_id', $patient_file->doctor_id);
                                                 
                 $booked_appointments = Booked_Appointment::withTrashed()
                                                     ->where('patient_id', $patient_file->patient_id)
                                                     ->where('doctor_id', $patient_file->doctor_id);
                                                 
                 // دمج الاستعلامين باستخدام union
                 $all_appointments = $completed_appointments->union($booked_appointments)->paginate(3);
         
                 $Other_patient_files = Patient_File::withTrashed()->where('patient_id',"=",$patient_file->patient_id)
                                                                    ->where('id',"!=",$patient_file->id)
                                                                    ->get();

                 if($Other_patient_files->isEmpty()){
                    return $this->api_response(
                        'True',
                        'The data has been restored successfully.',
                        [
                          'Patient File' =>new PatientFilesResources($patient_file),
                          'All Appointments' =>PatientAppointmentsResource::collection($all_appointments),
                        ],
                        200,
                    );
                 }                                                   
                 return $this->api_response(
                     'True',
                     'The data has been restored successfully.',
                     [
                       'Patient File' =>new PatientFilesResources($patient_file),
                       'Other Patient Files' =>new PatientFilesResources($Other_patient_files),
                       'All Appointments' =>PatientAppointmentsResource::collection($all_appointments),
                     ],
                     200,
                 );
            }
        } 
        catch(\Exception $erorr){
        
            return $this->api_response(
                'False',
                "An error occurred while finalizing the patient's file: " . $erorr->getMessage(),
                null,
                500 
            );
        }
    }

}
