<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientFilesResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        
        return [
            'id' => $this->id,
            'patient_id' => $this->patient->id ?? $this->id,
            'Patient Name' => $this->patient->name ?? $this->name,
            'Patient Age' => $this->patient->age ?? $this->age, 
            'Patient Phone' => $this->patient->phone ?? $this->phone,
            'Patient Email' => $this->patient->email ?? $this->email,
            'Patient Address' => $this->patient->address ?? $this->address,
        ];
    }
}
