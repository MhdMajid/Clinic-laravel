<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PatientAppointmentsResource extends JsonResource
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
                'appointment_id' => $this->appointment->id,
                'Date' => $this->appointment->date,
                'Time'=> $this->appointment->time,
                'Status' => $this->appointment->status,
        ];
        
    }
}
