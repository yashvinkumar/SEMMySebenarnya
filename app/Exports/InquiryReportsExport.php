<?php

namespace App\Exports;

use App\Models\InquirySubmissionRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class InquiryReportsExport implements FromCollection, WithHeadings
{
    protected $startDate;
    protected $endDate;
    protected $year;
    protected $month;

    public function __construct($startDate, $endDate, $year = null, $month = null)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->year = $year;
        $this->month = $month;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = InquirySubmissionRecord::with('user');

        // Apply year and month filters if provided
        if ($this->year && $this->month) {
            // Filter by specific year and month
            $query->whereYear('inquiry_Created_At', $this->year)
                  ->whereMonth('inquiry_Created_At', $this->month);
        } elseif ($this->year) {
            // Filter by year only
            $query->whereYear('inquiry_Created_At', $this->year);
        } elseif ($this->month) {
            // Filter by month only (current year)
            $query->whereMonth('inquiry_Created_At', $this->month)
                  ->whereYear('inquiry_Created_At', now()->year);
        } else {
            // Use date range filters if no year/month specified
            $query->whereDate('inquiry_Created_At', '>=', $this->startDate)
                  ->whereDate('inquiry_Created_At', '<=', $this->endDate);
        }

        return $query->get()->map(function ($inquiry) {
                return [
                    'ID' => $inquiry->inquiry_ID,
                    'Title' => $inquiry->inquiry_Title,
                    'Category' => $inquiry->inquiry_Category,
                    'Status' => $inquiry->inquiry_Status,
                    'Created At' => $inquiry->inquiry_Created_At,
                    'User' => $inquiry->user->name ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Category',
            'Status',
            'Created At',
            'User',
        ];
    }
}
