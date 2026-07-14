<?php

namespace App\Http\Controllers;

use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Exports\InternReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /**
     * Export the recap report to Excel.
     */
    public function exportExcel()
    {
        return Excel::download(new InternReportExport, 'Laporan_Magang.xlsx');
    }

    /**
     * Display the report / recap page for Admins and Mentors.
     */
    public function index()
    {
        $interns = $this->getInternsReportData();
        return view('reports.index', compact('interns'));
    }

    /**
     * Export the recap report to PDF.
     */
    public function exportPdf()
    {
        $interns = $this->getInternsReportData();

        // Render the reports/pdf blade view into PDF
        $pdf = Pdf::loadView('reports.pdf', compact('interns'))
            ->setPaper('a4', 'landscape'); // Landscape is better for tables

        return $pdf->download('rekap_nilai_magang_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Get compiled data of interns with their average scores.
     */
    protected function getInternsReportData()
    {
        \App\Models\Submission::autoFailExpiredTasks();

        return User::where('role', 'intern')
            ->with(['internProfile.mentor', 'submissions' => function ($query) {
                $query->where('status', 'graded')->whereNotNull('score');
            }])
            ->get()
            ->map(function ($intern) {
                $gradedSubmissions = $intern->submissions;
                $gradedCount = $gradedSubmissions->count();
                $averageScore = $gradedCount > 0 
                    ? round($gradedSubmissions->avg('score'), 2) 
                    : 0;

                // Determine final status/grade note
                $gradeStatus = 'Belum Ada Nilai';
                if ($gradedCount > 0) {
                    if ($averageScore >= 85) {
                        $gradeStatus = 'Sangat Memuaskan (A)';
                    } elseif ($averageScore >= 75) {
                        $gradeStatus = 'Memuaskan (B)';
                    } elseif ($averageScore >= 60) {
                        $gradeStatus = 'Cukup (C)';
                    } else {
                        $gradeStatus = 'Kurang (D/E)';
                    }
                }

                return (object)[
                    'id' => $intern->id,
                    'name' => $intern->name,
                    'nim' => $intern->username,
                    'university' => $intern->internProfile->university ?? '-',
                    'major' => $intern->internProfile->major ?? '-',
                    'mentor_name' => $intern->internProfile->mentor->name ?? 'Belum Ditugaskan',
                    'average_score' => $averageScore,
                    'graded_count' => $gradedCount,
                    'grade_status' => $gradeStatus,
                ];
            });
    }
}
