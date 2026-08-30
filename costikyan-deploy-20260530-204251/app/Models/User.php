<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'ai_credits',
        'company_name',
        'trade_discount',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
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

    const ROLE_ADMIN  = 'admin';
    const ROLE_TEAM   = 'team';
    const ROLE_TRADE  = 'trade';
    const ROLE_CLIENT = 'client';

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isTeam(): bool
    {
        return $this->role === self::ROLE_TEAM;
    }

    public function isTrade(): bool
    {
        return $this->role === self::ROLE_TRADE;
    }

    public function isClient(): bool
    {
        return $this->role === self::ROLE_CLIENT;
    }

    public function isAdminOrTeam(): bool
    {
        return in_array($this->role, [self::ROLE_ADMIN, self::ROLE_TEAM]);
    }

    public function roleBadge(): array
    {
        return match($this->role) {
            self::ROLE_ADMIN  => ['label' => 'Admin',    'bg' => '#0f172a', 'color' => '#fff'],
            self::ROLE_TEAM   => ['label' => 'Team',     'bg' => '#7c3aed', 'color' => '#fff'],
            self::ROLE_TRADE  => ['label' => 'Trade',    'bg' => '#E8651A', 'color' => '#fff'],
            default           => ['label' => 'Customer', 'bg' => '#e5e7eb', 'color' => '#374151'],
        };
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function wishlist()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }

    public function savedEstimates()
    {
        return $this->hasMany(SavedEstimate::class);
    }

    public function roomVisualizations()
    {
        return $this->hasMany(RoomVisualization::class);
    }

    public function tradeProjects()
    {
        return $this->hasMany(TradeProject::class);
    }

    public function tradeQuotes()
    {
        return $this->hasMany(TradeQuote::class);
    }

    public function sampleRequests()
    {
        return $this->hasMany(SampleRequest::class);
    }
}
