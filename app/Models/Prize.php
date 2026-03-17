<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prize extends Model
{
    protected $fillable = ['name', 'description', 'photo_path', 'quantity', 'order'];

    public function winners()
    {
        return $this->hasMany(Winner::class);
    }

    public function availableQuantity()
    {
        return $this->quantity - $this->winners()->count();
    }
}
