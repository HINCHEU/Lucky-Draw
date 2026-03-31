<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = ['draw_id', 'registration_number', 'employee_name'];

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }
}
