<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentsCountPerDoctorResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'  =>$this->id ,
            'Doctor Name' =>$this->name ,
            'Booked Appointment Count' =>$this->booked_appointment_count,
            'Completed Appointment Count' =>$this->completed_appointment_count,
        ];
    }
}
