<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class InquiryAssignmentReportExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    private $reportData;
    private $filters;

    public function __construct($reportData, $filters)
    {
        $this->reportData = $reportData;
        $this->filters = $filters;
    }

    /**
     * Return array of data for Excel
     */
    public function array(): array
    {
        $data = [];

        // Add summary section
        $data[] = ['INQUIRY ASSIGNMENT REPORT'];
        $data[] = ['Generated on: ', Carbon::now()->format('Y-m-d H:i:s')];
        $data[] = ['Date Range: ', ($this->filters['date_from'] ?? 'N/A') . ' to ' . ($this->filters['date_to'] ?? 'N/A')];
        $data[] = ['Filter by Agency: ', ($this->filters['agency_id'] ?? null) ? 'Yes' : 'All Agencies'];
        $data[] = ['Filter by Status: ', ($this->filters['status'] ?? null) ? ucfirst($this->filters['status']) : 'All Statuses'];
        $data[] = ['Group by: ', ucfirst($this->filters['group_by'] ?? 'agency')];
        $data[] = []; // Empty row

        // Add detailed data
        foreach($this->reportData as $groupKey => $assignments) {
            $data[] = [strtoupper($groupKey)]; // Group header
            $data[] = $this->getSubHeadings(); // Column headers

            foreach($assignments as $assignment) {
                $data[] = [
                    $assignment->assignment_ID ?? '',
                    $assignment->inquiry_Title ?? '',
                    $assignment->user_name ?? '',
                    $assignment->user_email ?? '',
                    $assignment->agency_Name ?? '',
                    $assignment->agency_Type ?? '',
                    $assignment->inquiry_Category ?? '',
                    $assignment->assignment_Status ?? '',
                    $assignment->assignment_Date ? Carbon::parse($assignment->assignment_Date)->format('Y-m-d H:i') : '',
                    $assignment->completed_At ? Carbon::parse($assignment->completed_At)->format('Y-m-d H:i') : '',
                    $assignment->assigned_by_name ?? '',
                    $assignment->assignment_Comments ?? '',
                ];
            }
            $data[] = []; // Empty row between groups
        }

        return $data;
    }

    /**
     * Return headings for the Excel sheet
     */
    public function headings(): array
    {
        return [];
    }

    /**
     * Return sub headings for data table
     */
    private function getSubHeadings(): array
    {
        return [
            'Assignment ID',
            'Inquiry Title',
            'User Name',
            'User Email',
            'Agency Name',
            'Agency Type',
            'Category',
            'Status',
            'Assignment Date',
            'Completed Date',
            'Assigned By',
            'Comments'
        ];
    }

    /**
     * Apply styles to the worksheet
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the title row
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 16,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],

            // Style for header rows
            'A:L' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Set column widths
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 30,
            'C' => 20,
            'D' => 25,
            'E' => 25,
            'F' => 20,
            'G' => 15,
            'H' => 15,
            'I' => 20,
            'J' => 20,
            'K' => 20,
            'L' => 30,
        ];
    }

    /**
     * Set worksheet title
     */
    public function title(): string
    {
        return 'Inquiry Assignment Report';
    }
}
