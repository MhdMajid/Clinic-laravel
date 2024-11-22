<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if($this->status === "Booked"){
            return [
                'id' => $this->id,
                'booked_appointment_id'=> $this->booked_appointment->id ,
                'patient_id'=> $this->booked_appointment->patient->id ,
                'Date' => $this->date,
                'Time'=> $this->time,
                'Status' => $this->status,
                'Deleted_at' => $this->deleted_at ?? Null,
            ];
        }
        if($this->status === "Completed"){
            return [
                'id' => $this->id,
                'completed_appointment_id'=> $this->completed_appointment->id ,
                'patient_id'=> $this->completed_appointment->patient->id ,
                'Date' => $this->date,
                'Time'=> $this->time,
                'Status' => $this->status,
                'Deleted_at' => $this->deleted_at ?? Null,
            ];
        }
        return [
            'id' => $this->id,
            'Date' => $this->date,
            'Time'=> $this->time,
            'Status' => $this->status,
            'Deleted_at' => $this->deleted_at ?? Null,
        ];
    }
}
