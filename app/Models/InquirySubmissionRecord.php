<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class InquirySubmissionRecord extends Model
{
    use HasFactory;

    protected $table = 'inquiries';
    protected $primaryKey = 'inquiry_ID';
    public $timestamps = false;

    protected $fillable = [
        'user_ID',
        'inquiry_Title',
        'inquiry_Description',
        'inquiry_Category',
        'inquiry_Attachment_URL',
        'inquiry_Status',
        'inquiry_Created_At'
    ];

    protected $casts = [
        'inquiry_Created_At' => 'datetime',
    ];

    protected $attributes = [
        'inquiry_Status' => 'pending'
    ];

    const INQUIRY_TYPES = [
        'general' => 'General Inquiry',
        'technical' => 'Technical Support',
        'complaint' => 'Complaint',
        'suggestion' => 'Suggestion',
        'other' => 'Other'
    ];

    const STATUSES = [
        'pending' => 'Pending',
        'in_progress' => 'In Progress',
        'completed' => 'Completed',
        'rejected' => 'Rejected',
        'assigned_to_agency' => 'Assigned to Agency',
        'agency_review_in_progress' => 'Agency Review in Progress',
        'agency_review_completed' => 'Agency Review Completed',
        'agency_rejected' => 'Rejected by Agency'
    ];

    public function getInquiryCreatedAtMalaysiaAttribute()
    {
        return $this->inquiry_Created_At ? $this->inquiry_Created_At->setTimezone('Asia/Kuala_Lumpur') : null;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(PublicUser::class, 'user_ID', 'user_ID');
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class, 'inquiry_ID', 'inquiry_ID');
    }

    public function assignments()
    {
        return $this->hasManyThrough(
            InquiryAssignment::class,
            Approval::class,
            'inquiry_ID',
            'approval_ID',
            'inquiry_ID',
            'approval_ID'
        );
    }

    public function progress()
    {
        return $this->hasMany(InquiryProgress::class, 'inquiry_ID', 'inquiry_ID');
    }

    public function progressRecords()
    {
        return $this->hasMany(InquiryProgress::class, 'inquiry_ID', 'inquiry_ID');
    }

    public function latestProgress()
    {
        return $this->hasOne(InquiryProgress::class, 'inquiry_ID', 'inquiry_ID')
            ->latest('progress_Updated_At');
    }

    public function currentAssignment()
    {
        return $this->assignments()
            ->whereIn('assignment_Status', ['pending', 'in_progress'])
            ->latest('assignment_Date')
            ->first();
    }

    public function assignedAgency()
    {
        $assignment = $this->currentAssignment();
        return $assignment ? $assignment->agency : null;
    }

    public function getCurrentAssignmentWithAgency()
    {
        return $this->assignments()
            ->with('agency')
            ->whereIn('assignment_Status', ['pending', 'in_progress', 'completed'])
            ->latest('assignment_Date')
            ->first();
    }

    public function getAgencyAssignmentInfo()
    {
        $assignment = $this->getCurrentAssignmentWithAgency();

        if (!$assignment || !$assignment->agency) {
            return null;
        }

        return [
            'agency_name' => $assignment->agency->agency_Name,
            'assigned_date' => $assignment->assignment_Date,
            'assignment_status' => $assignment->assignment_Status,
            'assignment_comments' => $assignment->assignment_Comments
        ];
    }

    public function hasAgencyAssignment()
    {
        return $this->getCurrentAssignmentWithAgency() !== null;
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('inquiry_Status', $status);
    }

    public function scopeInquiryType($query, $type)
    {
        return $query->where('inquiry_Category', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('inquiry_Title', 'like', '%' . $search . '%');
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_ID', $userId);
    }

    public function getFormattedInquiryTypeAttribute()
    {
        return self::INQUIRY_TYPES[$this->inquiry_Category] ?? ucfirst($this->inquiry_Category);
    }

    public function getFormattedStatusAttribute()
    {
        return self::STATUSES[$this->inquiry_Status] ?? ucfirst(str_replace('_', ' ', $this->inquiry_Status));
    }

    public function canBeEdited()
    {
        return in_array($this->inquiry_Status, ['pending', 'in_progress']);
    }

    public function canBeProcessed()
    {
        return in_array($this->inquiry_Status, ['pending', 'in_progress']);
    }

    public function canBeAssigned()
    {
        $allowedStatuses = ['submitted', 'pending', 'under_review', 'assigned_to_agency'];
        return in_array($this->inquiry_Status, $allowedStatuses);
    }

    public function getAvailableActions()
    {
        switch ($this->inquiry_Status) {
            case 'pending':
                return ['approve', 'reject', 'in_progress'];
            case 'in_progress':
                return ['approve', 'reject'];
            case 'completed':
            case 'rejected':
            default:
                return [];
        }
    }

    public function createApproval($staffId, $status, $comments = null, $type = 'mcmc_review')
    {
        $validStaffId = DB::table('mcmc_staff')->where('staff_ID', $staffId)->exists()
            ? $staffId
            : DB::table('mcmc_staff')->first()->staff_ID ?? 1;

        return DB::table('approvals')->insert([
            'inquiry_ID' => $this->inquiry_ID,
            'staff_ID' => $validStaffId,
            'approval_Status' => $status,
            'approval_Comments' => $comments,
            'approval_Type' => $type,
            'approval_Date' => now(),
        ]);
    }

    public function getApprovals()
    {
        return DB::table('approvals')
            ->where('inquiry_ID', $this->inquiry_ID)
            ->orderBy('approval_Date', 'desc')
            ->get();
    }

    public function getLatestApproval()
    {
        return DB::table('approvals')
            ->where('inquiry_ID', $this->inquiry_ID)
            ->orderBy('approval_Date', 'desc')
            ->first();
    }

    public function hasAttachment()
    {
        return !empty($this->inquiry_Attachment_URL);
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->inquiry_Attachment_URL ? asset('storage/' . $this->inquiry_Attachment_URL) : null;
    }

    public function getAttachmentFilenameAttribute()
    {
        return $this->inquiry_Attachment_URL ? basename($this->inquiry_Attachment_URL) : null;
    }

    public function getIdAttribute()
    {
        return $this->inquiry_ID;
    }

    public function getSubjectAttribute()
    {
        return $this->inquiry_Title;
    }

    public function getDescriptionAttribute()
    {
        return $this->inquiry_Description;
    }

    public function getInquiryTypeAttribute()
    {
        return $this->inquiry_Category;
    }

    public function getStatusAttribute()
    {
        return $this->inquiry_Status;
    }

    public function getAttachmentAttribute()
    {
        return $this->inquiry_Attachment_URL;
    }

    public function getCreatedAtAttribute()
    {
        return $this->inquiry_Created_At;
    }

    public function getUpdatedAtAttribute()
    {
        return $this->inquiry_Created_At;
    }
}
