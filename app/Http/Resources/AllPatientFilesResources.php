<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllPatientFilesResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
  
        return [

            'Patient_File_id' => $this->id ,
            'Patient_id' => $this->patient->id ,
            'Patient Name' => $this->patient->name ,
            'Doctor' => $this->doctor->name ,
            'Patient Age' => $this->patient->age , 
            'Patient Phone' => $this->patient->phone ,
            'Patient Email' => $this->patient->email ,
            'Patient Address' => $this->patient->address ,
        ];

    }
}
