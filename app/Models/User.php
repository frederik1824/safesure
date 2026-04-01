<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, \Spatie\Permission\Traits\HasRoles, \App\Traits\Auditable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
        'avatar',
        'password',
        'responsable_id',
        'last_login_at',
        'last_login_ip',
    ];

    /**
     * Get the user's avatar URL.
     */
    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&color=7F9CF5&background=EBF4FF';
    }

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
            'last_login_at' => 'datetime',
        ];
    }

    public function rol()
    {
        return $this->belongsTo(Rol::class);
    }

    public function responsable()
    {
        return $this->belongsTo(Responsable::class);
    }

    /**
     * Determine if the user belongs to the parent company (ARS CMD)
     */
    public function isCmd(): bool
    {
        // If they have no responsible assigned, they are core/corporate (CMD)
        if (is_null($this->responsable_id)) return true;
        
        // If they have a responsible, we check if it is the CMD one (ID 1)
        return $this->responsable_id == 1;
    }

    /**
     * Determine if the user belongs to a gestora (like SAFESURE)
     */
    public function isGestora(): bool
    {
        return !$this->isCmd();
    }
}
