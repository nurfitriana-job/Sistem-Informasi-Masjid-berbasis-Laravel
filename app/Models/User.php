<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasName;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Jeffgreco13\FilamentBreezy\Traits\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia, HasName, MustVerifyEmail
{
    use HasApiTokens;
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasRoles;
    use InteractsWithMedia;
    use LogsActivity;

    use Notifiable;
    use TwoFactorAuthenticatable;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->username = $model->generateUsername();
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'phone',
        'username',
        'email',
        'password',
        'avatar_url',
        'telegram_chat_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // public function getFilamentAvatarUrl(): ?string
    // {
    //     return asset($this->avatar_url);
    // }

    public function getFilamentAvatarUrl(): ?string
    {
        if (!$this->avatar_url) {
            return null;
        }

        // Jika avatar_url sudah berupa URL (http/https), gunakan langsung
        if (filter_var($this->avatar_url, FILTER_VALIDATE_URL)) {
            return $this->avatar_url;
        }

        return Storage::url($this->avatar_url);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function getFilamentName(): string
    {
        return $this->name;
    }

    public function generateUsername(): string
    {
        $username = preg_replace('/\s+/', '', $this->name);
        $username = strtolower($username);
        $username = substr($username, 0, 20);

        return $username;
    }
}
