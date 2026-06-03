<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\InquiryAssignment;
use App\Models\InquirySubmissionRecord;

class AssignmentStatusUpdatedNotification extends Notification
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
        $statusLabels = [
            'pending' => 'Pending Review',
            'in_progress' => 'Under Review',
            'completed' => 'Review Completed',
            'rejected' => 'Rejected'
        ];

        $statusLabel = $statusLabels[$this->assignment->assignment_Status] ?? ucfirst($this->assignment->assignment_Status);

        if ($notifiable instanceof \App\Models\UserRecord) {
            // Notification to user
            $mailMessage = (new MailMessage)
                ->subject('Assignment Status Update - Inquiry #' . $this->inquiry->inquiry_ID)
                ->greeting('Hello ' . $notifiable->name)
                ->line('There has been an update to your inquiry assignment.')
                ->line('**Inquiry:** ' . $this->inquiry->inquiry_Title)
                ->line('**Agency:** ' . $this->assignment->agency->agency_Name)
                ->line('**Status:** ' . $statusLabel);

            if ($this->assignment->assignment_Status === 'completed') {
                $mailMessage->line('Great news! The agency has completed their review of your inquiry.')
                    ->line('You can now view the results and any comments from the agency.');

                // Add completion details if available
                if ($this->assignment->assignment_Comments) {
                    $mailMessage->line('**Review Summary:**')
                        ->line($this->assignment->assignment_Comments);
                }
            } elseif ($this->assignment->assignment_Status === 'rejected') {
                $mailMessage->line('The agency has determined that your inquiry does not fall within their jurisdiction.')
                    ->line('MCMC will reassign your inquiry to the appropriate agency.');

                // Add rejection reason if available
                if ($this->assignment->rejection_Reason) {
                    $mailMessage->line('**Reason for Rejection:**')
                        ->line($this->assignment->rejection_Reason);
                }
            } elseif ($this->assignment->assignment_Status === 'in_progress') {
                $mailMessage->line('The agency has accepted and started reviewing your inquiry.')
                    ->line('They will provide updates as the review progresses.');

                // Add progress details if available
                if ($this->assignment->assignment_Comments) {
                    $mailMessage->line('**Agency Comments:**')
                        ->line($this->assignment->assignment_Comments);
                }
            }

            return $mailMessage->action('View Inquiry', url('/user/inquiries/' . $this->inquiry->inquiry_ID))
                ->line('Thank you for using our inquiry system.');
        } else {
            // Notification to MCMC staff
            return (new MailMessage)
                ->subject('Assignment Status Update - Inquiry #' . $this->inquiry->inquiry_ID)
                ->greeting('Hello')
                ->line('An assignment status has been updated.')
                ->line('**Inquiry:** ' . $this->inquiry->inquiry_Title)
                ->line('**Agency:** ' . $this->assignment->agency->agency_Name)
                ->line('**New Status:** ' . $statusLabel)
                ->line('**User:** ' . $this->inquiry->user->name)
                ->action('View Assignment', url('/mcmc/assignments/' . $this->assignment->assignment_ID))
                ->line('Please review the assignment details.');
        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $statusLabels = [
            'pending' => 'Pending Review',
            'in_progress' => 'Under Review',
            'completed' => 'Review Completed',
            'rejected' => 'Rejected'
        ];

        $statusLabel = $statusLabels[$this->assignment->assignment_Status] ?? ucfirst($this->assignment->assignment_Status);

        if ($notifiable instanceof \App\Models\UserRecord) {
            return [
                'type' => 'assignment_status_updated_user',
                'inquiry_id' => $this->inquiry->inquiry_ID,
                'inquiry_title' => $this->inquiry->inquiry_Title,
                'agency_name' => $this->assignment->agency->agency_Name,
                'assignment_status' => $this->assignment->assignment_Status,
                'status_label' => $statusLabel,
                'assignment_id' => $this->assignment->assignment_ID,
                'message' => 'Assignment status updated to: ' . $statusLabel,
                'action_url' => '/user/inquiries/' . $this->inquiry->inquiry_ID
            ];
        } else {
            return [
                'type' => 'assignment_status_updated_staff',
                'inquiry_id' => $this->inquiry->inquiry_ID,
                'inquiry_title' => $this->inquiry->inquiry_Title,
                'agency_name' => $this->assignment->agency->agency_Name,
                'assignment_status' => $this->assignment->assignment_Status,
                'status_label' => $statusLabel,
                'assignment_id' => $this->assignment->assignment_ID,
                'user_name' => $this->inquiry->user->name,
                'message' => 'Assignment status updated by ' . $this->assignment->agency->agency_Name . ': ' . $statusLabel,
                'action_url' => '/mcmc/assignments/' . $this->assignment->assignment_ID
            ];
        }
    }
}
