<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class NotificationTemplate extends Model implements HasMedia
{
    use InteractsWithMedia;

    // add fillable
    protected $fillable = ['name', 'subject', 'body', 'is_active'];

    // add guaded
    protected $guarded = ['id'];

    // add hidden
    protected $hidden = ['created_at', 'updated_at'];

    // add casts
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parseBody($data)
    {
        return str_replace(
            array_keys($data),
            array_values($data),
            $this->body
        );
    }

    public function parseSubject($data)
    {
        if (! $this->subject) {
            return null;
        }

        $subject = $this->subject;

        foreach ($data as $key => $value) {
            $subject = str_replace('{' . $key . '}', $value, $subject);
        }

        return $subject;
    }
}
