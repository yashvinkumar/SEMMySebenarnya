<?php

namespace App\Notifications;

use App\Models\InquirySubmissionRecord;
use App\Models\Agency;
use App\Models\InquiryAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InquiryReassignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $inquiry;
    protected $newAgency;
    protected $assignment;

    /**
     * Create a new notification instance.
     */
    public function __construct(InquirySubmissionRecord $inquiry, Agency $newAgency, InquiryAssignment $assignment)
    {
        $this->inquiry = $inquiry;
        $this->newAgency = $newAgency;
        $this->assignment = $assignment;
    }

    /**
     * Get the notification's delivery channels.
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
            ->subject('Your Inquiry Has Been Reassigned - MySebenarnya')
            ->greeting('Dear ' . ($notifiable->name ?? 'User'))
            ->line('Your inquiry has been reassigned to a different agency for better handling.')
            ->line('**Inquiry Details:**')
            ->line('- **ID:** #' . $this->inquiry->inquiry_ID)
            ->line('- **Subject:** ' . $this->inquiry->inquiry_Title)
            ->line('- **Category:** ' . $this->inquiry->formatted_inquiry_type)
            ->line('')
            ->line('**New Assignment Details:**')
            ->line('- **Agency:** ' . $this->newAgency->agency_Name)
            ->line('- **Agency Type:** ' . $this->newAgency->formatted_agency_type)
            ->line('- **Assigned Date:** ' . $this->assignment->assignment_Date->format('M d, Y H:i'))
            ->line('')
            ->when($this->assignment->assignment_Comments, function ($message) {
                return $message->line('**Assignment Notes:** ' . $this->assignment->assignment_Comments);
            })
            ->line('The new agency will review your inquiry and get back to you soon. You may receive separate communication from them regarding next steps.')
            ->action('View Inquiry Status', url('/user/inquiries/' . $this->inquiry->inquiry_ID))
            ->line('Thank you for using MySebenarnya. If you have any questions, please don\'t hesitate to contact us.');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'inquiry_reassigned',
            'inquiry_id' => $this->inquiry->inquiry_ID,
            'inquiry_title' => $this->inquiry->inquiry_Title,
            'new_agency_id' => $this->newAgency->agency_ID,
            'new_agency_name' => $this->newAgency->agency_Name,
            'assignment_id' => $this->assignment->assignment_ID,
            'assignment_date' => $this->assignment->assignment_Date->toISOString(),
            'assignment_comments' => $this->assignment->assignment_Comments,
            'message' => "Your inquiry #{$this->inquiry->inquiry_ID} has been reassigned to {$this->newAgency->agency_Name}",
            'action_url' => url('/user/inquiries/' . $this->inquiry->inquiry_ID)
        ];
    }

    /**
     * Get the notification's database type.
     */
    public function databaseType(object $notifiable): string
    {
        return 'inquiry_reassigned';
    }
}
