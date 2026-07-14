@extends('layouts.app')

@section('content')
<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="welcome-text mb-1 notion-headline">Dashboard</h3>
            <p class="text-muted mb-0 small" style="color: var(--colors-ink-muted) !important;">Selamat datang di Sistem Informasi Manajemen Magang</p>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card notion-card">
        <div class="card-body py-5 text-center">
            <div class="fs-1 text-primary mb-3"><i class="mdi mdi-hand-wave"></i></div>
            <h4 class="fw-bold text-dark notion-headline mb-2">Halo, {{ Auth::user()->name }}!</h4>
            <p class="text-muted mb-4">Anda telah berhasil masuk ke dalam sistem. Silakan navigasikan menu di sebelah kiri untuk mengelola program magang Anda.</p>
            <span class="notion-sticker-badge sticker-teal">Role Anda: {{ strtoupper(Auth::user()->role) }}</span>
        </div>
    </div>
</div>
@endsection
