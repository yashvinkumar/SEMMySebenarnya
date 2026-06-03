<?php

namespace App\Services;

use App\Models\InquirySubmissionRecord;
use App\Models\Agency;
use App\Models\InquiryAssignment;
use App\Models\Approval;
use App\Notifications\InquiryAssignedNotification;
use App\Notifications\InquiryReassignedNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;

class InquiryAssignmentService
{
    /**
     * Assign an inquiry to an agency
     */
    public function assignInquiry(InquirySubmissionRecord $inquiry, array $data): InquiryAssignment
    {
        DB::beginTransaction();

        try {
            $agency = Agency::findOrFail($data['agency_id']);

            // Check if this is a reassignment
            $isReassignment = $inquiry->currentAssignment() !== null;

            if ($isReassignment) {
                $this->handleReassignment($inquiry, $data['reassignment_reason'] ?? '');
            }

            // Create approval record
            $approval = $this->createApprovalRecord($inquiry, [
                'status' => 'assigned',
                'comments' => $data['comments'] ?? '',
                'type' => $isReassignment ? 'reassignment' : 'agency_assignment'
            ]);

            // Create assignment record
            $assignment = $this->createAssignmentRecord($inquiry, $agency, $approval, $data);

            // Update inquiry status
            $this->updateInquiryStatus($inquiry, 'assigned_to_agency');

            // Send notifications
            $this->sendAssignmentNotifications($inquiry, $assignment, $isReassignment);

            // Log the action
            Log::info('Inquiry assigned to agency', [
                'inquiry_id' => $inquiry->inquiry_ID,
                'agency_id' => $agency->agency_ID,
                'staff_id' => Auth::guard('mcmc')->id(),
                'is_reassignment' => $isReassignment
            ]);

            DB::commit();

            return $assignment;

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Failed to assign inquiry to agency', [
                'inquiry_id' => $inquiry->inquiry_ID,
                'agency_id' => $data['agency_id'] ?? null,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    /**
     * Bulk assign multiple inquiries to an agency
     */
    public function bulkAssignInquiries(array $inquiryIds, array $data): array
    {
        $inquiries = InquirySubmissionRecord::whereIn('inquiry_ID', $inquiryIds)->get();
        $agency = Agency::findOrFail($data['agency_id']);

        $results = [
            'successful' => [],
            'failed' => [],
            'total' => $inquiries->count()
        ];

        foreach ($inquiries as $inquiry) {
            try {
                // Check if inquiry can be assigned
                if (!$this->canBeAssigned($inquiry)) {
                    $results['failed'][] = [
                        'inquiry_id' => $inquiry->inquiry_ID,
                        'reason' => 'Inquiry cannot be assigned in its current state'
                    ];
                    continue;
                }

                // Assign the inquiry
                $assignment = $this->assignInquiry($inquiry, $data);

                $results['successful'][] = [
                    'inquiry_id' => $inquiry->inquiry_ID,
                    'assignment_id' => $assignment->assignment_ID
                ];

            } catch (\Exception $e) {
                $results['failed'][] = [
                    'inquiry_id' => $inquiry->inquiry_ID,
                    'reason' => $e->getMessage()
                ];
            }
        }

        // Log bulk assignment results
        Log::info('Bulk assignment completed', [
            'agency_id' => $data['agency_id'],
            'staff_id' => Auth::guard('mcmc')->id(),
            'successful_count' => count($results['successful']),
            'failed_count' => count($results['failed']),
            'total_count' => $results['total']
        ]);

        return $results;
    }

    /**
     * Get inquiry assignment statistics
     */
    public function getAssignmentStatistics(int $agencyId = null): array
    {
        $query = InquiryAssignment::query();

        if ($agencyId) {
            $query->where('agency_ID', $agencyId);
        }

        $stats = [
            'total_assignments' => $query->count(),
            'pending_assignments' => $query->where('assignment_Status', 'pending')->count(),
            'in_progress_assignments' => $query->where('assignment_Status', 'in_progress')->count(),
            'completed_assignments' => $query->where('assignment_Status', 'completed')->count(),
            'rejected_assignments' => $query->where('assignment_Status', 'rejected')->count(),
        ];

        // Calculate completion rate
        $stats['completion_rate'] = $stats['total_assignments'] > 0
            ? round(($stats['completed_assignments'] / $stats['total_assignments']) * 100, 2)
            : 0;

        // Calculate average response time for completed assignments
        $averageResponseTime = $query->whereNotNull('completed_At')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, assignment_Date, completed_At)) as avg_hours')
            ->value('avg_hours');

        $stats['average_response_hours'] = $averageResponseTime ? round($averageResponseTime, 2) : 0;

        return $stats;
    }

    /**
     * Get assignment history for an inquiry
     */
    public function getAssignmentHistory(InquirySubmissionRecord $inquiry): \Illuminate\Database\Eloquent\Collection
    {
        return $inquiry->assignments()
            ->with(['agency', 'assignedByStaff', 'approval'])
            ->orderBy('assignment_Date', 'desc')
            ->get();
    }

    /**
     * Check if inquiry can be assigned
     */
    public function canBeAssigned(InquirySubmissionRecord $inquiry): bool
    {
        $allowedStatuses = ['pending', 'under_review', 'assigned_to_agency'];
        return in_array($inquiry->inquiry_Status, $allowedStatuses);
    }

