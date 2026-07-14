@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="welcome-text mb-1 notion-headline">Rekap Laporan Nilai Anak Magang</h3>
            <p class="text-muted mb-0 small" style="color: var(--colors-ink-muted) !important;">Daftar rekap nilai kumulatif seluruh peserta magang</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('reports.excel') }}" class="btn btn-success btn-sm text-white d-inline-flex align-items-center gap-1">
                <i class="mdi mdi-file-excel"></i> Export to Excel
            </a>
            <a href="{{ route('reports.pdf') }}" class="btn-notion-primary text-decoration-none">
                <i class="mdi mdi-file-pdf"></i> Ekspor PDF
            </a>
        </div>
    </div>

    <!-- Main Card & Table -->
    <div class="card notion-card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="card-title mb-1 text-dark fw-bold notion-headline">Rekap Nilai Akhir</h4>
                    <p class="card-description mb-0 text-muted small" style="color: var(--colors-ink-muted) !important;">Dihitung otomatis dari rata-rata seluruh tugas yang telah dinilai</p>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>NIM / Nama</th>
                            <th>Universitas / Jurusan</th>
                            <th>Mentor</th>
                            <th class="text-center">Tugas Dinilai</th>
                            <th class="text-center">Rata-Rata Nilai</th>
                            <th class="text-center">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($interns as $intern)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark mb-1">{{ $intern->name }}</div>
                                    <div class="text-muted small" style="color: var(--colors-ink-faint) !important;"><i class="mdi mdi-card-account-details-outline me-1"></i>NIM: {{ $intern->nim }}</div>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium mb-1">{{ $intern->university }}</div>
                                    <div class="text-muted small" style="color: var(--colors-ink-muted) !important;">{{ $intern->major }}</div>
                                </td>
                                <td>
                                    <span class="notion-sticker-badge sticker-purple">
                                        <i class="mdi mdi-account-tie me-1"></i>{{ $intern->mentor_name }}
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-dark">
                                    {{ $intern->graded_count }}
                                </td>
                                <td class="text-center">
                                    <span class="fs-5 fw-extrabold text-primary" style="color: var(--colors-primary) !important;">
                                        {{ number_format($intern->average_score, 1) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($intern->graded_count > 0)
                                        @if($intern->average_score >= 75)
                                            <span class="notion-sticker-badge sticker-green">
                                                <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>{{ $intern->grade_status }}
                                            </span>
                                        @elseif($intern->average_score >= 60)
                                            <span class="notion-sticker-badge sticker-orange">
                                                <i class="mdi mdi-alert-circle-outline me-1"></i>{{ $intern->grade_status }}
                                            </span>
                                        @else
                                            <span class="notion-sticker-badge sticker-pink">
                                                <i class="mdi mdi-close-circle-outline me-1"></i>{{ $intern->grade_status }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="notion-sticker-badge sticker-grey">
                                            <i class="mdi mdi-minus-circle-outline me-1"></i>Belum Ada Nilai
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data anak magang untuk rekap nilai.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
