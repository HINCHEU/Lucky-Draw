<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Draw extends Model
{
    protected $fillable = ['name', 'description', 'draw_date', 'active', 'start_code', 'end_code'];

    protected $casts = [
        'draw_date' => 'date',
        'active' => 'boolean',
    ];

    public function prizes()
    {
        return $this->hasMany(Prize::class);
    }
}
