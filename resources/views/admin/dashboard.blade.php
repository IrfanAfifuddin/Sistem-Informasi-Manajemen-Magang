<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Admin Dashboard - Intern Management') }}
            </h2>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Lihat Rekap Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{ 
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Alert Toast -->
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200 dark:border-green-800" role="alert">
                    <span class="font-medium">Sukses!</span> {{ session('success') }}
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card Interns -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl p-6 border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Anak Magang (Interns)</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $internsCount }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg text-blue-600 dark:text-blue-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                </div>

                <!-- Card Mentors -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl p-6 border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Mentor</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $mentorsCount }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg text-purple-600 dark:text-purple-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </div>
                </div>

                <!-- Card Admins -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-xl p-6 border border-gray-100 dark:border-gray-700 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Administrator</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $adminsCount }}</p>
                    </div>
                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg text-indigo-600 dark:text-indigo-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="flex border-b border-gray-200 dark:border-gray-700">
                <button @click="activeTab = 'interns'" :class="activeTab === 'interns' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-150">
                    Daftar Anak Magang (Interns)
                </button>
                <button @click="activeTab = 'mentors'" :class="activeTab === 'mentors' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-150">
                    Daftar Mentor
                </button>
                <button @click="activeTab = 'admins'" :class="activeTab === 'admins' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300'" class="py-4 px-6 border-b-2 font-medium text-sm transition-colors duration-150">
                    Daftar Admin
                </button>
            </div>

            <!-- Interns Table Panel -->
            <div x-show="activeTab === 'interns'" class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Anak Magang</h3>
                    <button @click="openCreateIntern = true" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold uppercase tracking-widest rounded-lg shadow-sm transition-colors duration-150">
                        Tambah Anak Magang
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-750">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">NIM / Nama</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Universitas / Jurusan</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Masa Magang</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Mentor</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">First Login?</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users->where('role', 'intern') as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">NIM: {{ $user->username }}</div>
                                        <div class="text-xs text-gray-400">{{ $user->email ?? 'No Email' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $user->internProfile->university ?? '-' }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->internProfile->major ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($user->internProfile)
                                            {{ \Carbon\Carbon::parse($user->internProfile->start_date)->format('d M Y') }} - 
                                            {{ \Carbon\Carbon::parse($user->internProfile->end_date)->format('d M Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        @if($user->internProfile && $user->internProfile->mentor)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400">
                                                {{ $user->internProfile->mentor->name }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400">
                                                Belum Ditentukan
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        @if($user->is_first_login)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400">YA</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">TIDAK</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button @click="openEditInternModal({{ json_encode($user) }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">Edit</button>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Belum ada data anak magang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mentors Table Panel -->
            <div x-show="activeTab === 'mentors'" class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Mentor</h3>
                    <button @click="openCreateUser = true; editUserData.role = 'mentor'" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold uppercase tracking-widest rounded-lg shadow-sm transition-colors duration-150">
                        Tambah Mentor
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-750">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama / Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah Anak Magang</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users->where('role', 'mentor') as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Username: {{ $user->username }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-indigo-800 bg-indigo-100 rounded-full dark:bg-indigo-900/30 dark:text-indigo-400">
                                            {{ $user->internProfilesForMentor->count() }} Interns
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        <button @click="openEditUserModal({{ json_encode($user) }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">Edit</button>
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus mentor ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Belum ada data mentor.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Admins Table Panel -->
            <div x-show="activeTab === 'admins'" class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Administrator</h3>
                    <button @click="openCreateUser = true; editUserData.role = 'admin'" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold uppercase tracking-widest rounded-lg shadow-sm transition-colors duration-150">
                        Tambah Admin
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-750">
                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama / Username</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($users->where('role', 'admin') as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Username: {{ $user->username }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                                        @if($user->id !== auth()->id())
                                            <button @click="openEditUserModal({{ json_encode($user) }})" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900">Edit</button>
                                            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus admin ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-900">Delete</button>
                                            </form>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Akun Anda</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">Belum ada data admin.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
