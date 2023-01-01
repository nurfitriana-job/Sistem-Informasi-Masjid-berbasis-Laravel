<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JournalEntry extends Model
{
    // add fillable
    protected $fillable = [];

    // add guaded
    protected $guarded = ['id'];

    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    protected static function booted()
    {
        static::created(function () {
            AccountBalance::updateAccountBalance();
        });

        static::updated(function () {
            AccountBalance::updateAccountBalance();
        });

        static::deleted(function () {
            AccountBalance::updateAccountBalance();
        });
    }

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
