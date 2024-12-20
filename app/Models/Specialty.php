<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    use HasFactory;
    #one to  one 
    public function doctor()
    {
      return $this->hasOne(Doctor::class);
    }
    
}
