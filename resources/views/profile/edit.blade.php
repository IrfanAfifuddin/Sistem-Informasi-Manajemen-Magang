@extends('layouts.app')

@section('content')
<style>
    /* Profile styling overrides */
    .profile-card {
        max-width: 650px;
    }
    /* Ensure the sub-components look perfect */
    .profile-card input[type="text"],
    .profile-card input[type="email"],
    .profile-card input[type="password"] {
        width: 100% !important;
        border-radius: 4px !important;
        border: 1px solid #ddd !important;
        font-size: 14px !important;
        padding: 8px 12px !important;
        background-color: var(--colors-canvas) !important;
        color: var(--colors-ink) !important;
    }
    .profile-card input[type="text"]:focus,
    .profile-card input[type="email"]:focus,
    .profile-card input[type="password"]:focus {
        border-color: var(--colors-primary) !important;
        box-shadow: rgba(0,75,222,0.15) 0 0 0 3px !important;
        outline: none !important;
    }
    
    /* Notion primary buttons override inside forms */
    .profile-card button,
    .profile-card button[type="submit"] {
        background-color: var(--colors-primary) !important;
        color: #ffffff !important;
        border-radius: 9999px !important;
        font-weight: 500 !important;
        border: none !important;
        padding: 8px 20px !important;
        font-size: 14px !important;
        transition: background-color 0.15s ease;
    }
    .profile-card button:hover,
    .profile-card button[type="submit"]:hover {
        background-color: var(--colors-primary-active) !important;
    }
    
    /* Danger button overrides */
    .profile-card .bg-red-600,
    .profile-card button.btn-danger,
    .profile-card button.bg-red-600 {
        background-color: #ef4444 !important;
    }
    .profile-card .bg-red-600:hover,
    .profile-card button.btn-danger:hover,
    .profile-card button.bg-red-600:hover {
        background-color: #dc2626 !important;
    }
</style>

<div class="content-wrapper">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="welcome-text mb-1 notion-headline">Edit Profil</h3>
            <p class="text-muted mb-0 small" style="color: var(--colors-ink-muted) !important;">Kelola detail profil, perbarui kata sandi, dan kelola keamanan akun Anda</p>
        </div>
    </div>

    @if (session('warning'))
        <div class="alert alert-warning alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px; background-color: rgba(221, 91, 0, 0.08); color: var(--colors-accent-orange);">
            <div class="d-flex align-items-center gap-2">
                <i class="mdi mdi-alert-circle-outline fs-5"></i>
                <div><strong>Peringatan!</strong> {{ session('warning') }}</div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex flex-column gap-4">
        <!-- Update Profile Info Card -->
        <div class="card notion-card profile-card">
            <div class="card-body p-4">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <!-- Update Password Card -->
        <div class="card notion-card profile-card">
            <div class="card-body p-4">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <!-- Delete User Card -->
        <div class="card notion-card profile-card">
            <div class="card-body p-4">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</div>
@endsection