    /**
     * Get available agencies for assignment
     */
    public function getAvailableAgencies(): \Illuminate\Database\Eloquent\Collection
    {
        return Agency::active()
            ->with(['assignments' => function($query) {
                $query->whereIn('assignment_Status', ['pending', 'in_progress']);
            }])
            ->orderBy('agency_Name')
            ->get()
            ->map(function ($agency) {
                $agency->current_workload = $agency->assignments->count();
                return $agency;
            });
    }

    /**
     * Handle reassignment logic
     */
    private function handleReassignment(InquirySubmissionRecord $inquiry, string $reason): void
    {
        $currentAssignment = $inquiry->currentAssignment();

        if ($currentAssignment) {
            $currentAssignment->update([
                'assignment_Status' => 'reassigned',
                'assignment_Comments' => $currentAssignment->assignment_Comments .
                    " [REASSIGNED: {$reason}]"
            ]);
        }
    }

    /**
     * Create approval record
     */
    private function createApprovalRecord(InquirySubmissionRecord $inquiry, array $data): Approval
    {
        $approval = new Approval();
        $approval->inquiry_ID = $inquiry->inquiry_ID;
        $approval->staff_ID = Auth::guard('mcmc')->id();
        $approval->approval_Status = $data['status'];
        $approval->approval_Comments = $data['comments'];
        $approval->approval_Type = $data['type'];
        $approval->approval_Date = now();
        $approval->save();

        return $approval;
    }

    /**
     * Create assignment record
     */
    private function createAssignmentRecord(
        InquirySubmissionRecord $inquiry,
        Agency $agency,
        Approval $approval,
        array $data
    ): InquiryAssignment {
        $assignment = new InquiryAssignment();
        $assignment->agency_ID = $agency->agency_ID;
        $assignment->approval_ID = $approval->approval_ID;
        $assignment->assignment_Date = now();
        $assignment->assignment_Status = 'pending';
        $assignment->assignment_Comments = $data['comments'] ?? '';
        $assignment->assigned_By = Auth::guard('mcmc')->id();
        $assignment->save();

        // Load relationships
        $assignment->load(['agency', 'assignedByStaff']);

        return $assignment;
    }

    /**
     * Update inquiry status
     */
    private function updateInquiryStatus(InquirySubmissionRecord $inquiry, string $status): void
    {
        $inquiry->inquiry_Status = $status;
        $inquiry->save();
    }

    /**
     * Send assignment notifications
     */
    private function sendAssignmentNotifications(
        InquirySubmissionRecord $inquiry,
        InquiryAssignment $assignment,
        bool $isReassignment = false
    ): void {
        try {
            // Notify the user who submitted the inquiry
            if ($inquiry->user) {
                $notificationClass = $isReassignment
                    ? InquiryReassignedNotification::class
                    : InquiryAssignedNotification::class;

                Notification::send(
                    $inquiry->user,
                    new $notificationClass($inquiry, $assignment->agency, $assignment)
                );
            }

            // Notify the assigned agency
            Notification::send(
                $assignment->agency,
                new InquiryAssignedNotification($inquiry, $assignment->agency, $assignment)
            );

        } catch (\Exception $e) {
            // Log notification failure but don't throw exception
            Log::error('Failed to send assignment notifications', [
                'inquiry_id' => $inquiry->inquiry_ID,
                'assignment_id' => $assignment->assignment_ID,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Generate assignment report data
     */
    public function generateAssignmentReport(array $filters = []): array
    {
        $query = InquiryAssignment::with(['inquiry', 'agency', 'assignedByStaff']);

        // Apply filters
        if (isset($filters['start_date'])) {
            $query->whereDate('assignment_Date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('assignment_Date', '<=', $filters['end_date']);
        }

        if (isset($filters['agency_id'])) {
            $query->where('agency_ID', $filters['agency_id']);
        }

        if (isset($filters['status'])) {
            $query->where('assignment_Status', $filters['status']);
        }

        $assignments = $query->orderBy('assignment_Date', 'desc')->get();

        // Generate statistics
        $stats = [
            'total_assignments' => $assignments->count(),
            'by_status' => $assignments->groupBy('assignment_Status')->map->count(),
            'by_agency' => $assignments->groupBy('agency.agency_Name')->map->count(),
            'average_response_time' => $this->calculateAverageResponseTime($assignments),
            'completion_rate' => $this->calculateCompletionRate($assignments)
        ];

        return [
            'assignments' => $assignments,
            'statistics' => $stats,
            'filters' => $filters
        ];
    }

    /**
     * Calculate average response time for assignments
     */
    private function calculateAverageResponseTime($assignments): float
    {
        $completedAssignments = $assignments->where('assignment_Status', 'completed')
            ->whereNotNull('completed_At');

        if ($completedAssignments->isEmpty()) {
            return 0;
        }

        $totalHours = $completedAssignments->sum(function ($assignment) {
            return $assignment->assignment_Date->diffInHours($assignment->completed_At);
        });

        return round($totalHours / $completedAssignments->count(), 2);
    }

    /**
     * Calculate completion rate for assignments
     */
    private function calculateCompletionRate($assignments): float
    {
        if ($assignments->isEmpty()) {
            return 0;
        }

        $completedCount = $assignments->where('assignment_Status', 'completed')->count();
        return round(($completedCount / $assignments->count()) * 100, 2);
    }
}
