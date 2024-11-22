<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'name', 
        'age', 
        'phone', 
        'email', 
        'address', 
        
    ];

  
    
    # Inverse   one to many 
    public function patient_health_information()
    {
        return $this->hasOne(Patient_Health_Information::class);
    }
   
    # Inverse  one to many
    public function booked_appointment()
    {
        return $this->hasMany(Booked_Appointment::class);
    }
    #  one to many 
    public function completed_appointment()
    {
        return $this->hasMany(Completed_Appointment::class);
    }
    # one to  many 
    public function doctor_diagnosis()
    {
        return $this->hasOne(Doctor_Diagnosis::class);
    }
    #  one to one
    public function patient_file()
    {
      return $this->hasOne(Patient_File::class);
    }
}
