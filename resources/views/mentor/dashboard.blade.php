<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Mentor Dashboard - Bimbingan Magang') }}
            </h2>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Lihat Rekap Laporan
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="{
        openCreateTask: false,
        openEditTask: false,
        openGrade: false,
        editTaskData: {},
        gradeData: {},
        openEditTaskModal(task) {
            this.editTaskData = {
                id: task.id,
                title: task.title,
                description: task.description,
                due_date: task.due_date.split(' ')[0] + 'T' + task.due_date.split(' ')[1].substring(0,5)
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
                feedback: sub.feedback || ''
            };
            this.openGrade = true;
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Success Alert -->
            @if (session('success'))
                <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400 border border-green-200 dark:border-green-800" role="alert">
                    <span class="font-medium">Sukses!</span> {{ session('success') }}
                </div>
            @endif

            <!-- Warning Alert -->
            @if ($errors->any())
                <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200 dark:border-red-800" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Main Layout Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Left Column: Mapped Interns -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 p-6 space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white border-b pb-2 border-gray-100 dark:border-gray-700">
                        Anak Magang Anda ({{ $interns->count() }})
                    </h3>
                    <div class="space-y-3">
                        @forelse($interns as $intern)
                            <div class="flex items-center space-x-3 p-3 bg-gray-50 dark:bg-gray-900/50 rounded-lg border border-gray-100 dark:border-gray-750">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">
                                        {{ $intern->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ $intern->internProfile->university }} ({{ $intern->internProfile->major }})
                                    </p>
                                    <p class="text-[10px] text-indigo-600 dark:text-indigo-400 font-medium mt-1">
                                        NIM: {{ $intern->username }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada anak magang yang dipetakan kepada Anda.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Right 2 Columns: Tasks and Submissions -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- Tasks List Panel -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Kelola Tugas</h3>
                            @if($interns->isNotEmpty())
                                <button @click="openCreateTask = true" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold uppercase tracking-widest rounded-lg shadow-sm transition-colors duration-150">
                                    Berikan Tugas Baru
                                </button>
                            @endif
                        </div>
                        <div class="p-6 space-y-4">
                            @forelse($tasks as $task)
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-750 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center space-x-2">
                                            <h4 class="text-base font-bold text-gray-900 dark:text-white">{{ $task->title }}</h4>
                                            @if($task->status === 'graded')
                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">DINILAI</span>
                                            @elseif($task->status === 'submitted')
                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">DIKUMPULKAN</span>
                                            @else
                                                <span class="px-2 py-0.5 text-[10px] font-semibold rounded bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400">PENDING</span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-650 dark:text-gray-400">{{ Str::limit($task->description, 100) }}</p>
                                        <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                                            <span class="font-medium">Penerima: <span class="text-indigo-600 dark:text-indigo-400">{{ $task->intern->name }}</span></span>
                                            <span>Deadline: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y H:i') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 self-end md:self-center">
                                        <button @click="openEditTaskModal({{ json_encode($task) }})" class="px-3 py-1.5 border border-gray-300 dark:border-gray-750 text-gray-700 dark:text-gray-300 rounded-lg text-xs font-semibold hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                            Edit
                                        </button>
                                        <form action="{{ route('mentor.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-1.5 bg-red-50 dark:bg-red-950/20 text-red-600 dark:text-red-400 rounded-lg text-xs font-semibold hover:bg-red-100 dark:hover:bg-red-900/30 transition">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada tugas yang diberikan.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Submissions List Panel -->
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pengumpulan Tugas Interns</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            @forelse($submissions as $sub)
                                <div class="p-4 bg-gray-50 dark:bg-gray-900/40 rounded-xl border border-gray-100 dark:border-gray-750 flex flex-col md:flex-row md:items-center justify-between gap-4">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-950 dark:text-white">Tugas: {{ $sub->task->title }}</h4>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">Oleh: <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $sub->intern->name }}</span></p>
                                        <div class="flex flex-col mt-2 space-y-1">
                                            @if($sub->file_path)
                                                <a href="{{ asset('storage/' . $sub->file_path) }}" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                    Lihat File Lampiran
                                                </a>
                                            @endif
                                            @if($sub->link_url)
                                                <a href="{{ $sub->link_url }}" target="_blank" class="text-xs text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center">
                                                    <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                    Link URL: {{ $sub->link_url }}
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-2">
                                        @if($sub->status === 'graded')
                                            <div class="text-right">
                                                <span class="text-2xl font-black text-green-600 dark:text-green-400">{{ $sub->score }}</span><span class="text-xs text-gray-500 dark:text-gray-400">/100</span>
                                                <p class="text-xs text-gray-400 mt-1 italic">"{{ Str::limit($sub->feedback, 30) }}"</p>
                                            </div>
                                            <button @click="openGradeModal({{ json_encode($sub) }})" class="text-xs text-indigo-650 dark:text-indigo-400 hover:underline">Re-Grade</button>
                                        @else
                                            <button @click="openGradeModal({{ json_encode($sub) }})" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition">
                                                Nilai Sekarang
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada tugas yang dikumpulkan.</p>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>

            <!-- MODAL: Create Task -->
            <div x-show="openCreateTask" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
                <div @click.away="openCreateTask = false" class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl max-w-md w-full border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Berikan Tugas Baru</h3>
                        <button @click="openCreateTask = false" class="text-gray-400 hover:text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form action="{{ route('mentor.tasks.store') }}" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <x-input-label for="intern_id" :value="__('Anak Magang Penerima')" />
                            <select name="intern_id" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-350 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required>
                                <option value="">-- Pilih Anak Magang --</option>
                                @foreach($interns as $intern)
                                    <option value="{{ $intern->id }}">{{ $intern->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="title" :value="__('Judul Tugas')" />
                            <x-text-input type="text" name="title" class="block mt-1 w-full" required />
                        </div>
                        <div>
                            <x-input-label for="description" :value="__('Instruksi / Deskripsi')" />
                            <textarea name="description" rows="4" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required></textarea>
                        </div>
                        <div>
                            <x-input-label for="due_date" :value="__('Batas Waktu (Deadline)')" />
                            <x-text-input type="datetime-local" name="due_date" class="block mt-1 w-full" required />
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" @click="openCreateTask = false" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">Batal</button>
                            <x-primary-button>Berikan Tugas</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL: Edit Task -->
            <div x-show="openEditTask" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
                <div @click.away="openEditTask = false" class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl max-w-md w-full border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Edit Rincian Tugas</h3>
                        <button @click="openEditTask = false" class="text-gray-400 hover:text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form :action="'/mentor/tasks/' + editTaskData.id" method="POST" class="p-6 space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <x-input-label for="title" :value="__('Judul Tugas')" />
                            <x-text-input type="text" name="title" x-model="editTaskData.title" class="block mt-1 w-full" required />
                        </div>
                        <div>
                            <x-input-label for="description" :value="__('Instruksi / Deskripsi')" />
                            <textarea name="description" rows="4" x-model="editTaskData.description" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" required></textarea>
                        </div>
                        <div>
                            <x-input-label for="due_date" :value="__('Batas Waktu (Deadline)')" />
                            <x-text-input type="datetime-local" name="due_date" x-model="editTaskData.due_date" class="block mt-1 w-full" required />
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" @click="openEditTask = false" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">Batal</button>
                            <x-primary-button>Simpan Perubahan</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- MODAL: Grade Submission -->
            <div x-show="openGrade" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm" style="display: none;">
                <div @click.away="openGrade = false" class="bg-white dark:bg-gray-800 rounded-xl overflow-hidden shadow-xl max-w-md w-full border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Penilaian Tugas</h3>
                            <p class="text-xs text-gray-500" x-text="'Intern: ' + gradeData.intern_name"></p>
                        </div>
                        <button @click="openGrade = false" class="text-gray-400 hover:text-gray-500"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
                    </div>
                    <form :action="'/mentor/submissions/' + gradeData.id + '/grade'" method="POST" class="p-6 space-y-4">
                        @csrf
                        <div>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">Lampiran Intern:</p>
                            <div class="mt-2 space-y-1">
                                <template x-if="gradeData.file_path">
                                    <a :href="'/storage/' + gradeData.file_path" target="_blank" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline block">
                                        Unduh/Lihat File Lampiran
                                    </a>
                                </template>
                                <template x-if="gradeData.link_url">
                                    <a :href="gradeData.link_url" target="_blank" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline block">
                                        Buka Tautan Link: <span x-text="gradeData.link_url"></span>
                                    </a>
                                </template>
                            </div>
                        </div>
                        <div>
                            <x-input-label for="score" :value="__('Nilai (0 - 100)')" />
                            <x-text-input type="number" name="score" min="0" max="100" x-model="gradeData.score" class="block mt-1 w-full" required />
                        </div>
                        <div>
                            <x-input-label for="feedback" :value="__('Feedback / Ulasan')" />
                            <textarea name="feedback" rows="4" x-model="gradeData.feedback" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm" placeholder="Berikan feedback atau instruksi revisi..."></textarea>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                            <button type="button" @click="openGrade = false" class="px-4 py-2 border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 rounded-lg text-sm">Batal</button>
                            <x-primary-button>Simpan Nilai</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
