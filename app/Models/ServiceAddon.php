<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAddon extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'service_addons';

    protected $guarded = [];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}


