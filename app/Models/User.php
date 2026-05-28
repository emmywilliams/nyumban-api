<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use SoftDeletes;

    protected $fillable = [
        'uuid',
        'name',
        'phone',
        'email',
        'password',
        'role_id',
        'national_id',
        'avatar',
        'status',
        'email_verified_at',
        'phone_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password'          => 'hashed',
            'status'            => 'string',
            'deleted_at'        => 'datetime',
        ];
    }

    /**
     * Boot function to handle UUID generation automatically.
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->uuid)) {
                $user->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Filament Access Control: 
     * Only users with 'admin' role can access the panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isAdmin() && $this->status === 'active';
    }


    // ────────────────────────────────────────────────
    // Relationships
    // ────────────────────────────────────────────────

    /**
     * The role this user belongs to (tenant / landlord / admin etc.)
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Properties owned by this user (as landlord)
     */
    public function properties(): HasMany
    {
        return $this->hasMany(Property::class, 'landlord_id');
    }

    /**
     * Bookings made by this user (as tenant)
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'tenant_id');
    }

    /**
     * Landlord verifications submitted by this user
     */
    public function verifications()
    {
        return $this->hasMany(LandlordVerification::class, 'landlord_id');
    }

    /**
     * Payments received or made related to this user (can be expanded)
     */
    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Booking::class,
            'tenant_id',         // foreign key on payments -> bookings
            'booking_id',        // foreign key on bookings -> users
            'id',                // local key on users
            'id'                 // local key on bookings
        );
    }

    // Optional: Scope for active users
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Optional: Check if user is landlord
    public function isLandlord(): bool
    {
        return $this->role && $this->role->name === 'landlord';
    }

    // Optional: Check if user is tenant
    public function isTenant(): bool
    {
        return $this->role && $this->role->name === 'tenant';
    }

    // Optional: Check if user is admin
    public function isAdmin(): bool
    {
        return strtolower(optional($this->role)->name) === 'admin';
    }

    // Favorite properties (many-to-many relationship)
    public function favoriteProperties()
    {
        return $this->belongsToMany(
            Property::class,
            'favorites'
        )->withTimestamps();
    }
}
