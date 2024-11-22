<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentDetailsResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        
        if($this->appointment->status =='Completed'){
            return [
                'id' => $this->id ,
                'Date' => $this->appointment->date,
                'Time' => $this->appointment->time,
                'Status' => $this->appointment->status,
    
                'Patient Name' => $this->patient->name ,
                'Patient Age' => $this->patient->age ,
                'Patient Phone' => $this->patient->phone ,
                'Patient Email' => $this->patient->email ,
                'Patient Address' => $this->patient->address ,
    
                'Chronic Diseases' => $this->patient_health_information->chronic_diseases,
                'Previous Surgeries' => $this->patient_health_information->previous_surgeries ,
                'Permanent Medications' => $this->patient_health_information->permanent_medications ,
                'Current Disease Symptoms' => $this->patient_health_information->current_disease_symptoms ,
                'Visit Type' => $this->patient_health_information->visit_type ,
    
                'Diagnosis' => $this->doctor_diagnosis->diagnosis,
                'Treatment Plan' => $this->doctor_diagnosis->treatment_plan,
                'Medical Tests' => $this->doctor_diagnosis->medical_tests,
                'Radiographs' => $this->doctor_diagnosis->radiographs,
            ];   
        }

        return [
            'id' => $this->id ,
            'Date' => $this->appointment->date,
            'Time' => $this->appointment->time,
            'Status' => $this->appointment->status,

            'Patient Name' => $this->patient->name ,
            'Patient Age' => $this->patient->age ,
            'Patient Phone' => $this->patient->phone ,
            'Patient Email' => $this->patient->email ,
            'Patient Address' => $this->patient->address ,

            'Chronic Diseases' => $this->patient_health_information->chronic_diseases,
            'Previous Surgeries' => $this->patient_health_information->previous_surgeries ,
            'Permanent Medications' => $this->patient_health_information->permanent_medications ,
            'Current Disease Symptoms' => $this->patient_health_information->current_disease_symptoms ,
            'Visit Type' => $this->patient_health_information->visit_type ,

            
        ];
    }
}
