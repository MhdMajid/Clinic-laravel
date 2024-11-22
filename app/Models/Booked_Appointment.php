<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booked_Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table='booked_appointments';
    protected $fillable = 
    [
      'appointment_id', 'patient_id', 'patient_health_information_id', 'doctor_id',
    ];
     
    # one to  one 
    public function appointment()
    {
      return $this->belongsTo(Appointment::class);
    }
    # one to  many 
    public function patient()
    {
      return $this->belongsTo(Patient::class);
    }
    # one to  many 
    public function doctor()
    {
      return $this->belongsTo(Doctor::class);
    }
    #  one to one 
    public function patient_health_information()
    {
       return $this->belongsTo(Patient_Health_Information::class);
    }
   
}

