<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;

class UserRecord extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'contact_number',
        'password',
        'user_type',
        'agency_ID',
        'temporary_password',
        'force_password_reset',
        'email_verified_at',
        'profile_picture',
        'email_notifications',
        'sms_notifications',
        'language',
        'timezone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'temporary_password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'force_password_reset' => 'boolean',
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
        ];
    }

    /**
     * Check if the user needs to reset their password
     */
    public function needsPasswordReset(): bool
    {
        return $this->force_password_reset === true;
    }

    /**
     * Determine if the user has verified their email address.
     *
     * @return bool
     */
    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark the given user's email as verified.
     *
     * @return bool
     */
    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();
    }

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new \Illuminate\Auth\Notifications\VerifyEmail);
    }

    /**
     * Get the email address that should be used for verification.
     *
     * @return string
     */
    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    /**
     * Get the guard name based on user type.
     *
     * @return string
     */
    public function getGuardName(): string
    {
        return $this->user_type;
    }

    /**
     * Check if the user is a public user.
     *
     * @return bool
     */
    public function isPublicUser(): bool
    {
        return $this->user_type === 'public';
    }

    /**
     * Check if the user is MCMC staff.
     *
     * @return bool
     */
    public function isMcmcStaff(): bool
    {
        return $this->user_type === 'mcmc';
    }

    /**
     * Check if the user is an agency user.
     *
     * @return bool
     */
    public function isAgency(): bool
    {
        return $this->user_type === 'agency';
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        // Map common role names to user_type values
        $roleMapping = [
            'admin' => 'mcmc',
            'mcmc' => 'mcmc',
            'staff' => 'mcmc',
            'public' => 'public',
            'agency' => 'agency',
        ];

        $mappedRole = $roleMapping[strtolower($role)] ?? strtolower($role);

        return strtolower($this->user_type) === $mappedRole;
    }

    /**
     * Get the login redirect route based on user type.
     *
     * @return string
     */
    public function getLoginRedirectRoute(): string
    {
        return match ($this->user_type) {
            'public' => 'public.dashboard',
            'mcmc' => 'mcmc.dashboard',
            'agency' => 'agency.dashboard',
            default => 'public.dashboard',
        };
    }

    /**
     * Get the agency that this user belongs to (for agency users)
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_ID', 'agency_ID');
    }
}
