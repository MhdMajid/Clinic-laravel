<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       
        
        return [

            'id' => $this->id  ?? $this->doctor_id,
            'Name' => $this->name ?? $this->doctor->name,
            'Specialty'=> $this->specialty->name_specialty ?? $this->doctor->specialty->name_specialty,
            'Phone' => $this->phone ?? $this->doctor->phone,
            'Email' => $this->email ?? $this->doctor->email,
            'Governorate' => $this->governorate ?? $this->doctor->governorate,
            'Clinic Location' => $this->clinic_location ?? $this->doctor->clinic_location,
            'Academic Certificates' => $this->academic_certificates ?? $this->doctor->academic_certificates,
            'Experience' => $this->experience ?? $this->doctor->experience,
            'Hospital' => $this->hospital ?? $this->doctor->hospital,
            'Date the account was created' =>  $this->created_at,
        ];

    }
}
