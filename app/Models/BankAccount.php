<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class BankAccount extends Model
{
    use LogsActivity;

    // add fillable
    protected $fillable = [];

    // add guaded
    protected $guarded = ['id'];

    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
