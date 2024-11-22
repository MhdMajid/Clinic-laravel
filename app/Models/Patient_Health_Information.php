<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient_Health_Information extends Model
{
    
    use HasFactory;
    use SoftDeletes;
    
    protected $table='patients_health_information';
    protected $fillable = [
        'chronic_diseases', 
        'previous_surgeries', 
        'permanent_medications',
        'current_disease_symptoms', 
        'visit_type',
        'patient_id',
        'doctor_id',
        
    ];

    #  one to  many
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
    #  one to  one
    public function booked_appointment()
    {
        return $this->hasOne(Booked_Appointment::class ,'patient_health_information_id','id' );
    }
    #  one to  one
    public function completed_appointment()
    {
        return $this->hasOne(Completed_Appointment::class ,'patient_health_information_id','id');
    }
    #Inverse relations  many to  many 
    public function doctor()
    {
        return $this->belongsTo(Doctor::class,'doctor_id');
    }


}
