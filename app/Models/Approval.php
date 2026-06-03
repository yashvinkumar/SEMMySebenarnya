<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $table = 'approvals';
    protected $primaryKey = 'approval_ID';
    public $timestamps = false;

    protected $fillable = [
        'inquiry_ID',
        'staff_ID',
        'approval_Status',
        'approval_Comments',
        'approval_Type',
        'approval_Date'
    ];

    protected $casts = [
        'approval_Date' => 'datetime',
    ];

    /**
     * Approval types
     */
    const APPROVAL_TYPES = [
        'mcmc_review' => 'MCMC Review',
        'agency_assignment' => 'Agency Assignment',
        'agency_review' => 'Agency Review',
        'final_approval' => 'Final Approval'
    ];

    /**
     * Approval statuses
     */
    const STATUSES = [
        'pending' => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'assigned' => 'Assigned to Agency',
        'in_progress' => 'In Progress',
        'completed' => 'Completed'
    ];

    /**
     * Get the inquiry that this approval belongs to
     */
    public function inquiry()
    {
        return $this->belongsTo(InquirySubmissionRecord::class, 'inquiry_ID', 'inquiry_ID');
    }

    /**
     * Get the staff member who made this approval
     */
    public function staff()
    {
        return $this->belongsTo(McmcStaff::class, 'staff_ID', 'staff_ID');
    }

    /**
     * Get the assignment for this approval
     */
    public function assignment()
    {
        return $this->hasOne(InquiryAssignment::class, 'approval_ID', 'approval_ID');
    }

    /**
     * Get the formatted approval type
     */
    public function getFormattedApprovalTypeAttribute()
    {
        return self::APPROVAL_TYPES[$this->approval_Type] ?? ucfirst(str_replace('_', ' ', $this->approval_Type));
    }

    /**
     * Get the formatted status
     */
    public function getFormattedStatusAttribute()
    {
        return self::STATUSES[$this->approval_Status] ?? ucfirst(str_replace('_', ' ', $this->approval_Status));
    }

    /**
     * Check if approval can be updated
     */
    public function canBeUpdated()
    {
        return in_array($this->approval_Status, ['pending', 'in_progress']);
    }

    /**
     * Scope for approvals by inquiry
     */
    public function scopeForInquiry($query, $inquiryId)
    {
        return $query->where('inquiry_ID', $inquiryId);
    }

    /**
     * Scope for approvals by staff
     */
    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_ID', $staffId);
    }

    /**
     * Scope for approvals by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('approval_Type', $type);
    }
}
