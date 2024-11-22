<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class topSpecialtiesResources extends JsonResource
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
            'Specialty Name' =>$this->name_specialty ,
            'Booked Appointment Count' =>$this->booked_appointment_count,
            'Completed Appointment Count' =>$this->completed_appointment_count,
        ];
    }
}
