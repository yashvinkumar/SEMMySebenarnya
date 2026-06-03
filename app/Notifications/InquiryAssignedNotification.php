<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\InquirySubmissionRecord;
use App\Models\Agency;
use App\Models\InquiryAssignment;

class InquiryAssignedNotification extends Notification
{
    use Queueable;

    protected $inquiry;
    protected $agency;
    protected $assignment;
    protected $bulkCount;

    /**
     * Create a new notification instance.
     */
    public function __construct($inquiry = null, $agency = null, $assignment = null, $bulkCount = null)
    {
        $this->inquiry = $inquiry;
        $this->agency = $agency;
        $this->assignment = $assignment;
        $this->bulkCount = $bulkCount;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if ($this->bulkCount) {
            // Bulk assignment notification for agency
            return (new MailMessage)
                ->subject('New Bulk Assignment - ' . $this->bulkCount . ' Inquiries')
                ->greeting('Hello ' . $this->agency->agency_Name)
                ->line('You have been assigned ' . $this->bulkCount . ' new inquiries for verification.')
                ->line('Please log in to your dashboard to review these assignments.')
                ->action('View Assignments', url('/agency/assignments'))
                ->line('Please review these inquiries and determine if they fall within your jurisdiction.');
        }

        if (!$this->inquiry) {
            return new MailMessage;
        }

        if ($notifiable instanceof \App\Models\UserRecord) {
            // Notification to user
            return (new MailMessage)
                ->subject('Your Inquiry Has Been Assigned - #' . $this->inquiry->inquiry_ID)
                ->greeting('Hello ' . $notifiable->name)
                ->line('Your inquiry "' . $this->inquiry->inquiry_Title . '" has been assigned to an agency for verification.')
                ->line('**Agency:** ' . $this->agency->agency_Name)
                ->line('**Assignment Date:** ' . $this->assignment->assignment_Date->format('M d, Y H:i'))
                ->line('The agency will review your inquiry and determine if it falls within their jurisdiction.')
                ->action('View Inquiry Status', url('/user/inquiries/' . $this->inquiry->inquiry_ID))
                ->line('You will be notified of any updates to your inquiry status.');
        } else {
            // Notification to agency
            return (new MailMessage)
                ->subject('New Assignment - Inquiry #' . $this->inquiry->inquiry_ID)
                ->greeting('Hello ' . $this->agency->agency_Name)
                ->line('You have been assigned a new inquiry for verification.')
                ->line('**Inquiry Title:** ' . $this->inquiry->inquiry_Title)
                ->line('**Inquiry ID:** #' . $this->inquiry->inquiry_ID)
                ->line('**Assignment Date:** ' . $this->assignment->assignment_Date->format('M d, Y H:i'))
                ->line('**Submitted by:** ' . $this->inquiry->user->name)
                ->action('Review Assignment', url('/agency/assignments/' . $this->assignment->assignment_ID))
                ->line('Please review this inquiry and determine if it falls within your jurisdiction.');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        if ($this->bulkCount) {
            return [
                'type' => 'inquiry_bulk_assigned',
                'agency_name' => $this->agency->agency_Name,
                'count' => $this->bulkCount,
                'message' => 'You have been assigned ' . $this->bulkCount . ' new inquiries for verification.',
                'action_url' => '/agency/assignments'
            ];
        }

        if (!$this->inquiry) {
            return [];
        }

        if ($notifiable instanceof \App\Models\UserRecord) {
            return [
                'type' => 'inquiry_assigned_to_user',
                'inquiry_id' => $this->inquiry->inquiry_ID,
                'inquiry_title' => $this->inquiry->inquiry_Title,
                'agency_name' => $this->agency->agency_Name,
                'assignment_date' => $this->assignment->assignment_Date->toISOString(),
                'message' => 'Your inquiry has been assigned to ' . $this->agency->agency_Name . ' for verification.',
                'action_url' => '/user/inquiries/' . $this->inquiry->inquiry_ID
            ];
        } else {
            return [
                'type' => 'inquiry_assigned_to_agency',
                'inquiry_id' => $this->inquiry->inquiry_ID,
                'inquiry_title' => $this->inquiry->inquiry_Title,
                'assignment_id' => $this->assignment->assignment_ID,
                'assignment_date' => $this->assignment->assignment_Date->toISOString(),
                'user_name' => $this->inquiry->user->name,
                'message' => 'New inquiry assigned for verification: ' . $this->inquiry->inquiry_Title,
                'action_url' => '/agency/assignments/' . $this->assignment->assignment_ID
            ];
        }
    }
}
