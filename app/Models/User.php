<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'owner_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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

    /**
     * The owner this user belongs to (for managers & tenants).
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Users belonging to this owner (managers & tenants).
     */
    public function subUsers(): HasMany
    {
        return $this->hasMany(User::class, 'owner_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super-admin');
    }

    public function isOwner(): bool
    {
        return $this->hasRole('owner');
    }

    /** Property ids this user co-owns (revenue-share, not primary/managed). */
    public function coOwnedPropertyIds(): array
    {
        return \Illuminate\Support\Facades\DB::table('property_owners')
            ->where('owner_id', $this->id)
            ->pluck('property_id')
            ->all();
    }

    /**
     * A "co-owner viewer": an owner-role user who does NOT primarily manage any
     * property but co-owns at least one. They get a restricted, read-only view.
     */
    public function isCoOwnerViewer(): bool
    {
        if ($this->isSuperAdmin() || ! $this->hasRole('owner')) {
            return false;
        }

        $managesAny = \App\Models\Property::withoutGlobalScopes()->where('owner_id', $this->id)->exists();

        return ! $managesAny && ! empty($this->coOwnedPropertyIds());
    }

    /**
     * The owner scope key for this user:
     * - super-admin: null (no scoping, sees everything)
     * - owner: their own id
     * - manager/tenant: their parent owner's id
     */
    public function ownerId(): ?int
    {
        if ($this->isSuperAdmin()) {
            return null;
        }

        return $this->isOwner() ? $this->id : $this->owner_id;
    }

    public function tenant(): HasOne
    {
        return $this->hasOne(Tenant::class);
    }

    /**
     * The payment wallet for this owner (online payment config + balance).
     */
    public function wallet(): HasOne
    {
        return $this->hasOne(OwnerWallet::class, 'owner_id');
    }

    public function createdInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'created_by');
    }

    public function verifiedInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'verified_by');
    }

    public function createdLeases(): HasMany
    {
        return $this->hasMany(Lease::class, 'created_by');
    }

    public function updatedLeases(): HasMany
    {
        return $this->hasMany(Lease::class, 'updated_by');
    }
}
