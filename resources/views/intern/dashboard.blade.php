<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Intern Workspace') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="{
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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Warning banner for first login -->
            @if (auth()->user()->is_first_login)
                <div class="p-4 text-sm text-amber-800 rounded-lg bg-amber-50 dark:bg-gray-800 dark:text-amber-400 border border-amber-200 dark:border-amber-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3" role="alert">
                    <div>
                        <span class="font-bold">⚠️ Perhatian:</span> Akun Anda masih menggunakan password default. Silakan ubah password Anda di halaman profil demi keamanan.
                    </div>
                    <div>
                        <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white rounded text-xs font-bold transition">
                            Ubah Password Sekarang
                        </a>
                    </div>
                </div>
            @endif

            <!-- Success toast -->
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200 dark:border-green-800" role="alert">
                    <span class="font-medium">Sukses!</span> {{ session('success') }}
                </div>
            @endif

            <!-- Error messages -->
            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200 dark:border-red-800" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Layout: Profile and Statistics -->
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

                <!-- Profile Info Card -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <div class="text-center pb-4 border-b border-gray-150 dark:border-gray-700">
                        <div class="w-20 h-20 bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 rounded-full flex items-center justify-center mx-auto text-3xl font-extrabold shadow-inner mb-3">
                            {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                        </div>
                        <h3 class="font-bold text-gray-900 dark:text-white text-lg">{{ auth()->user()->name }}</h3>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-semibold uppercase mt-0.5">NIM: {{ auth()->user()->username }}</p>
                    </div>

                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block text-xs">Universitas</span>
                            <span class="font-semibold text-gray-850 dark:text-gray-200">{{ $profile->university ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block text-xs">Jurusan</span>
                            <span class="font-semibold text-gray-850 dark:text-gray-200">{{ $profile->major ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-500 dark:text-gray-400 block text-xs">Periode Magang</span>
                            <span class="font-semibold text-gray-850 dark:text-gray-200 text-xs">
                                @if($profile)
                                    {{ \Carbon\Carbon::parse($profile->start_date)->format('d M Y') }} - 
                                    {{ \Carbon\Carbon::parse($profile->end_date)->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="pt-3 border-t border-gray-150 dark:border-gray-700">
                            <span class="text-gray-500 dark:text-gray-400 block text-xs">Mentor Bimbingan</span>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400 mt-1">
                                {{ $mentor->name ?? 'Belum Ditugaskan' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Stats Grid (Right 3 Columns) -->
                <div class="lg:col-span-3 space-y-6">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <!-- Stat Average -->
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-5 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Rata-Rata Nilai</span>
                            <div class="mt-2 flex items-baseline">
                                <span class="text-3xl font-black text-indigo-600 dark:text-indigo-400">{{ $averageScore }}</span>
                                <span class="text-xs text-gray-450 dark:text-gray-550 ml-1">/100</span>
                            </div>
                        </div>

                        <!-- Stat Total Tasks -->
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-5 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Total Tugas</span>
                            <span class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $tasksCount }}</span>
                        </div>

                        <!-- Stat Graded Tasks -->
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-5 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Tugas Dinilai</span>
                            <span class="text-3xl font-bold text-green-600 dark:text-green-400 mt-2">{{ $gradedCount }}</span>
                        </div>

                        <!-- Stat Pending Tasks -->
                        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-5 border border-gray-100 dark:border-gray-700 flex flex-col justify-between">
                            <span class="text-xs font-medium text-gray-500 dark:text-gray-400">Belum Selesai</span>
                            <span class="text-3xl font-bold text-amber-600 dark:text-amber-400 mt-2">{{ $pendingCount }}</span>
                        </div>
                    </div>

                    <!-- Tasks List Workspace -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Daftar Tugas & Status</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @forelse($tasks as $task)
                                <div class="p-5 bg-gray-50 dark:bg-gray-900/30 rounded-xl border border-gray-100 dark:border-gray-750 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="space-y-1.5 flex-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-base font-bold text-gray-950 dark:text-white">{{ $task->title }}</h4>
                                            @if($task->status === 'graded')
                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">NILAI: {{ $task->latestSubmission->score }}/100</span>
                                            @elseif($task->status === 'submitted')
                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">MENUNGGU PENILAIAN</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">PENDING</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-700 dark:text-gray-400">{{ $task->description }}</p>
                                        <div class="flex items-center text-xs text-gray-500 dark:text-gray-400 pt-1">
                                            <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Deadline: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y H:i') }}
                                        </div>

                                        <!-- Grading Feedback if Graded -->
                                        @if($task->status === 'graded' && $task->latestSubmission->feedback)
                                            <div class="mt-3 p-3 bg-green-50 dark:bg-green-950/20 rounded-lg border border-green-200 dark:border-green-800 text-xs text-green-800 dark:text-green-300">
                                                <span class="font-bold">Feedback Mentor:</span> "{{ $task->latestSubmission->feedback }}"
                                            </div>
                                        @endif
                                    </div>

                                    <div class="self-end md:self-center">
                                        @if($task->status === 'graded')
                                            <span class="px-4 py-2 bg-gray-100 dark:bg-gray-750 text-gray-500 dark:text-gray-450 rounded-lg text-xs font-semibold">Tugas Selesai</span>
                                        @elseif($task->status === 'submitted')
                                            <button @click="openSubmitModal({{ json_encode($task) }})" class="px-4 py-2 border border-indigo-500 text-indigo-600 dark:text-indigo-400 rounded-lg text-xs font-semibold hover:bg-indigo-50 dark:hover:bg-indigo-900/35 transition">
                                                Kumpulkan Ulang
                                            </button>
                                        @else
                                            <button @click="openSubmitModal({{ json_encode($task) }})" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition">
                                                Kumpulkan Tugas
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-6">Belum ada tugas yang diberikan oleh mentor.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>

            <!-- MODAL: Submit Task -->
            <div x-show="openSubmit" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
                <div @click.away="openSubmit = false" class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl max-w-md w-full border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Pengumpulan Tugas</h3>
                            <p class="text-xs text-gray-500" x-text="submitData.title"></p>
                        </div>
                        <button @click="openSubmit = false" class="text-gray-400 hover:text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form :action="'/intern/tasks/' + submitData.id + '/submit'" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="file" :value="__('Unggah File Lampiran (pdf, zip, docx, png, dll. maks 10MB)')" />
                            <input type="file" name="file" class="block mt-1 w-full border border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 rounded-md shadow-sm text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500">
                        </div>
                        <div class="relative flex py-2 items-center">
                            <div class="flex-grow border-t border-gray-350 dark:border-gray-700"></div>
                            <span class="flex-shrink mx-4 text-xs text-gray-500 dark:text-gray-400 font-bold uppercase">ATAU</span>
                            <div class="flex-grow border-t border-gray-350 dark:border-gray-700"></div>
                        </div>
                        <div>
                            <x-input-label for="link_url" :value="__('Tautan Link URL (GitHub, Drive, dll.)')" />
                            <x-text-input type="url" name="link_url" x-model="submitData.link_url" class="block mt-1 w-full" placeholder="https://github.com/..." />
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" @click="openSubmit = false" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">Batal</button>
                            <x-primary-button>Kirim Tugas</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
