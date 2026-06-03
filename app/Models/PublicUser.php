<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class PublicUser extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'public_users';
    protected $primaryKey = 'user_ID';
    public $timestamps = false;

    protected $fillable = [
        'user_Name',
        'user_Email',
        'user_Phone_Number',
        'user_Password',
        'user_Status',
        'user_Created_At',
        'user_Updated_At'
    ];

    protected $hidden = [
        'user_Password',
    ];

    protected $casts = [
        'user_Created_At' => 'datetime',
        'user_Updated_At' => 'datetime',
    ];

    /**
     * Get the password attribute for authentication
     */
    public function getAuthPassword()
    {
        return $this->user_Password;
    }

    /**
     * Get the unique identifier for the user
     */
    public function getAuthIdentifierName()
    {
        return 'user_ID';
    }

    /**
     * Get the inquiries submitted by this user
     */
    public function inquiries()
    {
        return $this->hasMany(InquirySubmissionRecord::class, 'user_ID', 'user_ID');
    }

    /**
     * Get the name attribute (for compatibility)
     */
    public function getNameAttribute()
    {
        return $this->user_Name;
    }

    /**
     * Get the email attribute (for compatibility)
     */
    public function getEmailAttribute()
    {
        return $this->user_Email;
    }
}
