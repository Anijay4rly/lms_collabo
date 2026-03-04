<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'request_type',
        'start_date',
        'end_date',
        'status',
        'report_path',
        'hod_signature',
        'hod_signed_at',
        'hod_remarks',
        'admin_signature',
        'admin_signed_at',
        'admin_remarks',
        'reasons',
        'destination',
        'request_category',
    ];

    protected $casts = [
        'start_date'      => 'datetime',
        'end_date'        => 'datetime',
        'hod_signed_at'   => 'datetime',
        'admin_signed_at' => 'datetime',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /**
     * Relationship: A leave request belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A leave request has many histories (timeline)
     */
    public function histories()
    {
        return $this->hasMany(LeaveHistory::class);
    }

    /**
     * Accessor: Duration in days
     */
    public function getDurationInDaysAttribute()
    {
        if ($this->start_date && $this->end_date) {
            return $this->start_date->diffInDays($this->end_date) + 1;
        }
        return null;
    }

    /**
     * Helper methods to check leave status
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function leavehistories()
    {
        return $this->hasMany(LeaveHistory::class);
    }

  }
