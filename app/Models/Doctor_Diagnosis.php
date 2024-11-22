<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Doctor_Diagnosis extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table='doctor_diagnoses';
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'patient_file_id',
        'completed_appointment_id',
        'diagnosis', 
        'treatment_plan', 
        'medical_tests', 
        'radiographs',     
    ];

  
    #Inverse relations one to many
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    #Inverse relations  one to many
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
 
    #Inverse relations  one to one        
    public function completed_appointment()   
    {
        return $this->belongsTo(Completed_Appointment::class);
    }
    #Inverse relations  one to many        
    public function patient_file()   
    {
        return $this->belongsTo(Patient_File::class);
    }

}
