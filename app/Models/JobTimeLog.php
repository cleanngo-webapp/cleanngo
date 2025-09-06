<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTimeLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'job_time_logs';

    protected $guarded = [];

    protected $casts = [
        'clock_in' => 'datetime',
        'clock_out' => 'datetime',
        'minutes_worked' => 'integer',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}


