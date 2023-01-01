<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Announcement extends Model
{
    use HasFactory;
    use LogsActivity;

    protected $fillable = [
        'name',
        'color',
        'custom_color',
        'title',
        'body',
        'icon',
        'users',
    ];

    protected $casts = [
        'users' => 'array',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
