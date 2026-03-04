<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'fname',
        'mname',
        'lname',
        'gender',
        'dob',
        'email',
        'password',
        'role',
        'department_id',
        'status',
        'assigned_as',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'dob' => 'date',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationships
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Accessors
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->fname} {$this->mname} {$this->lname}");
    }

    /**
     * Role helpers (optional but powerful)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isHod(): bool
    {
        return $this->role === 'hod';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    public function LeaveRequests(){
        return $this->hasMany(LeaveRequest::class);
    }

    public function leaveHistories(){
        return $this->hasManyThrough(LeaveHistory::class, LeaveRequest::class);
    }

     public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }
}
