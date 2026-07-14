<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class InternReportExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles, WithMapping, WithColumnFormatting
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        \App\Models\Submission::autoFailExpiredTasks();

        return User::where('role', 'intern')
            ->with(['internProfile', 'submissions' => function ($query) {
                $query->where('status', 'graded')->whereNotNull('score');
            }])
            ->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Nama Magang',
            'NIM/NIP',
            'Universitas',
            'Rata-Rata Nilai',
        ];
    }

    /**
     * @param mixed $intern
     * @return array
     */
    public function map($intern): array
    {
        $gradedSubmissions = $intern->submissions;
        $gradedCount = $gradedSubmissions->count();
        $averageScore = $gradedCount > 0 
            ? round($gradedSubmissions->avg('score'), 2) 
            : 0;

        return [
            $intern->name,
            strval($intern->username), // Explicitly cast to string
            $intern->internProfile->university ?? '-',
            $averageScore,
        ];
    }

    /**
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT, // Format NIM/NIP column as text
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings) as bold
            1 => ['font' => ['bold' => true]],
        ];
    }
}
