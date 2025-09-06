<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $guarded = [];

    public function addons()
    {
        return $this->hasMany(ServiceAddon::class);
    }
}


