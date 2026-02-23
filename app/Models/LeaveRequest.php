<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LeaveRequest extends Model
{
    use HasFactory;

    // Mass assignable fields
    protected $fillable = [
        'user_id',
        'request_type',
        'start_date',
        'end_date',
        'status',
        'report_path',
        'admin_remark',
        'hod_signature',
        'hod_signed_at',
        'hod_remarks',
        'admin_signature',
        'admin_signed_at',
        'admin_remarks',
    ];

    /**
     * Relationship: A leave request belongs to a user
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Optional: Helper to check leave status
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
}
