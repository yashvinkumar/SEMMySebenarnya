<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InquiryProgress extends Model
{
    use HasFactory;

    protected $table = 'inquiry_progress';
    protected $primaryKey = 'progress_ID';
    public $timestamps = false;

    protected $fillable = [
        'inquiry_ID',
        'agency_ID',
        'user_ID',
        'staff_ID',
        'progress_Status',
        'progress_Remarks',
        'progress_Updated_At'
    ];

    protected $casts = [
        'progress_Updated_At' => 'datetime',
    ];

    /**
     * Get the inquiry associated with this progress record
     */
    public function inquiry(): BelongsTo
    {
        return $this->belongsTo(InquirySubmissionRecord::class, 'inquiry_ID', 'inquiry_ID');
    }

    /**
     * Get the agency associated with this progress record
     */
    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class, 'agency_ID', 'agency_ID');
    }

    /**
     * Get the user associated with this progress record
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserRecord::class, 'user_ID', 'user_ID');
    }

    /**
     * Get the MCMC staff associated with this progress record
     */
    public function staff(): BelongsTo
    {
        return $this->belongsTo(McmcStaff::class, 'staff_ID', 'staff_ID');
    }

    /**
     * Get the formatted progress status
     */
    public function getFormattedStatusAttribute()
    {
        return ucfirst(str_replace('_', ' ', $this->progress_Status));
    }

    public function assignment()
    {
        return $this->belongsTo(InquiryAssignment::class, 'assignment_ID', 'assignment_ID');
    }
}
