<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AllAppointmentsResources extends JsonResource
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
                'doctor_id'=> $this->doctor->id ,
                'By Doctor' =>$this->doctor->name,
                'Date' => $this->date,
                'Time'=> $this->time,
                'Status' => $this->status,
            ];
        }
        if($this->status === "Completed"){
            return [
                'id' => $this->id,
                'completed_appointment_id'=> $this->completed_appointment->id ,
                'doctor_id'=> $this->doctor->id ,
                'By Doctor' =>$this->doctor->name,
                'Date' => $this->date,
                'Time'=> $this->time,
                'Status' => $this->status,
            ];
        }
        return [
            'id' => $this->id,
            'By Doctor' =>$this->doctor->name,
            'Date' => $this->date,
            'Time'=> $this->time,
            'Status' => $this->status,
        ];
    }
}
