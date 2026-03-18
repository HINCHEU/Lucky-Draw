<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $fillable = ['name', 'description', 'photo_path', 'quantity', 'order', 'draw_id'];

    public function winners()
    {
        return $this->hasMany(Winner::class);
    }

    public function draw()
    {
        return $this->belongsTo(Draw::class);
    }

    public function availableQuantity()
    {
        return $this->quantity - $this->winners()->count();
    }
}
