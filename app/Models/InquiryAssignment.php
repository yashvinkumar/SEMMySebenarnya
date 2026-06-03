<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiryAssignment extends Model
{
    use HasFactory;

    protected $table = 'inquiry_assignments';
    protected $primaryKey = 'assignment_ID';
    public $timestamps = false;

    protected $fillable = [
        'agency_ID',
        'approval_ID',
        'assignment_Date',
        'assignment_Status',
        'assignment_Comments',
        'rejection_Reason',
        'assigned_By',
        'completed_At'
    ];

    protected $casts = [
        'assignment_Date' => 'datetime',
        'completed_At' => 'datetime',
    ];

    /**
     * Assignment statuses
     */
    const STATUSES = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'rejected' => 'Rejected',
        'reassigned' => 'Reassigned'
    ];

    /**
     * Get the agency that this assignment belongs to
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_ID', 'agency_ID');
    }

    /**
     * Get the approval record
     */
    public function approval()
    {
        return $this->belongsTo(Approval::class, 'approval_ID', 'approval_ID');
    }

    /**
     * Get the inquiry through the approval
     */
    public function inquiry()
    {
        return $this->hasOneThrough(
            InquirySubmissionRecord::class,
            Approval::class,
            'approval_ID',
            'inquiry_ID',
            'approval_ID',
            'inquiry_ID'
        );
    }

    /**
     * Get the staff member who assigned this
     */
    public function assignedByStaff()
    {
        return $this->belongsTo(McmcStaff::class, 'assigned_By', 'staff_ID');
    }

    /**
     * Get the formatted status
     */
    public function getFormattedStatusAttribute()
    {
        return self::STATUSES[$this->assignment_Status] ?? ucfirst(str_replace('_', ' ', $this->assignment_Status));
    }

    /**
     * Check if assignment can be updated
     */
    public function canBeUpdated()
    {
        return in_array($this->assignment_Status, ['pending', 'in_progress']);
    }

    /**
     * Check if assignment can be rejected
     */
    public function canBeRejected()
    {
        return in_array($this->assignment_Status, ['pending', 'in_progress']);
    }

    /**
     * Mark assignment as in progress
     */
    public function markInProgress()
    {
        $this->assignment_Status = 'in_progress';
        $this->save();
    }

    /**
     * Mark assignment as completed
     */
    public function markCompleted($comments = null)
    {
        $this->assignment_Status = 'completed';
        $this->completed_At = now();
        if ($comments) {
            $this->assignment_Comments = $comments;
        }
        $this->save();
    }

    /**
     * Reject assignment
     */
    public function reject($reason)
    {
        $this->assignment_Status = 'rejected';
        $this->rejection_Reason = $reason;
        $this->save();
    }

    /**
     * Scope for pending assignments
     */
    public function scopePending($query)
    {
        return $query->where('assignment_Status', 'pending');
    }

    /**
     * Scope for assignments by agency
     */
    public function scopeForAgency($query, $agencyId)
    {
        return $query->where('agency_ID', $agencyId);
    }

    /**
     * Scope for assignments by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('assignment_Date', [$startDate, $endDate]);
    }

    public function progressUpdates()
    {
        return $this->hasMany(InquiryProgress::class, 'assignment_ID', 'assignment_ID');
    }
}
