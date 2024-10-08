<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'account',
        'is_active',
        'phone',
        'avatar'
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    public function filmDiaries()
    {
        return $this->belongsToMany(Film::class, 'film_diaries');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function ticketOrders()
    {
        return $this->hasMany(TicketOrder::class);
    }

    public function hasPermission($permission_code)
    {
        // Load roles and permissions for the user only if not already loaded
        if (!$this->relationLoaded('roles.permissions')) {
            $this->load('roles.permissions:id,code,name');
        }

        // Collect all unique permission codes associated with the user's roles
        $permissionCodes = $this->roles->flatMap(function ($role) {
            return $role->permissions->pluck('code');
        })->unique();

        // Check if the specified permission code exists in the user's permissions
        return $permissionCodes->contains($permission_code);
    }
}
