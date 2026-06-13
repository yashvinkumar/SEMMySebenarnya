<?php

namespace Database\Seeders;

use App\Models\InquiryAssignment;
use Illuminate\Database\Seeder;

class BackfillSlaDataSeeder extends Seeder
{
    /**
     * Backfill SLA data for existing assignment records.
     *
     * For assignments that already exist before the migration:
     * - Set due_date = assignment_Date + 7 days (if due_date is null)
     * - Set sla_status = 'On Time' (if null)
     *
     * Run: php artisan db:seed --class=BackfillSlaDataSeeder
     */
    public function run(): void
    {
        $assignments = InquiryAssignment::whereNull('due_date')->get();

        $count = 0;
        foreach ($assignments as $assignment) {
            if ($assignment->assignment_Date) {
                $assignment->due_date = $assignment->assignment_Date->copy()->addDays(7);
            } else {
                // If no assignment_Date, use current time as fallback
                $assignment->due_date = now()->addDays(7);
            }

            if (!$assignment->sla_status) {
                $assignment->sla_status = 'On Time';
            }

            $assignment->save();
            $count++;
        }

        $this->command->info("Backfilled SLA data for {$count} existing assignment records.");
    }
}