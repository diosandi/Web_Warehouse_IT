<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'is_active',
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
            'is_active'=>'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staf';
    }

    public function isOperator(): bool
    {
        return $this->isSuperAdmin() || $this->isAdmin();
    }

    public function canManageUsers(): bool
    {
        return $this->isOperator();
    }

    public function canManageIssueReports(): bool
    {
        return $this->isOperator();
    }

    public static function roleLabels(): array
    {
        return [
            'super_admin' => 'Super Admin',
            'admin' => 'Admin',
            'client' => 'Client',
            'staf' => 'Staf',
        ];
    }

    public function distributions()
    {
        return $this->hasMany(Distribution::class, 'user_id');
    }

    public function issueReports()
    {
        return $this->hasMany(IssueReport::class, 'reporter_id');
    }

    public function resolvedIssueReports()
    {
        return $this->hasMany(IssueReport::class, 'resolved_by');
    }

}
