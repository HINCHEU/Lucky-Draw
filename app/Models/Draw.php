<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    protected $fillable = ['name', 'description', 'draw_date', 'active'];

    protected $casts = [
        'draw_date' => 'date',
        'active' => 'boolean',
    ];

    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }
}
