<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Agency extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'agencies';
    protected $primaryKey = 'agency_ID';
    public $timestamps = false;

    protected $fillable = [
        'agency_Name',
        'agency_Type',
        'agency_Email',
        'agency_Phone',
        'agency_Password',
        'agency_First_Time_Login',
        'agency_Created_At',
        'agency_Updated_At'
    ];

    protected $hidden = [
        'agency_Password',
    ];

    protected $casts = [
        'agency_First_Time_Login' => 'boolean',
        'agency_Created_At' => 'datetime',
        'agency_Updated_At' => 'datetime',
    ];

    /**
     * Get the password attribute name for authentication
     */
    public function getAuthPassword()
    {
        return $this->agency_Password;
    }

    /**
     * Get the unique identifier for the user
     */
    public function getAuthIdentifierName()
    {
        return 'agency_ID';
    }

    /**
     * Get the unique identifier for the user
     */
    public function getAuthIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Get the name of the unique identifier for the user
     */
    public function getKeyName()
    {
        return 'agency_ID';
    }

    /**
     * Get the assignments for this agency
     */
    public function assignments()
    {
        return $this->hasMany(InquiryAssignment::class, 'agency_ID', 'agency_ID');
    }

    /**
     * Get the users associated with this agency (if applicable)
     */
    public function users()
    {
        return $this->hasMany(UserRecord::class, 'agency_ID', 'agency_ID');
    }

    /**
     * Get the inquiry progress records for this agency
     */
    public function progressRecords()
    {
        return $this->hasMany(InquiryProgress::class, 'agency_ID', 'agency_ID');
    }

    /**
     * Get all inquiries handled by this agency through assignments
     */
    public function inquiries()
    {
        return $this->hasManyThrough(
            InquirySubmissionRecord::class,
            InquiryAssignment::class,
            'agency_ID',
            'inquiry_ID',
            'agency_ID',
            'approval_ID'
        )->join('approvals', 'inquiry_assignments.approval_ID', '=', 'approvals.approval_ID');
    }

    /**
     * Check if this is the first time login
     */
    public function isFirstTimeLogin()
    {
        return $this->agency_First_Time_Login === true;
    }

    /**
     * Mark first time login as completed
     */
    public function markFirstTimeLoginCompleted()
    {
        $this->agency_First_Time_Login = false;
        $this->save();
    }

    /**
     * Get formatted agency type
     */
    public function getFormattedAgencyTypeAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->agency_Type));
    }

    /**
     * Get agency creation date in readable format
     */
    public function getCreatedAtFormattedAttribute()
    {
        return $this->agency_Created_At ? $this->agency_Created_At->format('M d, Y') : 'N/A';
    }

    /**
     * Scope a query to only include active agencies
     */
    public function scopeActive($query)
    {
        return $query->whereNotNull('agency_Email');
    }

    /**
     * Scope a query to filter by agency type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('agency_Type', $type);
    }

    /**
     * Get the pending assignments count
     */
    public function getPendingAssignmentsCount()
    {
        return $this->assignments()->where('assignment_Status', 'pending')->count();
    }

    /**
     * Get the in progress assignments count
     */
    public function getInProgressAssignmentsCount()
    {
        return $this->assignments()->where('assignment_Status', 'in_progress')->count();
    }

    /**
     * Get the completed assignments count
     */
    public function getCompletedAssignmentsCount()
    {
        return $this->assignments()->where('assignment_Status', 'completed')->count();
    }

    /**
     * Scope a query to search agencies by name
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('agency_Name', 'like', '%' . $search . '%');
    }
}
