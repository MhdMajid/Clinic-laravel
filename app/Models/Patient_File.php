<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient_File extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table='patient_files';
    protected $fillable = [
        'doctor_id',
        'patient_id',
    
    ];

  
    #Inverse relations many to one
    public function doctor()
    {
        return $this->belongsto(Doctor::class);
    }
    #Inverse relations  one to one
    public function patient()
    {
        return $this->belongsto(Patient::class);
    }
    #  one to many
    public function doctor_diagnosis()
    {
        return $this->hasOne(Doctor_Diagnosis::class);
    }
  

}
