<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorsResource extends JsonResource
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
            'name' => $this->name ,
            'Specialty' => $this->specialty->name_specialty ?? null,
            'phone' => $this->phone ,
            'email' => $this->email ,
            'governorate' => $this->governorate ,
            'clinic location' => $this->clinic_location,
            'Deleted_at' => $this->deleted_at ?? Null,
        ];
    }
}
