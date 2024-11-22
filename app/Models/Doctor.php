<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'age',
        'phone',
        'email',
        'governorate',
        'specialty_id',
        'academic_certificates',
        'experience',
        'hospital',
        'clinic_location',
    ];
    
     #Inverse    many to one
    public function appointment()
    {
      return $this->hasMany(Appointment::class);
    } 
    #Inverse  one to many
    public function booked_appointment()
    {
      return $this->hasOne(Booked_Appointment::class);
    }
   
    #  many to many 
    public function patient_health_information()
    {
        return $this->hasMany(Patient_Health_Information::class);
    }

    #Inverse relations  one to one 
    public function specialty()
    {
        return $this->belongsTo(Specialty::class,'specialty_id');
    }
     #one to one  
     public function doctor_account()
     {
       return $this->hasOne(Doctor_Account::class);
     }
    # one to many 
    public function doctor_diagnosis()
    {
        return $this->hasOne(Doctor_Diagnosis::class);
    }
    #nverse  one to many 
     public function completed_appointment()
     {
         return $this->hasOne(Completed_Appointment::class);
     }
     # many to one
     public function patient_file()
     {
      return $this->hasMany(Patient_File::class,'doctor_id','id');
     }
}
