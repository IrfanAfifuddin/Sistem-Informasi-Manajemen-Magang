<x-app-layout>
    <div class="py-4" x-data="{ 
        activeTab: 'interns', 
        openCreateIntern: false, 
        openCreateUser: false,
        openEditIntern: false,
        openEditUser: false,
        editUserData: {},
        editInternData: {},
        openEditInternModal(intern) {
            this.editInternData = {
                id: intern.id,
                name: intern.name,
                email: intern.email,
                nim: intern.username,
                university: intern.intern_profile ? intern.intern_profile.university : '',
                major: intern.intern_profile ? intern.intern_profile.major : '',
                start_date: intern.intern_profile ? intern.intern_profile.start_date.split('T')[0] : '',
                end_date: intern.intern_profile ? intern.intern_profile.end_date.split('T')[0] : '',
                mentor_id: intern.intern_profile ? intern.intern_profile.mentor_id : ''
            };
            this.openEditIntern = true;
        },
        openEditUserModal(user) {
            this.editUserData = {
                id: user.id,
                name: user.name,
                email: user.email,
                username: user.username,
                role: user.role
            };
            this.openEditUser = true;
        }
    }">
        <div class="container-fluid px-4">

            <!-- Alert Toast -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert" style="border-radius: 8px; background-color: rgba(26, 174, 57, 0.1); color: var(--colors-accent-green);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline fs-5"></i>
                        <div><strong>Sukses!</strong> {{ session('success') }}</div>
                    </div>
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="row mb-4">
                <!-- Card Interns -->
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="card-title text-dark">Total Anak Magang (Interns)</h4>
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="text-dark font-weight-bold mb-0">{{ $internsCount }}</h2>
                                <div class="p-3 bg-light rounded-circle text-primary">
                                    <i class="mdi mdi-account-multiple fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Mentors -->
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="card-title text-dark">Total Mentor</h4>
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="text-dark font-weight-bold mb-0">{{ $mentorsCount }}</h2>
                                <div class="p-3 bg-light rounded-circle text-success">
                                    <i class="mdi mdi-account-tie fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Admins -->
                <div class="col-md-4 grid-margin stretch-card">
                    <div class="card shadow-sm border-0">
                        <div class="card-body">
                            <h4 class="card-title text-dark">Total Administrator</h4>
                            <div class="d-flex justify-content-between align-items-center">
                                <h2 class="text-dark font-weight-bold mb-0">{{ $adminsCount }}</h2>
                                <div class="p-3 bg-light rounded-circle text-info">
                                    <i class="mdi mdi-shield-account fs-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{ 'active': activeTab === 'interns' }" @click="activeTab = 'interns'" type="button">
                        Daftar Anak Magang (Interns)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{ 'active': activeTab === 'mentors' }" @click="activeTab = 'mentors'" type="button">
                        Daftar Mentor
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" :class="{ 'active': activeTab === 'admins' }" @click="activeTab = 'admins'" type="button">
                        Daftar Admin
                    </button>
                </li>
            </ul>

            <!-- Interns Table Panel -->
            <div x-show="activeTab === 'interns'" class="card shadow-sm border-0 mb-4" style="display: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title text-dark mb-0">Anak Magang</h4>
                        <button @click="openCreateIntern = true" class="btn btn-primary btn-sm text-white">
                            Tambah Anak Magang
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>NIM / Nama</th>
                                    <th>Universitas / Jurusan</th>
                                    <th>Masa Magang</th>
                                    <th>Mentor</th>
                                    <th>First Login?</th>
                                    <th>DOKUMEN</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users->where('role', 'intern') as $user)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark mb-1">{{ $user->name }}</div>
                                            <small class="text-muted d-block mb-1">NIM: {{ $user->username }}</small>
                                            <small class="text-muted d-block">{{ $user->email ?? 'No Email' }}</small>
                                        </td>
                                        <td>
                                            <div class="text-dark mb-1">{{ $user->internProfile->university ?? '-' }}</div>
                                            <small class="text-muted d-block">{{ $user->internProfile->major ?? '-' }}</small>
                                        </td>
                                        <td>
                                            @if($user->internProfile)
                                                {{ \Carbon\Carbon::parse($user->internProfile->start_date)->format('d M Y') }} - 
                                                {{ \Carbon\Carbon::parse($user->internProfile->end_date)->format('d M Y') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->internProfile && $user->internProfile->mentor)
                                                <span class="badge bg-purple text-purple-800">
                                                    {{ $user->internProfile->mentor->name }}
                                                </span>
                                            @else
                                                <span class="badge bg-danger text-white">
                                                    Belum Ditentukan
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->is_first_login)
                                                <span class="badge bg-warning text-dark">YA</span>
                                            @else
                                                <span class="badge bg-success text-white">TIDAK</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($user->internProfile && $user->internProfile->application_letter_path)
                                                    <a href="{{ asset('storage/' . $user->internProfile->application_letter_path) }}" target="_blank" class="btn btn-primary btn-xs text-white text-decoration-none">
                                                        Lihat Surat
                                                    </a>
                                                @endif
                                                @if($user->internProfile && $user->internProfile->certificate_path)
                                                    <a href="{{ asset('storage/' . $user->internProfile->certificate_path) }}" target="_blank" class="btn btn-success btn-xs text-white text-decoration-none">
                                                        Lihat Sertifikat
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button" @click="openEditInternModal({{ json_encode($user) }})" class="btn btn-sm btn-primary text-white">Edit</button>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger text-white">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">Belum ada data anak magang.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Mentors Table Panel -->
            <div x-show="activeTab === 'mentors'" class="card shadow-sm border-0 mb-4" style="display: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title text-dark mb-0">Mentor</h4>
                        <button @click="openCreateUser = true; editUserData.role = 'mentor'" class="btn btn-primary btn-sm text-white">
                            Tambah Mentor
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama / Username</th>
                                    <th>Email</th>
                                    <th>Jumlah Anak Magang</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users->where('role', 'mentor') as $user)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark mb-1">{{ $user->name }}</div>
                                            <small class="text-muted d-block">Username: {{ $user->username }}</small>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge bg-indigo text-indigo-800">
                                                {{ $user->internProfilesForMentor->count() }} Interns
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button" @click="openEditUserModal({{ json_encode($user) }})" class="btn btn-sm btn-primary text-white">Edit</button>
                                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mentor ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger text-white">Delete</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4">Belum ada data mentor.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Admins Table Panel -->
            <div x-show="activeTab === 'admins'" class="card shadow-sm border-0 mb-4" style="display: none;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title text-dark mb-0">Administrator</h4>
                        <button @click="openCreateUser = true; editUserData.role = 'admin'" class="btn btn-primary btn-sm text-white">
                            Tambah Admin
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama / Username</th>
                                    <th>Email</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users->where('role', 'admin') as $user)
                                    <tr>
                                        <td>
                                            <div class="fw-bold text-dark mb-1">{{ $user->name }}</div>
                                            <small class="text-muted d-block">Username: {{ $user->username }}</small>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->id !== auth()->id())
                                                <div class="d-flex align-items-center gap-2">
                                                    <button type="button" @click="openEditUserModal({{ json_encode($user) }})" class="btn btn-sm btn-primary text-white">Edit</button>
                                                    <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus admin ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger text-white">Delete</button>
                                                    </form>
                                                </div>
                                            @else
                                                <small class="text-muted italic">Akun Anda</small>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center py-4">Belum ada data admin.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- MODAL: Create Intern -->
            <div x-show="openCreateIntern" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
                <div @click.away="openCreateIntern = false" class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl max-w-lg w-full border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tambah Anak Magang (Intern)</h3>
                        <button @click="openCreateIntern = false" class="text-gray-400 hover:text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form action="{{ route('admin.interns.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input type="text" name="name" class="block mt-1 w-full" required />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nim" :value="__('NIM (Student ID)')" />
                                <x-text-input type="text" name="nim" class="block mt-1 w-full" placeholder="NIM sebagai username & default password" required />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Email (Opsional)')" />
                                <x-text-input type="email" name="email" class="block mt-1 w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="university" :value="__('Universitas')" />
                                <x-text-input type="text" name="university" class="block mt-1 w-full" required />
                            </div>
                            <div>
                                <x-input-label for="major" :value="__('Jurusan')" />
                                <x-text-input type="text" name="major" class="block mt-1 w-full" required />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                                <x-text-input type="date" name="start_date" class="block mt-1 w-full" required />
                            </div>
                            <div>
                                <x-input-label for="end_date" :value="__('Tanggal Selesai')" />
                                <x-text-input type="date" name="end_date" class="block mt-1 w-full" required />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="mentor_id" :value="__('Petakan ke Mentor')" />
                            <select name="mentor_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-350 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">-- Pilih Mentor --</option>
                                @foreach($mentors as $mentor)
                                    <option value="{{ $mentor->id }}">{{ $mentor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" @click="openCreateIntern = false" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">Batal</button>
                            <x-primary-button>Simpan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL: Create User (Admin / Mentor) -->
            <div x-show="openCreateUser" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
                <div @click.away="openCreateUser = false" class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl max-w-md w-full border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editUserData.role === 'mentor' ? 'Tambah Mentor' : 'Tambah Admin'">Tambah User</h3>
                        <button @click="openCreateUser = false" class="text-gray-400 hover:text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form action="{{ route('admin.users.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <input type="hidden" name="role" x-model="editUserData.role">
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input type="text" name="name" class="block mt-1 w-full" required />
                        </div>
                        <div>
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input type="text" name="username" class="block mt-1 w-full" required />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input type="email" name="email" class="block mt-1 w-full" required />
                        </div>
                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input type="password" name="password" class="block mt-1 w-full" required />
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" @click="openCreateUser = false" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">Batal</button>
                            <x-primary-button>Simpan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL: Edit Intern -->
            <div x-show="openEditIntern" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
                <div @click.away="openEditIntern = false" class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl max-w-lg w-full border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Anak Magang (Intern)</h3>
                        <button @click="openEditIntern = false" class="text-gray-400 hover:text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form :action="'/admin/interns/' + editInternData.id" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input type="text" name="name" x-model="editInternData.name" class="block mt-1 w-full" required />
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="nim" :value="__('NIM (Student ID)')" />
                                <x-text-input type="text" name="nim" x-model="editInternData.nim" class="block mt-1 w-full" required />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Email (Opsional)')" />
                                <x-text-input type="email" name="email" x-model="editInternData.email" class="block mt-1 w-full" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="university" :value="__('Universitas')" />
                                <x-text-input type="text" name="university" x-model="editInternData.university" class="block mt-1 w-full" required />
                            </div>
                            <div>
                                <x-input-label for="major" :value="__('Jurusan')" />
                                <x-text-input type="text" name="major" x-model="editInternData.major" class="block mt-1 w-full" required />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="start_date" :value="__('Tanggal Mulai')" />
                                <x-text-input type="date" name="start_date" x-model="editInternData.start_date" class="block mt-1 w-full" required />
                            </div>
                            <div>
                                <x-input-label for="end_date" :value="__('Tanggal Selesai')" />
                                <x-text-input type="date" name="end_date" x-model="editInternData.end_date" class="block mt-1 w-full" required />
                            </div>
                        </div>
                        <div>
                            <x-input-label for="mentor_id" :value="__('Petakan ke Mentor')" />
                            <select name="mentor_id" x-model="editInternData.mentor_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-350 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                                <option value="">-- Pilih Mentor --</option>
                                @foreach($mentors as $mentor)
                                    <option value="{{ $mentor->id }}">{{ $mentor->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="password" :value="__('Ubah Password (Kosongkan jika tidak diubah)')" />
                            <x-text-input type="password" name="password" class="block mt-1 w-full" placeholder="Password baru" />
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" @click="openEditIntern = false" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">Batal</button>
                            <x-primary-button>Update</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL: Edit User -->
            <div x-show="openEditUser" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
                <div @click.away="openEditUser = false" class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl max-w-md w-full border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="editUserData.role === 'mentor' ? 'Edit Mentor' : 'Edit Admin'">Edit User</h3>
                        <button @click="openEditUser = false" class="text-gray-400 hover:text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form :action="'/admin/users/' + editUserData.id" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="name" :value="__('Nama Lengkap')" />
                            <x-text-input type="text" name="name" x-model="editUserData.name" class="block mt-1 w-full" required />
                        </div>
                        <div>
                            <x-input-label for="username" :value="__('Username')" />
                            <x-text-input type="text" name="username" x-model="editUserData.username" class="block mt-1 w-full" required />
                        </div>
                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input type="email" name="email" x-model="editUserData.email" class="block mt-1 w-full" required />
                        </div>
                        <div>
                            <x-input-label for="password" :value="__('Ubah Password (Kosongkan jika tidak diubah)')" />
                            <x-text-input type="password" name="password" class="block mt-1 w-full" />
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" @click="openEditUser = false" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">Batal</button>
                            <x-primary-button>Update</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
