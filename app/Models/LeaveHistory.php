<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\LeaveRequest;

class LeaveHistory extends Model
{
    protected $fillable = [
        'leave_request_id',
        'user_id',
        'action',
        'remarks',
        'created_at',
    ];
    public $timestamps = false;
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function leaveRequest()
    {
        return $this->belongsTo(LeaveRequest::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
