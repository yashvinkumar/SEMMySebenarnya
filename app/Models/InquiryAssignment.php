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
        'completed_At',
        'due_date',
        'sla_status'
    ];

    protected $casts = [
        'assignment_Date' => 'datetime',
        'completed_At' => 'datetime',
        'due_date' => 'datetime',
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
     * The "booted" method of the model.
     * Automatically set due_date when an assignment is created.
     * SLA period: 7 days from assignment_Date.
     */
    protected static function booted()
    {
        static::creating(function ($assignment) {
            if ($assignment->assignment_Date && !$assignment->due_date) {
                $assignment->due_date = $assignment->assignment_Date->copy()->addDays(7);
            }
            if (!$assignment->sla_status) {
                $assignment->sla_status = 'On Time';
            }
        });
    }

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

    /**
     * Get the computed SLA status.
     *
     * Business rule:
     * - If assignment_Status is 'completed' → 'On Time'
     * - If current date > due_date AND assignment is not completed → 'Overdue'
     * - Otherwise → 'On Time'
     */
    public function getSlaStatusAttribute($value)
    {
        // If assignment is completed, always return On Time
        if ($this->assignment_Status === 'completed') {
            return 'On Time';
        }

        // If we have a due_date and it's in the past, it's overdue
        if ($this->due_date && now()->gt($this->due_date)) {
            return 'Overdue';
        }

        // Otherwise, use the stored value or default
        return $value ?: 'On Time';
    }

    /**
     * Scope to get only overdue assignments.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                     ->where('assignment_Status', '!=', 'completed');
    }

    /**
     * Scope to get only on-time assignments.
     */
    public function scopeOnTime($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('due_date')
              ->orWhere('due_date', '>=', now())
              ->orWhere('assignment_Status', 'completed');
        });
    }
}