@extends('layouts.app')

@section('content')
<style>
    /* Notion Design System Overrides */
    .welcome-text {
        font-family: 'Inter', sans-serif !important;
        font-weight: 700 !important;
        letter-spacing: -1px !important;
        color: var(--colors-ink) !important;
    }
    .notion-headline {
        font-family: 'Inter', sans-serif !important;
        color: var(--colors-ink) !important;
        font-weight: 700 !important;
        letter-spacing: -0.5px !important;
    }
    
    /* Notion Cards (Flat, hairline border, soft Level-1 layered shadow) */
    .notion-card {
        background-color: var(--colors-canvas) !important;
        border: 1px solid var(--colors-hairline) !important;
        border-radius: 12px !important;
        box-shadow: rgba(0,0,0,0.01) 0 0.175px 1.041px, 
                    rgba(0,0,0,0.02) 0 0.8px 2.925px, 
                    rgba(0,0,0,0.027) 0 2.025px 7.847px, 
                    rgba(0,0,0,0.04) 0 4px 18px !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        overflow: hidden;
    }
    .notion-card:hover {
        transform: translateY(-2px);
        box-shadow: rgba(0,0,0,0.015) 0 0.25px 1.5px, 
                    rgba(0,0,0,0.025) 0 1.2px 4px, 
                    rgba(0,0,0,0.035) 0 3px 10px, 
                    rgba(0,0,0,0.05) 0 6px 24px !important;
    }
    
    /* Notion stats card */
    .notion-stats-card {
        background-color: var(--colors-canvas) !important;
        border: 1px solid var(--colors-hairline) !important;
        border-radius: 12px !important;
        padding: 20px !important;
        box-shadow: rgba(0,0,0,0.01) 0 0.175px 1.041px, 
                    rgba(0,0,0,0.02) 0 0.8px 2.925px, 
                    rgba(0,0,0,0.027) 0 2.025px 7.847px, 
                    rgba(0,0,0,0.04) 0 4px 18px !important;
        display: flex;
        align-items: center;
        position: relative;
    }
    .notion-stats-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }
    .notion-stats-card.sky::before { background-color: var(--colors-accent-sky); }
    .notion-stats-card.purple::before { background-color: var(--colors-accent-purple); }
    .notion-stats-card.teal::before { background-color: var(--colors-accent-teal); }
    .notion-stats-card.green::before { background-color: var(--colors-accent-green); }
    
    .stats-label {
        color: var(--colors-ink-muted) !important;
        font-size: 11px !important;
        font-weight: 600 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stats-value {
        color: var(--colors-ink) !important;
        font-size: 26px !important;
        font-weight: 700 !important;
        line-height: 1;
    }

    /* Notion Badges / Sticker Palette indicators */
    .notion-sticker-badge {
        font-size: 12px !important;
        font-weight: 600 !important;
        letter-spacing: 0.125px !important;
        border-radius: 9999px !important; /* fully pill */
        padding: 4px 10px !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .sticker-teal {
        background-color: rgba(42, 157, 153, 0.1) !important;
        color: var(--colors-accent-teal) !important;
        border: 1px solid rgba(42, 157, 153, 0.15) !important;
    }
    .sticker-pink {
        background-color: rgba(255, 100, 200, 0.1) !important;
        color: var(--colors-accent-pink) !important;
        border: 1px solid rgba(255, 100, 200, 0.15) !important;
    }
    .sticker-orange {
        background-color: rgba(221, 91, 0, 0.1) !important;
        color: var(--colors-accent-orange) !important;
        border: 1px solid rgba(221, 91, 0, 0.15) !important;
    }
    .sticker-sky {
        background-color: rgba(98, 174, 240, 0.1) !important;
        color: var(--colors-accent-sky) !important;
        border: 1px solid rgba(98, 174, 240, 0.15) !important;
    }
    .sticker-purple {
        background-color: rgba(214, 182, 246, 0.15) !important;
        color: #903df5 !important;
        border: 1px solid rgba(214, 182, 246, 0.25) !important;
    }
    .sticker-green {
        background-color: rgba(26, 174, 57, 0.1) !important;
        color: var(--colors-accent-green) !important;
        border: 1px solid rgba(26, 174, 57, 0.15) !important;
    }
    .sticker-grey {
        background-color: rgba(100, 116, 139, 0.1) !important;
        color: #64748b !important;
        border: 1px solid rgba(100, 116, 139, 0.15) !important;
    }

    /* Notion Tables */
    .table-responsive {
        border-radius: 8px;
        overflow: hidden;
    }
    .table th {
        background: var(--colors-canvas-soft) !important;
        font-weight: 700 !important;
        text-transform: uppercase;
        font-size: 0.725rem !important;
        letter-spacing: 0.5px;
        color: var(--colors-ink-muted) !important;
        border: none !important;
        padding: 12px 16px !important;
    }
    .table td {
        padding: 16px 18px !important;
        vertical-align: middle !important;
        border-bottom: 1px solid var(--colors-hairline) !important;
        color: var(--colors-ink-secondary) !important;
    }
    
    /* Notion Buttons */
    .btn-notion-primary {
        background-color: var(--colors-primary) !important;
        color: #ffffff !important;
        border-radius: 9999px !important; /* fully pill */
        font-weight: 500 !important;
        border: none !important;
        padding: 8px 20px !important;
        font-size: 14px !important;
        transition: background-color 0.15s ease, transform 0.1s ease;
    }
    .btn-notion-primary:hover {
        background-color: var(--colors-primary-active) !important;
    }
    .btn-notion-primary:active {
        transform: scale(0.96);
    }
    
    .btn-notion-utility {
        background-color: var(--colors-canvas) !important;
        color: var(--colors-ink-secondary) !important;
        border: 1px solid var(--colors-hairline) !important;
        border-radius: 8px !important; /* tighter 8px */
        font-weight: 500 !important;
        padding: 6px 14px !important;
        font-size: 13px !important;
        transition: background-color 0.15s ease;
    }
    .btn-notion-utility:hover {
        background-color: var(--colors-canvas-soft) !important;
        color: var(--colors-ink) !important;
    }

    .btn-notion-danger {
        background-color: rgba(239, 68, 68, 0.08) !important;
        color: #ef4444 !important;
        border: 1px solid rgba(239, 68, 68, 0.15) !important;
        border-radius: 8px !important;
        font-weight: 500 !important;
        padding: 6px 12px !important;
        font-size: 13px !important;
        transition: background-color 0.15s ease;
    }
    .btn-notion-danger:hover {
        background-color: rgba(239, 68, 68, 0.15) !important;
    }

    /* Modal dialog styling */
    .modal-content {
        border: 1px solid var(--colors-hairline) !important;
        border-radius: 12px !important;
        box-shadow: rgba(0, 0, 0, 0.05) 0 23px 52px !important;
    }
</style>

<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="welcome-text mb-1 notion-headline">Dashboard Mentor</h3>
            <p class="text-muted mb-0 small" style="color: var(--colors-ink-muted) !important;">Kelola bimbingan, tugas, dan rekap nilai anak magang secara real-time</p>
        </div>
        <a href="{{ route('reports.index') }}" class="btn-notion-primary text-decoration-none d-flex align-items-center gap-2">
            <i class="mdi mdi-file-document-outline"></i> Lihat Rekap Laporan
        </a>
    </div>

    <!-- Alert Notifications -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px; background-color: rgba(26, 174, 57, 0.1); color: var(--colors-accent-green);">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-checkbox-marked-circle-outline fs-5"></i>
                <div><strong>Sukses!</strong> {{ session('success') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px; background-color: rgba(239, 68, 68, 0.08); color: #ef4444;">
            <div class="d-flex align-items-start gap-2">
                <i class="mdi mdi-alert-circle-outline fs-5 mt-0.5"></i>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Notion Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card notion-stats-card sky w-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded fs-3" style="background-color: rgba(98, 174, 240, 0.1) !important; color: #0075de !important;">
                        <i class="mdi mdi-account-multiple"></i>
                    </div>
                    <div>
                        <div class="stats-label">Anak Magang Anda</div>
                        <div class="stats-value">{{ $interns->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card notion-stats-card purple w-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded fs-3" style="background-color: rgba(214, 182, 246, 0.15) !important; color: #903df5 !important;">
                        <i class="mdi mdi-file-document-box-multiple"></i>
                    </div>
                    <div>
                        <div class="stats-label">Tugas Diberikan</div>
                        <div class="stats-value">{{ $tasks->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card notion-stats-card teal w-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded fs-3" style="background-color: rgba(42, 157, 153, 0.1) !important; color: var(--colors-accent-teal) !important;">
                        <i class="mdi mdi-checkbox-marked-circle-outline"></i>
                    </div>
                    <div>
                        <div class="stats-label">Total Pengumpulan</div>
                        <div class="stats-value">{{ $submissions->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 grid-margin stretch-card">
            <div class="card notion-stats-card green w-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2 rounded fs-3" style="background-color: rgba(26, 174, 57, 0.1) !important; color: var(--colors-accent-green) !important;">
                        <i class="mdi mdi-check-all"></i>
                    </div>
                    <div>
                        <div class="stats-label">Sudah Dinilai</div>
                        <div class="stats-value">{{ $submissions->where('status', 'graded')->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div x-data="{
        openCreateTask: false,
        openEditTask: false,
        openGrade: false,
        openCertificate: false,
        editTaskData: {},
        gradeData: {},
        certificateData: {},
        openEditTaskModal(task) {
            this.editTaskData = {
                id: task.id,
                title: task.title,
                description: task.description,
                due_date: task.due_date.split(' ')[0] + 'T' + task.due_date.split(' ')[1].substring(0,5),
                attachment_path: task.attachment_path
            };
            this.openEditTask = true;
        },
        openGradeModal(sub) {
            this.gradeData = {
                id: sub.id,
                task_title: sub.task.title,
                intern_name: sub.intern.name,
                file_path: sub.file_path,
                link_url: sub.link_url,
                score: sub.score || '',
                feedback: sub.feedback || '',
                status: sub.status
            };
            this.openGrade = true;
        },
        openCertificateModal(intern) {
            this.certificateData = {
                id: intern.id,
                name: intern.name,
                certificate_path: intern.intern_profile ? intern.intern_profile.certificate_path : ''
            };
            this.openCertificate = true;
        }
    }">
        <div class="row">
            <!-- Left Column: Anak Magang Anda -->
            <div class="col-lg-4 grid-margin stretch-card">
                <div class="card notion-card">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            <h4 class="card-title mb-1 text-dark fw-bold notion-headline">Anak Magang Anda</h4>
                            <p class="card-description mb-0 text-muted small" style="color: var(--colors-ink-muted) !important;">Daftar anak magang bimbingan Anda</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Detail Intern</th>
                                        <th class="text-end">Sertifikat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($interns as $intern)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark mb-1">{{ $intern->name }}</div>
                                                <div class="text-muted small mb-1" style="color: var(--colors-ink-faint) !important;"><i class="mdi mdi-card-account-details-outline me-1"></i>NIM: {{ $intern->username }}</div>
                                                <div class="text-muted small" style="color: var(--colors-ink-muted) !important;"><i class="mdi mdi-school-outline me-1"></i>{{ $intern->internProfile->university ?? '-' }}</div>
                                                <div class="text-muted small text-primary fw-medium" style="color: var(--colors-primary) !important;">{{ $intern->internProfile->major ?? '-' }}</div>
                                            </td>
                                            <td class="text-end">
                                                <button type="button" @click="openCertificateModal({{ json_encode($intern) }})" class="btn-notion-utility btn-xs d-inline-flex align-items-center gap-1 btn-trigger-modal" data-target-modal="#certificateModal">
                                                    <i class="mdi mdi-certificate"></i> Unggah
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted py-4">Belum ada anak magang yang dipetakan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Kelola Tugas -->
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="card notion-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h4 class="card-title mb-1 text-dark fw-bold notion-headline">Kelola Tugas</h4>
                                <p class="card-description mb-0 text-muted small" style="color: var(--colors-ink-muted) !important;">Daftar penugasan yang telah Anda kirimkan</p>
                            </div>
                            @if($interns->isNotEmpty())
                                <button type="button" class="btn-notion-primary d-flex align-items-center gap-1 btn-trigger-modal" data-target-modal="#addTaskModal">
                                    <i class="mdi mdi-plus-circle-outline"></i> Tugas Baru
                                </button>
                            @endif
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Judul & Deskripsi</th>
                                        <th>Penerima</th>
                                        <th>Status</th>
                                        <th>Deadline & Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tasks as $task)
                                        <tr>
                                            <td style="max-width: 320px; white-space: normal;">
                                                <div class="fw-bold text-dark mb-1 fs-6">{{ $task->title }}</div>
                                                <div class="text-muted small mb-2 text-wrap" style="color: var(--colors-ink-secondary) !important;">{{ Str::limit($task->description, 130) }}</div>
                                                @if($task->attachment_path)
                                                    <a href="{{ asset('storage/' . $task->attachment_path) }}" target="_blank" class="notion-sticker-badge sticker-sky text-decoration-none">
                                                        <i class="mdi mdi-download"></i> Lampiran Instruksi
                                                    </a>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="notion-sticker-badge sticker-grey"><i class="mdi mdi-account me-1"></i>{{ $task->intern->name }}</span>
                                            </td>
                                            <td>
                                                @if($task->status === 'graded')
                                                    <span class="notion-sticker-badge sticker-green"><i class="mdi mdi-checkbox-marked-circle"></i> DINILAI</span>
                                                @elseif($task->status === 'submitted')
                                                    <span class="notion-sticker-badge sticker-sky"><i class="mdi mdi-progress-upload"></i> KUMPUL</span>
                                                @else
                                                    <span class="notion-sticker-badge sticker-orange"><i class="mdi mdi-clock-outline"></i> PENDING</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="text-muted small mb-2 fw-medium" style="color: var(--colors-ink-muted) !important;">
                                                    <i class="mdi mdi-calendar-clock me-1"></i>{{ \Carbon\Carbon::parse($task->due_date)->format('d M Y H:i') }}
                                                </div>
                                                <div class="d-flex gap-2">
                                                    <button type="button" @click="openEditTaskModal({{ json_encode($task) }})" class="btn-notion-utility btn-xs btn-trigger-modal" data-target-modal="#editTaskModal">
                                                        Edit
                                                    </button>
                                                    <form action="{{ route('mentor.tasks.destroy', $task->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn-notion-danger btn-xs" onclick="return confirm('Hapus tugas ini?')">
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">Belum ada tugas yang diberikan.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Full Row: Pengumpulan Tugas -->
            <div class="col-md-12 grid-margin stretch-card">
                <div class="card notion-card">
                    <div class="card-body">
                        <div class="mb-3">
                            <h4 class="card-title mb-1 text-dark fw-bold notion-headline">Pengumpulan Tugas Interns</h4>
                            <p class="card-description mb-0 text-muted small" style="color: var(--colors-ink-muted) !important;">Kelola seluruh submissions, beri masukan, dan berikan penilaian akhir</p>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tugas</th>
                                        <th>Intern</th>
                                        <th>Lampiran & Link</th>
                                        <th>Status & Nilai</th>
                                        <th class="text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($submissions as $sub)
                                        <tr>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $sub->task->title }}</div>
                                            </td>
                                            <td>
                                                <span class="notion-sticker-badge sticker-grey"><i class="mdi mdi-account-circle me-1"></i>{{ $sub->intern->name }}</span>
                                            </td>
                                            <td>
                                                @if($sub->file_path)
                                                    <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="btn-notion-utility btn-xs d-inline-flex align-items-center gap-1">
                                                        <i class="mdi mdi-file-pdf-box"></i> Unduh File
                                                    </a>
                                                @endif
                                                @if($sub->link_url)
                                                    <a href="{{ $sub->link_url }}" target="_blank" class="btn-notion-utility btn-xs d-inline-flex align-items-center gap-1">
                                                        <i class="mdi mdi-open-in-new"></i> Tautan Link
                                                    </a>
                                                @endif
                                                @if(!$sub->file_path && !$sub->link_url)
                                                    <span class="text-muted small italic">Tidak ada lampiran berkas</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($sub->status === 'graded')
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <span class="notion-sticker-badge sticker-green"><i class="mdi mdi-check-circle"></i> DINILAI</span>
                                                        <span class="fw-bold text-dark fs-6">{{ $sub->score }} / 100</span>
                                                    </div>
                                                    <div class="text-muted small italic text-wrap" style="max-width: 250px; color: var(--colors-ink-muted) !important;">"{{ Str::limit($sub->feedback, 60) }}"</div>
                                                @elseif($sub->status === 'expired')
                                                    <span class="notion-sticker-badge sticker-pink"><i class="mdi mdi-close-circle-outline"></i> EXPIRED (0)</span>
                                                @else
                                                    <span class="notion-sticker-badge sticker-orange"><i class="mdi mdi-timer-sand"></i> BELUM DINILAI</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                @if($sub->status === 'graded')
                                                    <button type="button" @click="openGradeModal({{ json_encode($sub) }})" class="btn-notion-utility btn-xs btn-trigger-modal" data-target-modal="#gradeModal">
                                                        Ubah Nilai
                                                    </button>
                                                @elseif($sub->status === 'expired')
                                                    <button disabled class="btn btn-notion-utility btn-xs disabled text-muted">
                                                        Expired
                                                    </button>
                                                @else
                                                    <button type="button" @click="openGradeModal({{ json_encode($sub) }})" class="btn-notion-primary btn-xs text-white fw-bold d-inline-flex align-items-center gap-1 btn-trigger-modal" data-target-modal="#gradeModal">
                                                        <i class="mdi mdi-grease-pencil"></i> Nilai Sekarang
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">Belum ada pengumpulan tugas dari anak magang Anda.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: Create Task (Bootstrap 5) -->
        <div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content notion-card">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2" id="addTaskModalLabel">
                            <i class="mdi mdi-plus-box text-primary fs-4"></i> Berikan Tugas Baru
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('mentor.tasks.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div x-data="{ 
                                selected: [], 
                                toggleAll() {
                                    if (this.selected.length === {{ $interns->count() }}) {
                                        this.selected = [];
                                    } else {
                                        this.selected = [
                                            @foreach($interns as $intern)
                                                '{{ $intern->id }}',
                                            @endforeach
                                        ];
                                    }
                                }
                            }" class="mb-3">
                                <label class="form-label fw-bold text-dark">Penerima Tugas (Pilih minimal satu)</label>
                                <div class="border rounded p-3 bg-light" style="max-height: 180px; overflow-y: auto; border-radius: 6px !important;">
                                    @if($interns->isNotEmpty())
                                        <div class="form-check pb-2 mb-2 border-bottom">
                                            <input type="checkbox" id="check-all-create" class="form-check-input" @click="toggleAll()" :checked="selected.length === {{ $interns->count() }}">
                                            <label for="check-all-create" class="form-check-label fw-bold text-primary">Pilih Semua Anak Magang Saya</label>
                                        </div>
                                    @endif
                                    @foreach($interns as $intern)
                                        <div class="form-check mb-1">
                                            <input type="checkbox" name="intern_ids[]" id="intern-create-{{ $intern->id }}" value="{{ $intern->id }}" x-model="selected" class="form-check-input">
                                            <label for="intern-create-{{ $intern->id }}" class="form-check-label text-dark">{{ $intern->name }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="title-create" class="form-label fw-bold text-dark">Judul Tugas</label>
                                <input type="text" name="title" id="title-create" class="form-control" placeholder="Tuliskan nama/topik tugas..." required>
                            </div>
                            <div class="mb-3">
                                <label for="description-create" class="form-label fw-bold text-dark">Instruksi / Deskripsi</label>
                                <textarea name="description" id="description-create" rows="4" class="form-control" placeholder="Jelaskan detail instruksi tugas secara rinci..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="attachment-create" class="form-label fw-bold text-dark">Lampiran File Instruksi (Opsional)</label>
                                <input type="file" name="attachment" id="attachment-create" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="due-create" class="form-label fw-bold text-dark">Batas Waktu (Deadline)</label>
                                <input type="datetime-local" name="due_date" id="due-create" min="{{ date('Y-m-d\TH:i') }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-notion-utility" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-notion-primary">Berikan Tugas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: Edit Task -->
        <div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content notion-card">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2"><i class="mdi mdi-grease-pencil text-primary fs-4"></i> Edit Rincian Tugas</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form :action="'/mentor/tasks/' + editTaskData.id" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="title-edit" class="form-label fw-bold text-dark">Judul Tugas</label>
                                <input type="text" name="title" id="title-edit" x-model="editTaskData.title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="description-edit" class="form-label fw-bold text-dark">Instruksi / Deskripsi</label>
                                <textarea name="description" id="description-edit" rows="4" x-model="editTaskData.description" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="attachment-edit" class="form-label fw-bold text-dark">Ganti Lampiran File (Opsional)</label>
                                <input type="file" name="attachment" id="attachment-edit" class="form-control">
                                <template x-if="editTaskData.attachment_path">
                                    <small class="text-success d-block mt-2 fw-medium"><i class="mdi mdi-checkbox-marked-circle-outline"></i> File instruksi saat ini sudah dilampirkan.</small>
                                </template>
                            </div>
                            <div class="mb-3">
                                <label for="due-edit" class="form-label fw-bold text-dark">Batas Waktu (Deadline)</label>
                                <input type="datetime-local" name="due_date" id="due-edit" x-model="editTaskData.due_date" min="{{ date('Y-m-d\TH:i') }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-notion-utility" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-notion-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: Grade Submission -->
        <div class="modal fade" id="gradeModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content notion-card">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2"><i class="mdi mdi-star text-warning fs-4"></i> Penilaian Tugas</h5>
                            <small class="text-muted" x-text="'Intern: ' + gradeData.intern_name"></small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form :action="'/mentor/submissions/' + gradeData.id + '/grade'" method="POST">
                        @csrf
                        <div class="modal-body">
                            <template x-if="gradeData.status === 'pending' || !gradeData.file_path">
                                <div class="alert alert-danger py-2 mb-3 border-0" style="border-radius: 6px;">
                                    <i class="mdi mdi-alert-outline me-1"></i> Tugas ini belum dikerjakan/dikumpulkan oleh anak magang, sehingga tidak dapat dinilai.
                                </div>
                            </template>
                            <div class="mb-3">
                                <label class="form-label fw-bold text-dark">Lampiran Intern:</label>
                                <div class="p-3 border rounded bg-light" style="border-radius: 6px !important;">
                                    <template x-if="gradeData.file_path">
                                        <a :href="'/storage/' + gradeData.file_path" target="_blank" class="d-flex align-items-center gap-1 text-decoration-none fw-bold">
                                            <i class="mdi mdi-download fs-5"></i> Unduh File Lampiran
                                        </a>
                                    </template>
                                    <template x-if="gradeData.link_url">
                                        <a :href="gradeData.link_url" target="_blank" class="d-flex align-items-center gap-1 text-decoration-none fw-bold mt-1">
                                            <i class="mdi mdi-link-variant fs-5"></i> Buka Tautan Link
                                        </a>
                                    </template>
                                    <template x-if="!gradeData.file_path && !gradeData.link_url">
                                        <span class="text-muted italic small"><i class="mdi mdi-file-hidden me-1"></i>Belum ada berkas/link yang diunggah</span>
                                    </template>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="score-field" class="form-label fw-bold text-dark">Nilai (0 - 100)</label>
                                <input type="number" name="score" id="score-field" min="0" max="100" x-model="gradeData.score" class="form-control" x-bind:disabled="gradeData.status === 'pending' || !gradeData.file_path" placeholder="Masukkan nilai tugas..." required>
                            </div>
                            <div class="mb-3">
                                <label for="feedback-field" class="form-label fw-bold text-dark">Feedback / Ulasan</label>
                                <textarea name="feedback" id="feedback-field" rows="4" x-model="gradeData.feedback" class="form-control" placeholder="Berikan masukan konstruktif atau instruksi revisi..." x-bind:disabled="gradeData.status === 'pending' || !gradeData.file_path"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-notion-utility" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-notion-primary" x-bind:disabled="gradeData.status === 'pending' || !gradeData.file_path" x-bind:class="{'opacity-50': gradeData.status === 'pending' || !gradeData.file_path}">Simpan Nilai</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL: Upload Certificate -->
        <div class="modal fade" id="certificateModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content notion-card">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2"><i class="mdi mdi-certificate text-success fs-4"></i> Unggah Sertifikat Kelulusan</h5>
                            <small class="text-muted" x-text="'Intern: ' + certificateData.name"></small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form :action="'/mentor/interns/' + certificateData.id + '/certificate'" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="certificate-file" class="form-label fw-bold text-dark">Berkas Sertifikat (PDF/Gambar)</label>
                                <input type="file" name="certificate" id="certificate-file" class="form-control" required>
                                <template x-if="certificateData.certificate_path">
                                    <div class="alert alert-success mt-3 mb-0 d-flex justify-content-between align-items-center border-0" style="border-radius: 6px; background-color: rgba(26, 174, 57, 0.1); color: var(--colors-accent-green);">
                                        <span class="small fw-medium"><i class="mdi mdi-check-circle me-1"></i>Sertifikat saat ini sudah diunggah.</span>
                                        <a :href="'/storage/' + certificateData.certificate_path" target="_blank" class="fw-bold text-success text-decoration-underline small">Lihat Berkas</a>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-notion-utility" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-notion-primary">Unggah Sertifikat</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
