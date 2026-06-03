<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\InquiryAssignment;
use App\Models\InquirySubmissionRecord;

class AssignmentRejectedNotification extends Notification
{
    use Queueable;

    protected $assignment;
    protected $inquiry;

    /**
     * Create a new notification instance.
     */
    public function __construct(InquiryAssignment $assignment, InquirySubmissionRecord $inquiry)
    {
        $this->assignment = $assignment;
        $this->inquiry = $inquiry;
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
        return (new MailMessage)
            ->subject('Assignment Rejected - Inquiry #' . $this->inquiry->inquiry_ID)
            ->greeting('Hello')
            ->line('An assignment has been rejected by the agency and requires your attention.')
            ->line('**Inquiry:** ' . $this->inquiry->inquiry_Title)
            ->line('**Agency:** ' . $this->assignment->agency->agency_Name)
            ->line('**User:** ' . $this->inquiry->user->name)
            ->line('**Rejection Reason:** ' . $this->assignment->rejection_Reason)
            ->line('The inquiry needs to be reassigned to an appropriate agency.')
            ->action('View Assignment', url('/mcmc/assignments/' . $this->assignment->assignment_ID . '/details'))
            ->line('Please review and reassign this inquiry promptly.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'assignment_rejected',
            'inquiry_id' => $this->inquiry->inquiry_ID,
            'inquiry_title' => $this->inquiry->inquiry_Title,
            'agency_name' => $this->assignment->agency->agency_Name,
            'assignment_id' => $this->assignment->assignment_ID,
            'rejection_reason' => $this->assignment->rejection_Reason,
            'user_name' => $this->inquiry->user->name,
            'message' => 'Assignment rejected by ' . $this->assignment->agency->agency_Name . ' - requires reassignment',
            'action_url' => '/mcmc/assignments/' . $this->assignment->assignment_ID . '/details'
        ];
    }
}
