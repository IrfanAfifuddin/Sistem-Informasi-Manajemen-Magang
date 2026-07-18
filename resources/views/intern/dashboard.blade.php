@extends('layouts.app')

@section('content')
<style>
    .notion-banner {
        border-radius: 8px !important;
        padding: 16px 20px !important;
        border: 1px solid transparent !important;
        margin-bottom: 20px !important;
    }
    .notion-banner-success {
        background-color: rgba(26, 174, 57, 0.08) !important;
        color: var(--colors-accent-green) !important;
        border-color: rgba(26, 174, 57, 0.15) !important;
    }
    .notion-banner-warning {
        background-color: rgba(221, 91, 0, 0.08) !important;
        color: var(--colors-accent-orange) !important;
        border-color: rgba(221, 91, 0, 0.15) !important;
    }
</style>

<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="welcome-text mb-1 notion-headline">Intern Workspace</h3>
            <p class="text-muted mb-0 small" style="color: var(--colors-ink-muted) !important;">Pantau pengerjaan tugas, rekap nilai, serta unduh sertifikat kelulusan</p>
        </div>
    </div>

    <div x-data="{
        openSubmit: false,
        submitData: {},
        openSubmitModal(task) {
            this.submitData = {
                id: task.id,
                title: task.title,
                description: task.description,
                link_url: task.latest_submission ? task.latest_submission.link_url : ''
            };
            this.openSubmit = true;
        }
    }">

        <!-- Certificate success banner -->
        @if ($profile && $profile->certificate_path)
            <div class="notion-banner notion-banner-success d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="fs-2"><i class="mdi mdi-certificate text-success"></i></div>
                    <div>
                        <h5 class="fw-bold mb-1" style="color: var(--colors-accent-green) !important;">Selamat! Program Magang Anda Telah Selesai</h5>
                        <p class="mb-0 small" style="color: var(--colors-ink-secondary) !important;">Sertifikat Kelulusan resmi Anda telah diterbitkan. Unduh sertifikat Anda sekarang.</p>
                    </div>
                </div>
                <div>
                    <a href="{{ asset('storage/' . $profile->certificate_path) }}" target="_blank" class="btn-notion-primary py-2 px-3">
                        <i class="mdi mdi-download"></i> Unduh Sertifikat
                    </a>
                </div>
            </div>
        @endif

        <!-- Warning banner for first login -->
        @if (auth()->user()->is_first_login)
            <div class="notion-banner notion-banner-warning d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-alert-circle fs-4"></i>
                    <div>
                        <strong>Perhatian:</strong> Akun Anda masih menggunakan password default. Silakan ubah password Anda di halaman profil demi keamanan.
                    </div>
                </div>
                <div>
                    <a href="{{ route('profile.edit') }}" class="btn-notion-primary py-2 px-3" style="background-color: var(--colors-accent-orange) !important;">
                        Ubah Password Sekarang
                    </a>
                </div>
            </div>
        @endif

        <!-- Success toast -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px; background-color: rgba(26, 174, 57, 0.1); color: var(--colors-accent-green);">
                <div class="d-flex align-items-center gap-2">
                    <i class="mdi mdi-checkbox-marked-circle-outline fs-5"></i>
                    <div><strong>Sukses!</strong> {{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Error messages -->
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

        <!-- Main Layout: Profile and Statistics -->
        <div class="row">
            <!-- Left Column: Profile Info Card -->
            <div class="col-lg-4 grid-margin">
                <!-- Profile Card -->
                <div class="card notion-card">
                    <div class="card-body">
                        <div class="text-center pb-4 mb-3 border-bottom" style="border-color: var(--colors-hairline) !important;">
                            <div class="w-20 h-20 bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 fs-2 fw-extrabold text-primary" style="width: 80px; height: 80px; background-color: rgba(0, 75, 222, 0.08) !important; color: var(--colors-primary) !important;">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <h4 class="font-bold text-dark mb-1 notion-headline">{{ auth()->user()->name }}</h4>
                            <p class="text-muted small mb-0" style="color: var(--colors-primary) !important; font-weight: 600;">NIM: {{ auth()->user()->username }}</p>
                        </div>

                        <div class="d-flex flex-column gap-3 small">
                            <div>
                                <span class="text-muted d-block small mb-1" style="color: var(--colors-ink-muted) !important;">Universitas</span>
                                <span class="fw-bold text-dark">{{ $profile->university ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-muted d-block small mb-1" style="color: var(--colors-ink-muted) !important;">Jurusan</span>
                                <span class="fw-bold text-dark">{{ $profile->major ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-muted d-block small mb-1" style="color: var(--colors-ink-muted) !important;">Periode Magang</span>
                                <span class="fw-bold text-dark">
                                    @if($profile)
                                        {{ \Carbon\Carbon::parse($profile->start_date)->format('d M Y') }} - 
                                        {{ \Carbon\Carbon::parse($profile->end_date)->format('d M Y') }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="pt-3 border-t" style="border-color: var(--colors-hairline) !important;">
                                <span class="text-muted d-block small mb-2" style="color: var(--colors-ink-muted) !important;">Mentor Bimbingan</span>
                                <span class="notion-sticker-badge sticker-purple">
                                    <i class="mdi mdi-account-tie me-1"></i>{{ $mentor->name ?? 'Belum Ditugaskan' }}
                                </span>
                            </div>
                            @if($profile && $profile->certificate_path)
                                <div class="pt-3 border-t" style="border-color: var(--colors-hairline) !important;">
                                    <span class="text-muted d-block small mb-2" style="color: var(--colors-accent-green) !important; font-weight: 700;">Sertifikat Kelulusan</span>
                                    <a href="{{ asset('storage/' . $profile->certificate_path) }}" target="_blank" class="w-100 btn-notion-primary py-2 text-decoration-none text-center">
                                        <i class="mdi mdi-download"></i> Unduh Sertifikat
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Dokumen Magang Card -->
                <div class="card notion-card mt-4">
                    <div class="card-body">
                        <h4 class="card-title mb-3 text-dark fw-bold notion-headline">Dokumen Magang</h4>
                        
                        <div class="d-flex flex-column gap-3 small">
                            <div>
                                <span class="text-muted d-block small mb-1" style="color: var(--colors-ink-muted) !important;">Status Surat Permohonan:</span>
                                @if($profile && $profile->application_letter_path)
                                    <span class="notion-sticker-badge sticker-green mb-2 d-inline-block">
                                        <i class="mdi mdi-check-circle-outline me-1"></i>Sudah Diunggah
                                    </span>
                                    <button type="button" data-file-url="{{ asset('storage/' . $profile->application_letter_path) }}" class="w-100 btn-notion-primary text-white d-flex align-items-center justify-content-center gap-2 btn-view-task-doc" style="font-size: 13px; font-weight: 600; padding: 10px 15px; border-radius: 9999px; cursor: pointer;">
                                        <i class="mdi mdi-file-document-outline"></i> Lihat Surat
                                    </button>
                                @else
                                    <span class="notion-sticker-badge sticker-orange mb-3 d-inline-block">
                                        <i class="mdi mdi-alert-circle-outline me-1"></i>Belum Diunggah
                                    </span>
                                    
                                    <form action="{{ route('intern.upload_letter') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="letter" class="form-label text-muted small mb-1" style="color: var(--colors-ink-muted) !important;">Upload Surat Permohonan (PDF/JPG/PNG, Max 2MB):</label>
                                            <input class="form-control form-control-sm" type="file" id="letter" name="letter" accept=".pdf,.jpg,.png" required style="border-radius: 6px;">
                                        </div>
                                        <button type="submit" class="w-100 btn-notion-primary text-white d-flex align-items-center justify-content-center gap-2" style="font-size: 13px; font-weight: 600; padding: 10px 15px; border-radius: 9999px; cursor: pointer;">
                                            <i class="mdi mdi-upload"></i> Unggah Surat
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Stats & Tasks List Workspace -->
            <div class="col-lg-8 grid-margin stretch-card">
                <div class="w-100 d-flex flex-column gap-4">
                    <!-- Stats Grid -->
                    <div class="row">
                        <!-- Stat Average -->
                        <div class="col-sm-3 mb-3 mb-sm-0">
                            <div class="card notion-stats-card purple w-100">
                                <div class="d-flex flex-column align-items-start">
                                    <span class="stats-label">Rata-Rata</span>
                                    <div class="mt-2 d-flex align-items-baseline">
                                        <span class="stats-value text-primary" style="color: #903df5 !important;">{{ $averageScore }}</span>
                                        <span class="text-muted small ms-1">/100</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Stat Total Tasks -->
                        <div class="col-sm-3 mb-3 mb-sm-0">
                            <div class="card notion-stats-card sky w-100">
                                <div class="d-flex flex-column align-items-start">
                                    <span class="stats-label">Total Tugas</span>
                                    <span class="stats-value mt-2">{{ $tasksCount }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stat Graded Tasks -->
                        <div class="col-sm-3 mb-3 mb-sm-0">
                            <div class="card notion-stats-card green w-100">
                                <div class="d-flex flex-column align-items-start">
                                    <span class="stats-label">Tugas Dinilai</span>
                                    <span class="stats-value mt-2 text-success" style="color: var(--colors-accent-green) !important;">{{ $gradedCount }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stat Pending Tasks -->
                        <div class="col-sm-3">
                            <div class="card notion-stats-card orange w-100">
                                <div class="d-flex flex-column align-items-start">
                                    <span class="stats-label">Belum Selesai</span>
                                    <span class="stats-value mt-2 text-warning" style="color: var(--colors-accent-orange) !important;">{{ $pendingCount }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks List Workspace -->
                    <div class="card notion-card w-100">
                        <div class="card-body">
                            <h4 class="card-title mb-3 text-dark fw-bold notion-headline">Daftar Tugas & Status</h4>
                            <div class="d-flex flex-column gap-3">
                                @forelse($tasks as $task)
                                    <div class="p-3 rounded-lg border bg-light d-flex flex-column md:flex-row justify-content-between align-items-start align-items-md-center gap-3" style="border-radius: 8px !important; border-color: var(--colors-hairline) !important; background-color: var(--colors-canvas-soft) !important;">
                                        <div class="flex-grow-1" style="max-width: 78%;">
                                            <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                                                <h5 class="fw-bold mb-0 text-dark notion-headline" style="font-size: 15px !important;">{{ $task->title }}</h5>
                                                @if($task->status === 'graded')
                                                    <span class="notion-sticker-badge sticker-green"><i class="mdi mdi-checkbox-marked-circle"></i> NILAI: {{ $task->latestSubmission->score }}/100</span>
                                                @elseif($task->status === 'submitted')
                                                    <span class="notion-sticker-badge sticker-sky"><i class="mdi mdi-clock-outline"></i> MENUNGGU PENILAIAN</span>
                                                @elseif($task->status === 'expired')
                                                    <span class="notion-sticker-badge sticker-pink"><i class="mdi mdi-close-circle-outline"></i> EXPIRED (0)</span>
                                                @else
                                                    <span class="notion-sticker-badge sticker-orange"><i class="mdi mdi-timer-sand"></i> PENDING</span>
                                                @endif
                                            </div>
                                            <p class="mb-2 text-muted small" style="color: var(--colors-ink-secondary) !important;">{{ $task->description }}</p>
                                            
                                            <div class="d-flex flex-wrap gap-3 align-items-center">
                                                @if($task->attachment_path)
                                                    <button type="button" data-file-url="{{ asset('storage/' . $task->attachment_path) }}" class="notion-sticker-badge sticker-sky text-decoration-none small btn-view-task-doc" style="cursor: pointer;">
                                                        <i class="mdi mdi-download"></i> Lampiran Instruksi
                                                    </button>
                                                @endif
                                                <span class="small text-muted" style="color: var(--colors-ink-muted) !important;">
                                                    <i class="mdi mdi-calendar-clock me-1"></i>Deadline: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y H:i') }}
                                                </span>
                                            </div>

                                            <!-- Grading Feedback if Graded -->
                                            @if($task->status === 'graded' && $task->latestSubmission->feedback)
                                                <div class="mt-2 p-2 rounded text-xs" style="background-color: rgba(26, 174, 57, 0.08); border: 1px solid rgba(26, 174, 57, 0.12); color: var(--colors-accent-green);">
                                                    <strong>Feedback Mentor:</strong> "{{ $task->latestSubmission->feedback }}"
                                                </div>
                                            @endif
                                        </div>

                                        <div class="align-self-stretch align-self-md-center text-end">
                                            @if($task->status === 'graded')
                                                <span class="notion-sticker-badge sticker-grey">Selesai</span>
                                            @elseif($task->status === 'submitted')
                                                <button type="button" @click="openSubmitModal({{ json_encode($task) }})" class="btn-notion-utility w-100 btn-trigger-modal" data-target-modal="#submitTaskModal">
                                                    Kumpulkan Ulang
                                                </button>
                                            @elseif($task->status === 'expired')
                                                <span class="notion-sticker-badge sticker-pink">Tidak Mengerjakan</span>
                                            @else
                                                <button type="button" @click="openSubmitModal({{ json_encode($task) }})" class="btn-notion-primary w-100 text-white btn-trigger-modal" data-target-modal="#submitTaskModal">
                                                    Kumpulkan
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted small text-center py-4">Belum ada tugas yang diberikan oleh mentor.</p>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL: Submit Task -->
        <div class="modal fade" id="submitTaskModal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content notion-card">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2"><i class="mdi mdi-upload text-primary fs-4"></i> Pengumpulan Tugas</h5>
                            <small class="text-muted" x-text="submitData.title"></small>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form :action="'/intern/tasks/' + submitData.id + '/submit'" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="file-upload" class="form-label">Unggah File Lampiran (PDF, ZIP, DOCX, PNG. Maks 10MB)</label>
                                <input type="file" name="file" id="file-upload" class="form-control">
                            </div>
                            <div class="d-flex align-items-center justify-content-center my-3">
                                <hr class="flex-grow-1" style="border-top: 1px solid var(--colors-hairline) !important;">
                                <span class="mx-3 text-muted small fw-bold">ATAU</span>
                                <hr class="flex-grow-1" style="border-top: 1px solid var(--colors-hairline) !important;">
                            </div>
                            <div class="mb-3">
                                <label for="link_url" class="form-label">Tautan Link URL (GitHub, Google Drive, dll.)</label>
                                <input type="url" name="link_url" id="link_url" x-model="submitData.link_url" class="form-control" placeholder="https://github.com/username/project">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-notion-utility" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn-notion-primary">Kirim Tugas</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    </div>
</div>

<!-- MODAL: Lihat Lampiran Tugas -->
<div class="modal fade" id="taskDocumentModal" tabindex="-1" role="dialog" aria-labelledby="taskDocumentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold text-dark" id="taskDocumentModalLabel">Pratinjau Lampiran Tugas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="taskDocumentModalBody" class="text-center"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $(document).on('click', '.btn-view-task-doc', function(e) {
        e.preventDefault();
        var fileUrl = $(this).attr('data-file-url');
        
        if (!fileUrl) return;

        var isPdf = fileUrl.toLowerCase().endsWith('.pdf');
        var content = '';

        if (isPdf) {
            content = '<iframe src="' + fileUrl + '" style="width:100%; height:70vh;" frameborder="0"></iframe>';
        } else {
            content = '<img src="' + fileUrl + '" class="img-fluid rounded" alt="Lampiran Tugas">';
        }

        $('#taskDocumentModalBody').html(content);
        $('#taskDocumentModal').modal('show');
    });

    // Clear modal body on close to prevent flashing old content
    $('#taskDocumentModal').on('hidden.bs.modal', function () {
        $('#taskDocumentModalBody').html('');
    });
});
</script>
@endpush
@endsection
