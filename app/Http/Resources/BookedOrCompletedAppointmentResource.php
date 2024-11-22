<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookedOrCompletedAppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id ,
            'appointment_id' => $this->appointment_id ,
            'patient_id' => $this->patient->id ,
            'patient_file_id' => $this->patient->patient_file->id ?? Null,
            'Date' => $this->appointment->date ,
            'Time'=> $this->appointment->time ,
            'Patient Name' => $this->patient->name ,
            'Patient Phone' => $this->patient->phone ,
            'Patient Email' => $this->patient->email ,
        ];
    }
}
