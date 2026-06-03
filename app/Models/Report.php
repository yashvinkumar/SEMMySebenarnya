<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $table = 'reports';
    protected $primaryKey = 'report_ID';
    public $timestamps = false;

    protected $fillable = [
        'staff_ID',
        'report_Title',
        'report_Generated_At',
        'report_Status',
        'report_Type'
    ];

    protected $casts = [
        'report_Generated_At' => 'datetime',
    ];

    /**
     * Report types
     */
    const REPORT_TYPES = [
        'inquiry_summary' => 'Inquiry Summary Report',
        'agency_performance' => 'Agency Performance Report',
        'monthly_statistics' => 'Monthly Statistics Report',
        'user_activity' => 'User Activity Report',
        'system_usage' => 'System Usage Report'
    ];

    /**
     * Report statuses
     */
    const STATUSES = [
        'pending' => 'Pending',
        'generating' => 'Generating',
        'completed' => 'Completed',
        'failed' => 'Failed'
    ];

    /**
     * Get the staff member who generated this report
     */
    public function staff()
    {
        return $this->belongsTo(McmcStaff::class, 'staff_ID', 'staff_ID');
    }

    /**
     * Get the formatted report type
     */
    public function getFormattedTypeAttribute()
    {
        return self::REPORT_TYPES[$this->report_Type] ?? ucfirst(str_replace('_', ' ', $this->report_Type));
    }

    /**
     * Get the formatted status
     */
    public function getFormattedStatusAttribute()
    {
        return self::STATUSES[$this->report_Status] ?? ucfirst(str_replace('_', ' ', $this->report_Status));
    }

    /**
     * Check if report is completed
     */
    public function isCompleted()
    {
        return $this->report_Status === 'completed';
    }

    /**
     * Check if report is pending
     */
    public function isPending()
    {
        return $this->report_Status === 'pending';
    }

    /**
     * Scope for reports by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('report_Type', $type);
    }

    /**
     * Scope for reports by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('report_Status', $status);
    }

    /**
     * Scope for reports by staff
     */
    public function scopeByStaff($query, $staffId)
    {
        return $query->where('staff_ID', $staffId);
    }
}
