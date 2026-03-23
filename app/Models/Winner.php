<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Winner extends Model
{
    protected $fillable = ['prize_id', 'code', 'drawn_at', 'winner_name'];
    protected $casts = [
        'drawn_at' => 'datetime',
    ];

    public function prize()
    {
        return $this->belongsTo(Prize::class);
    }
}
