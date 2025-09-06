<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PayrollItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'payroll_items';

    protected $guarded = [];

    protected $casts = [
        'base_minutes' => 'integer',
        'base_pay_cents' => 'integer',
        'bonus_cents' => 'integer',
        'deductions_cents' => 'integer',
        'total_pay_cents' => 'integer',
        'generated_at' => 'datetime',
    ];

    public function period()
    {
        return $this->belongsTo(PayrollPeriod::class, 'payroll_period_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}


