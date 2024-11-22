<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = 
    [
      'id','date','time','doctor_id', 'status',
    ];

    # one to one
    public function booked_appointment()
    {
      return $this->hasOne(Booked_Appointment::class);
    }
      
    # one to one
    public function completed_appointment()
    {
      return $this->hasOne(Completed_Appointment::class);
    }
    # many to  one 
    public function doctor()
    {
      return $this->belongsTo(Doctor::class,'doctor_id');
    }
}
