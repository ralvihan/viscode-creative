<x-app-layout x-data="{ 
    activeTab: '{{ request('tab', 'projects') }}',
    projectStageFilter: 'all',
    openCreateProject: false, 
    openProjectDetail: false, 
    openAddTask: false,
    openSubmitTask: false,
    openSubmitQc: false,
    openQcReviewModal: false,
    openDeliveryModal: false,
    openOverride: false,
    selectedProject: { tasks: [] }, 
    selectedTask: {},
    overrideRecordId: null 
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-8 bg-blue-600 rounded-full inline-block"></span>
                    Operations — Proyek, Tugas Tim & Quality Control
                </h2>
                <p class="text-xs text-slate-500 mt-1">Eksekusi produksi terpusat: pengelolaan proyek, pengerjaan tugas anggota, dan verifikasi standar kualitas (QC)</p>
            </div>
            @if(count($eligibleOrders) > 0)
            <button type="button" @click="openCreateProject = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-semibold rounded-xl shadow-sm transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Inisialisasi Proyek ({{ count($eligibleOrders) }} Siap)</span>
            </button>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">

        <!-- Segmented Workspace Navigation -->
        <x-viscode-workspace-tabs workspace="operations" />

        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-900 cursor-pointer">&times;</button>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                <ul class="list-disc list-inside space-y-1 text-xs">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Internal Tab Switcher (In-Page Instant Switching) -->
        <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="flex items-center gap-1.5 overflow-x-auto">
                    <button type="button" @click="activeTab = 'projects'"
                            :class="activeTab === 'projects' ? 'bg-slate-900 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>Projects</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'projects' ? 'bg-slate-700 text-white' : 'bg-slate-100 text-slate-600'">{{ $projects->total() }}</span>
                    </button>

                    <button type="button" @click="activeTab = 'my_tasks'"
                            :class="activeTab === 'my_tasks' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>My Tasks</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'my_tasks' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700'">{{ $myTasks->whereNotIn('status', ['DONE'])->count() }}</span>
                    </button>

                    <button type="button" @click="activeTab = 'qc_review'"
                            :class="activeTab === 'qc_review' ? 'bg-indigo-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 whitespace-nowrap cursor-pointer">
                        <span>Review & QC</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px]" :class="activeTab === 'qc_review' ? 'bg-indigo-500 text-white' : 'bg-indigo-50 text-indigo-700'">{{ $qcProjects->count() + $qcTasks->count() }}</span>
                    </button>
                </div>

                <div class="text-[11px] text-slate-500">
                    Satu tindakan utama ditampilkan sesuai status pengerjaan
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- TAB 1: PROJECTS                                       -->
            <!-- ===================================================== -->
            @php
                $prjCol = $projects->getCollection();
                $prjNewCount = $prjCol->where('status', 'NOT_STARTED')->count();
                $prjActiveCount = $prjCol->whereIn('status', ['IN_PROGRESS', 'REVISION'])->count();
                $prjReviewCount = $prjCol->where('status', 'REVIEW')->count();
                $prjDoneCount = $prjCol->where('status', 'COMPLETED')->count();
            @endphp
            <div x-show="activeTab === 'projects'">
                <div class="px-4 py-3 border-b border-slate-100 bg-white flex items-center gap-1.5 overflow-x-auto">
                    <button type="button" @click="projectStageFilter = 'all'"
                            :class="projectStageFilter === 'all' ? 'bg-slate-800 text-white shadow-2xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <span>Semua Proyek</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="projectStageFilter === 'all' ? 'bg-slate-600 text-white' : 'bg-slate-200 text-slate-700'">{{ $projects->total() }}</span>
                    </button>

                    <button type="button" @click="projectStageFilter = 'new'"
                            :class="projectStageFilter === 'new' ? 'bg-blue-600 text-white shadow-2xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <span>1. Baru / Belum Dikerjakan (NEW)</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="projectStageFilter === 'new' ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700'">{{ $prjNewCount }}</span>
                    </button>

                    <button type="button" @click="projectStageFilter = 'in_progress'"
                            :class="projectStageFilter === 'in_progress' ? 'bg-amber-600 text-white shadow-2xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <span>2. Sedang Berjalan (IN PROGRESS)</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="projectStageFilter === 'in_progress' ? 'bg-amber-500 text-white' : 'bg-amber-50 text-amber-700'">{{ $prjActiveCount }}</span>
                    </button>

                    <button type="button" @click="projectStageFilter = 'review'"
                            :class="projectStageFilter === 'review' ? 'bg-indigo-600 text-white shadow-2xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <span>3. Menunggu QC (REVIEW)</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="projectStageFilter === 'review' ? 'bg-indigo-500 text-white' : 'bg-indigo-50 text-indigo-700'">{{ $prjReviewCount }}</span>
                    </button>

                    <button type="button" @click="projectStageFilter = 'completed'"
                            :class="projectStageFilter === 'completed' ? 'bg-emerald-600 text-white shadow-2xs' : 'bg-slate-50 text-slate-600 hover:bg-slate-100 border border-slate-200'"
                            class="px-3 py-1.5 rounded-xl text-[11px] font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <span>4. Selesai (COMPLETED)</span>
                        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono" :class="projectStageFilter === 'completed' ? 'bg-emerald-500 text-white' : 'bg-emerald-50 text-emerald-700'">{{ $prjDoneCount }}</span>
                    </button>
                </div>

                <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase font-bold tracking-wider text-slate-500 bg-slate-50/70">
                            <th class="py-3.5 px-4">Kode Proyek</th>
                            <th class="py-3.5 px-4">Judul & Klien</th>
                            <th class="py-3.5 px-4">PM & Target</th>
                            <th class="py-3.5 px-4">Status Proyek</th>
                            <th class="py-3.5 px-4 text-center">Progress Tugas</th>
                            <th class="py-3.5 px-4 text-right">Tindakan Utama (Next Action)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($projects as $prj)
                        @php
                            $doneTasks = $prj->tasks->where('status', 'DONE')->count();
                            $totalTasks = $prj->tasks->count();
                            $allTasksDone = $totalTasks > 0 && $doneTasks === $totalTasks;
                            $prjFilterGroup = match($prj->status) {
                                'NOT_STARTED' => 'new',
                                'IN_PROGRESS', 'REVISION' => 'in_progress',
                                'REVIEW' => 'review',
                                'COMPLETED' => 'completed',
                                default => 'other',
                            };
                        @endphp
                        <tr x-show="projectStageFilter === 'all' || projectStageFilter === '{{ $prjFilterGroup }}'" class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4 font-mono font-bold text-blue-600">
                                {{ $prj->project_code }}
                                <div class="text-[10px] text-slate-400 font-normal">Order: {{ $prj->order->order_code }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $prj->title }}</div>
                                <div class="text-[11px] text-slate-500">{{ $prj->order->customer->name }} &bull; {{ $prj->order->service->name }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-800">{{ $prj->projectManager->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">Target: {{ $prj->target_completion_date ? $prj->target_completion_date->format('d M Y') : '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                $statusClasses = match($prj->status) {
                                    'NOT_STARTED' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'IN_PROGRESS' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'REVIEW' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'REVISION' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'COMPLETED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'CANCELLED' => 'bg-rose-50 text-rose-700 border-rose-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                @endphp
                                <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-full border {{ $statusClasses }}">
                                    {{ str_replace('_', ' ', $prj->status) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <span class="font-mono text-xs font-bold {{ $allTasksDone ? 'text-emerald-600' : 'text-slate-700' }}">
                                        {{ $doneTasks }} / {{ $totalTasks }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">({{ $prj->progress_percentage }}%)</span>
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center justify-end gap-2">
                                    {{-- CONTEXTUAL ACTION SYSTEM: ONE PRIMARY ACTION PER PROJECT STATUS --}}
                                    @if($prj->status === 'NOT_STARTED')
                                        <form action="{{ route('projects.start', $prj) }}" method="POST" class="inline"
                                              onsubmit="return window.confirmFormSubmit(event, {
                                                  title: 'Mulai Pengerjaan Proyek?',
                                                  html: 'Mulai pengerjaan proyek <b>{{ addslashes($prj->title) }}</b>?<br><br>Status proyek akan beralih ke <span class=\'text-blue-600 font-bold\'>IN_PROGRESS</span>.',
                                                  icon: 'info',
                                                  confirmButtonText: '▶ Mulai Proyek',
                                                  confirmButtonColor: '#2563EB'
                                              })">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-500 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                ▶ Mulai Proyek
                                            </button>
                                        </form>

                                    @elseif(in_array($prj->status, ['IN_PROGRESS', 'REVISION']))
                                        @if($allTasksDone)
                                            <button type="button" @click="selectedProject = {{ Js::from($prj) }}; openSubmitQc = true"
                                                    class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                🔍 Ajukan ke QC
                                            </button>
                                        @else
                                            <button type="button" @click="selectedProject = {{ Js::from($prj) }}; openProjectDetail = true"
                                                    class="px-3.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 text-xs font-bold rounded-xl transition cursor-pointer">
                                                📋 Lihat Progress Proyek
                                            </button>
                                        @endif

                                    @elseif($prj->status === 'REVIEW')
                                        @if(Auth::user()->isQc() || Auth::user()->isCeo() || Auth::user()->isSuperAdmin())
                                            <button type="button" @click="selectedProject = {{ Js::from($prj) }}; openQcReviewModal = true"
                                                    class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs transition cursor-pointer">
                                                🔍 Tinjau Hasil Kerja (QC)
                                            </button>
                                        @else
                                            <span class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-[11px] font-semibold">
                                                Sedang Ditinjau QC
                                            </span>
                                        @endif

                                    @elseif($prj->status === 'COMPLETED')
                                        <button type="button" @click="selectedProject = {{ Js::from($prj) }}; openDeliveryModal = true"
                                                class="px-3.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 text-xs font-bold rounded-xl transition cursor-pointer">
                                            📦 Lihat Detail Penyerahan
                                        </button>
                                    @endif

                                    <!-- Secondary Detail & Tasks Trigger -->
                                    <button type="button" @click="selectedProject = {{ Js::from($prj) }}; openProjectDetail = true"
                                            class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg border border-transparent hover:border-slate-200 transition cursor-pointer" title="Rincian Tugas & Berkas Proyek">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 text-xs">Belum ada proyek pengerjaan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                @if($projects->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">{{ $projects->links() }}</div>
                @endif
                </div>
            </div>

            <!-- ===================================================== -->
            <!-- TAB 2: MY TASKS (Focused Member Task Execution)       -->
            <!-- ===================================================== -->
            <div x-show="activeTab === 'my_tasks'" x-cloak class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-100 text-[11px] uppercase font-bold tracking-wider text-slate-500 bg-slate-50/70">
                            <th class="py-3.5 px-4">Tugas & Deskripsi</th>
                            <th class="py-3.5 px-4">Proyek & Klien</th>
                            <th class="py-3.5 px-4">PIC & Deadline</th>
                            <th class="py-3.5 px-4">Status Tugas</th>
                            <th class="py-3.5 px-4 text-right">Tindakan Utama (Next Action)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($myTasks as $tk)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-slate-900 text-sm">{{ $tk->title }}</div>
                                @if($tk->description)
                                    <div class="text-[11px] text-slate-500 mt-0.5">{{ $tk->description }}</div>
                                @endif
                                @if($tk->revision_notes && $tk->status === 'REVISION')
                                    <div class="text-[11px] text-amber-700 bg-amber-50 px-2 py-1 rounded mt-1 border border-amber-200">
                                        <strong>Catatan Revisi:</strong> {{ $tk->revision_notes }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-mono font-bold text-blue-600">{{ $tk->project->project_code ?? '-' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $tk->project->order->customer->name ?? '-' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-medium text-slate-800">{{ $tk->assignee->name ?? '-' }}</div>
                                <div class="text-[10px] text-slate-400 font-mono">Deadline: {{ $tk->deadline ? $tk->deadline->format('d M Y') : 'Fleksibel' }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                $tkBadge = match($tk->status) {
                                    'TODO' => 'bg-slate-100 text-slate-700 border-slate-200',
                                    'IN_PROGRESS' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'IN_REVIEW' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                    'REVISION' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'DONE' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    default => 'bg-slate-100 text-slate-700 border-slate-200',
                                };
                                @endphp
                                <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-full border {{ $tkBadge }}">
                                    {{ $tk->status }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                {{-- ONE PRIMARY ACTION FOR TASK --}}
                                @if($tk->status === 'TODO')
                                    <form action="{{ route('tasks.start', $tk) }}" method="POST" class="inline"
                                          onsubmit="return window.confirmFormSubmit(event, {
                                              title: 'Mulai Tugas Ini?',
                                              text: 'Status tugas akan beralih ke IN_PROGRESS.',
                                              icon: 'info',
                                              confirmButtonText: '▶ Mulai Tugas',
                                              confirmButtonColor: '#2563EB'
                                          })">
                                        @csrf
                                        <button type="submit" class="px-3.5 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer">
                                            ▶ Mulai Kerjakan
                                        </button>
                                    </form>
                                @elseif(in_array($tk->status, ['IN_PROGRESS', 'REVISION']))
                                    <button type="button" @click="selectedTask = {{ Js::from($tk) }}; openSubmitTask = true"
                                            class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer">
                                        📤 Serahkan Hasil
                                    </button>
                                @elseif($tk->status === 'IN_REVIEW')
                                    @if(Auth::user()->isQc() || Auth::user()->isCeo() || Auth::user()->isSuperAdmin())
                                        <form action="{{ route('tasks.approve', $tk) }}" method="POST" class="inline"
                                              onsubmit="return window.confirmFormSubmit(event, {
                                                  title: 'Setujui Hasil Tugas?',
                                                  text: 'Tugas akan ditandai DONE dan progres proyek otomatis diperbarui.',
                                                  icon: 'success',
                                                  confirmButtonText: '✓ Setujui Tugas',
                                                  confirmButtonColor: '#16A34A'
                                              })">
                                            @csrf
                                            <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer">
                                                ✓ Setujui Tugas
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-[11px] text-indigo-600 font-semibold">Menunggu Review</span>
                                    @endif
                                @else
                                    <span class="text-[11px] text-emerald-600 font-semibold">✓ Selesai</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400 text-xs">Tidak ada tugas pengerjaan yang ditemukan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- ===================================================== -->
            <!-- TAB 3: REVIEW & QC (Dedicated Quality Control Desk)   -->
            <!-- ===================================================== -->
            <div x-show="activeTab === 'qc_review'" x-cloak class="p-6 space-y-6">
                <div>
                    <h4 class="font-bold text-slate-900 text-sm mb-3">Proyek Menunggu Pemeriksaan Quality Control ({{ $qcProjects->count() }})</h4>
                    @if($qcProjects->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($qcProjects as $qp)
                            <div class="p-4 rounded-2xl border border-indigo-200 bg-indigo-50/40 flex flex-col justify-between gap-3">
                                <div>
                                    <div class="flex items-center justify-between">
                                        <span class="font-mono font-bold text-indigo-700 text-xs">{{ $qp->project_code }}</span>
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase bg-indigo-100 text-indigo-800">PENDING QC</span>
                                    </div>
                                    <h5 class="font-bold text-slate-900 text-sm mt-1.5">{{ $qp->title }}</h5>
                                    <p class="text-xs text-slate-600 mt-0.5">Klien: {{ $qp->order->customer->name }} &bull; PM: {{ $qp->projectManager->name ?? '-' }}</p>
                                    @if($qp->deliverable_url)
                                        <div class="mt-2">
                                            <a href="{{ $qp->deliverable_url }}" target="_blank" class="text-xs text-blue-600 hover:underline font-mono inline-flex items-center gap-1">
                                                🔗 Buka File Deliverable ↗
                                            </a>
                                        </div>
                                    @endif
                                </div>
                                <div class="pt-3 border-t border-indigo-200/60 flex justify-end">
                                    <button type="button" @click="selectedProject = {{ Js::from($qp) }}; openQcReviewModal = true"
                                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-xs cursor-pointer">
                                        🔍 Tinjau & Putuskan QC
                                    </button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="p-8 rounded-xl bg-slate-50 border border-slate-200 text-center text-xs text-slate-500">
                            Tidak ada proyek yang sedang menunggu verifikasi QC saat ini.
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

    <!-- ================================================================= -->
    <!-- MODAL 1: INISIALISASI PROYEK BARU DARI ORDER                      -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openCreateProject" title="Inisialisasi Proyek Baru dari Pesanan" maxWidth="md">
        <form action="{{ route('projects.store') }}" method="POST"
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Inisialisasi Proyek Baru?',
                  text: 'Proyek akan dibuat lengkap dengan tugas milestone standar sesuai template.',
                  icon: 'info',
                  confirmButtonText: 'Mulai Proyek',
                  confirmButtonColor: '#2563EB'
              })"
              class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Pilih Pesanan Terkonfirmasi</label>
                <select name="order_id" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Pilih Pesanan --</option>
                    @foreach($eligibleOrders as $ord)
                        <option value="{{ $ord->id }}">
                            {{ $ord->order_code }} - {{ $ord->customer->name }} ({{ $ord->service->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Judul / Nama Proyek</label>
                <input type="text" name="title" required placeholder="Pembuatan Website Landing Page Klien..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Project Manager</label>
                <select name="project_manager_id" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($team as $m)
                        <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->role_label }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="start_date" required value="{{ date('Y-m-d') }}" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Target Selesai</label>
                    <input type="date" name="target_completion_date" value="{{ date('Y-m-d', strtotime('+14 days')) }}" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div class="p-3.5 bg-blue-50/70 border border-blue-200/80 rounded-xl">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="apply_template" value="1" checked class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-xs font-bold text-slate-800">Otomatis Terapkan Template Tugas Teknis</span>
                </label>
                <div class="mt-2">
                    <select name="template_key" class="w-full rounded-lg bg-white border-slate-300 text-slate-900 text-xs">
                        <option value="web_development">Template Pengembangan Website (5 Tahapan Standar)</option>
                        <option value="graphic_design">Template Desain Grafis & Konten (4 Tahapan Standar)</option>
                        <option value="bundling">Template Paket Bundling Digital (4 Tahapan)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreateProject = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Mulai Proyek</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 2: AJUKAN HASIL PROYEK KE QC                                -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openSubmitQc" title="Ajukan Hasil Proyek ke Quality Control (QC)" maxWidth="md">
        <form :action="`/projects/${selectedProject.id}/submit-qc`" method="POST"
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Ajukan Proyek ke QC?',
                  text: 'Status proyek akan beralih ke REVIEW untuk diperiksa oleh tim Quality Control.',
                  icon: 'info',
                  confirmButtonText: 'Ajukan ke QC',
                  confirmButtonColor: '#4F46E5'
              })"
              class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-800 text-xs leading-relaxed">
                Ajukan deliverable akhir proyek <strong x-text="selectedProject.project_code"></strong> (<span x-text="selectedProject.title"></span>) ke Quality Control.
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tautan Deliverable Hasil Akhir (Figma / GitHub / GDrive)</label>
                <input type="url" name="deliverable_url" required :value="selectedProject.deliverable_url" placeholder="https://..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Pengerjaan untuk Tim QC (Opsional)</label>
                <textarea name="notes" rows="3" placeholder="Sertakan penjelasan deliverable..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openSubmitQc = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Ajukan ke QC</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 3: CONTEXTUAL QC REVIEW DIALOG (APPROVE OR REVISION)        -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openQcReviewModal" title="Pemeriksaan Quality Control (QC Review)" maxWidth="md">
        <div class="space-y-4 text-xs" x-data="{ showRevForm: false }">
            <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-900 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-mono font-bold text-indigo-700" x-text="selectedProject.project_code"></span>
                    <span class="px-2 py-0.5 rounded bg-indigo-100 text-indigo-800 font-bold text-[10px]">PENDING QC</span>
                </div>
                <div class="font-bold text-slate-900 text-sm" x-text="selectedProject.title"></div>
                <div>Klien: <strong x-text="selectedProject.order?.customer?.name"></strong></div>
            </div>

            <div x-show="selectedProject.deliverable_url" class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                <span class="font-semibold text-slate-700">Berkas Deliverable:</span>
                <a :href="selectedProject.deliverable_url" target="_blank" class="px-3 py-1 bg-white border border-slate-300 rounded-lg text-blue-600 font-bold hover:bg-blue-50 transition">
                    🔗 Buka Deliverable ↗
                </a>
            </div>

            <div x-show="!showRevForm" class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                <form :action="`/projects/${selectedProject.id}/approve-qc`" method="POST"
                      onsubmit="return window.confirmFormSubmit(event, {
                          title: 'Setujui & Loloskan QC?',
                          html: 'Anda menyetujui hasil proyek <b>' + (selectedProject.title || '') + '</b>?<br><br>Status proyek akan menjadi <span class=\'text-emerald-600 font-bold\'>COMPLETED</span>.',
                          icon: 'success',
                          confirmButtonText: '✓ Lolos QC (Approve)',
                          confirmButtonColor: '#16A34A'
                      })">
                    @csrf
                    <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition cursor-pointer">
                        ✓ Setujui & Lolos QC
                    </button>
                </form>

                <button type="button" @click="showRevForm = true"
                        class="w-full py-3 px-4 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 font-bold rounded-xl transition cursor-pointer">
                    ⚠ Minta Revisi
                </button>
            </div>

            <form x-show="showRevForm" :action="`/projects/${selectedProject.id}/revision`" method="POST"
                  onsubmit="return window.confirmFormSubmit(event, {
                      title: 'Kembalikan Proyek untuk Revisi?',
                      text: 'Kembalikan proyek ini ke anggota tim yang ditugaskan untuk perbaikan sesuai catatan QC?',
                      icon: 'warning',
                      confirmButtonText: 'Kirim Revisi',
                      confirmButtonColor: '#D97706'
                  })"
                  class="space-y-3 pt-2 border-t border-slate-100">
                @csrf
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">Catatan Revisi QC (Wajib) <span class="text-rose-500">*</span></label>
                    <textarea name="qc_notes" rows="3" required placeholder="Jelaskan poin perbaikan yang wajib diselesaikan tim..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-xs"></textarea>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="showRevForm = false" class="px-3 py-1.5 bg-white border border-slate-200 rounded-lg text-slate-600 font-semibold cursor-pointer">Kembali</button>
                    <button type="submit" class="px-4 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg cursor-pointer">Kirim Catatan Revisi</button>
                </div>
            </form>
        </div>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 4: COMPLETED PROJECT DELIVERY DETAILS                       -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openDeliveryModal" title="Detail Penyelesaian & Serah Terima Proyek" maxWidth="md">
        <div class="space-y-4 text-xs">
            <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-900 space-y-1">
                <div class="flex items-center justify-between">
                    <span class="font-mono font-bold text-emerald-700" x-text="selectedProject.project_code"></span>
                    <span class="px-2 py-0.5 rounded bg-emerald-100 text-emerald-800 font-bold text-[10px]">COMPLETED (LOLOS QC)</span>
                </div>
                <div class="font-bold text-slate-900 text-sm" x-text="selectedProject.title"></div>
                <div>Klien: <strong x-text="selectedProject.order?.customer?.name"></strong></div>
            </div>

            <div x-show="selectedProject.deliverable_url" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-1">
                <span class="text-slate-500 font-semibold block">Tautan Deliverable Final:</span>
                <a :href="selectedProject.deliverable_url" target="_blank" class="text-blue-600 hover:underline font-mono break-all" x-text="selectedProject.deliverable_url"></a>
            </div>

            <template x-if="selectedProject.order && selectedProject.order.payment_status !== 'PAID'">
                <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 flex items-center justify-between gap-3">
                    <div>
                        <div class="font-bold">Menunggu Pelunasan Tagihan Order</div>
                        <div class="text-[11px] text-amber-700">Pekerjaan teknis telah selesai. Finance dapat menindaklanjuti pelunasan akhir.</div>
                    </div>
                    <a href="{{ route('orders.index', ['filter' => 'waiting_payment']) }}" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-lg shrink-0">
                        Buka Order &rarr;
                    </a>
                </div>
            </template>

            <div class="flex justify-end pt-2 border-t border-slate-100">
                <button type="button" @click="openDeliveryModal = false" class="px-4 py-2 bg-slate-900 text-white font-semibold rounded-xl cursor-pointer">Tutup</button>
            </div>
        </div>
    </x-viscode-modal>

    <!-- ================================================================= -->
    <!-- MODAL 5: KELOLA TUGAS & DETAIL PROYEK                             -->
    <!-- ================================================================= -->
    <x-viscode-modal name="openProjectDetail" title="Manajemen Tugas & Detail Proyek" maxWidth="3xl">
        <div class="space-y-5 text-xs">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-200">
                <div>
                    <span class="font-mono text-blue-600 font-bold" x-text="selectedProject.project_code"></span>
                    <h3 class="text-slate-900 font-bold text-base mt-0.5" x-text="selectedProject.title"></h3>
                    <div class="text-[11px] text-slate-500 mt-0.5">PM: <span class="font-semibold text-slate-700" x-text="selectedProject.project_manager?.name"></span> • Status: <span class="font-bold text-blue-600" x-text="selectedProject.status"></span></div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <form :action="`/projects/${selectedProject.id}/apply-template`" method="POST" class="inline"
                          onsubmit="return window.confirmFormSubmit(event, {
                              title: 'Terapkan Template Tugas?',
                              text: 'Sistem akan otomatis menambahkan serangkaian tugas milestone standar ke proyek ini.',
                              icon: 'question',
                              confirmButtonText: 'Terapkan Template',
                              confirmButtonColor: '#2563EB'
                          })">
                        @csrf
                        <input type="hidden" name="template_key" :value="selectedProject.order?.service?.category ?? 'web_development'">
                        <button type="submit" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 font-semibold rounded-lg shadow-xs transition-colors cursor-pointer flex items-center gap-1.5">
                            ⚡ Template Tugas
                        </button>
                    </form>

                    <button type="button" @click="openAddTask = true" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg shadow-sm transition-colors cursor-pointer flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        + Tugas Kustom
                    </button>
                </div>
            </div>

            <div x-show="selectedProject.deliverable_url" class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                <span class="text-slate-600 font-semibold block mb-1">Tautan Deliverable Hasil Akhir:</span>
                <a :href="selectedProject.deliverable_url" target="_blank" class="text-blue-600 hover:underline font-mono text-xs break-all" x-text="selectedProject.deliverable_url"></a>
            </div>

            <div x-show="selectedProject.qc_notes" class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800">
                <span class="font-bold block mb-1">Catatan Quality Control (QC):</span>
                <pre class="font-sans text-[11px] whitespace-pre-wrap" x-text="selectedProject.qc_notes"></pre>
            </div>

            <!-- Daftar Tasks -->
            <div>
                <h4 class="font-bold text-slate-900 uppercase text-xs tracking-wider mb-2.5">Daftar Tugas Tim (Tasks)</h4>
                <div class="space-y-2.5 max-h-72 overflow-y-auto pr-1">
                    <template x-for="task in selectedProject.tasks" :key="task.id">
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-xs flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="font-semibold text-slate-900 text-sm" x-text="task.title"></div>
                                <div class="text-[11px] text-slate-500 mt-0.5">
                                    PIC: <span class="font-medium text-slate-700" x-text="task.assignee?.name ?? '-'"></span> • Deadline: <span class="font-mono" x-text="task.deadline ? task.deadline.substring(0,10) : 'Fleksibel'"></span>
                                </div>
                                <div x-show="task.submission_url" class="mt-1">
                                    <a :href="task.submission_url" target="_blank" class="text-[11px] text-blue-600 hover:underline inline-flex items-center gap-1">🔗 File Hasil ↗</a>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold uppercase rounded-full border"
                                      :class="{
                                          'bg-slate-100 text-slate-700 border-slate-200': task.status === 'TODO',
                                          'bg-blue-50 text-blue-700 border-blue-200': task.status === 'IN_PROGRESS',
                                          'bg-indigo-50 text-indigo-700 border-indigo-200': task.status === 'IN_REVIEW',
                                          'bg-amber-50 text-amber-700 border-amber-200': task.status === 'REVISION',
                                          'bg-emerald-50 text-emerald-700 border-emerald-200': task.status === 'DONE'
                                      }"
                                      x-text="task.status"></span>

                                <!-- One Primary Action per Task -->
                                <form x-show="task.status === 'TODO'" :action="`/tasks/${task.id}/start`" method="POST" class="inline"
                                      onsubmit="return window.confirmFormSubmit(event, {
                                          title: 'Mulai Pengerjaan Tugas?',
                                          text: 'Status tugas akan beralih ke IN_PROGRESS.',
                                          icon: 'info',
                                          confirmButtonText: 'Mulai',
                                          confirmButtonColor: '#2563EB'
                                      })">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-blue-600 hover:bg-blue-500 text-white rounded-lg text-xs font-semibold shadow-xs cursor-pointer">Mulai</button>
                                </form>

                                <button x-show="task.status === 'IN_PROGRESS' || task.status === 'REVISION'" 
                                        type="button" 
                                        @click="selectedTask = task; openSubmitTask = true" 
                                        class="px-2.5 py-1 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg text-xs font-semibold shadow-xs cursor-pointer">
                                    Serahkan
                                </button>

                                <form x-show="task.status === 'IN_REVIEW'" :action="`/tasks/${task.id}/approve`" method="POST" class="inline"
                                      onsubmit="return window.confirmFormSubmit(event, {
                                          title: 'Setujui Hasil Tugas Ini?',
                                          text: 'Pekerjaan tugas dinyatakan sesuai dan ditandai sebagai DONE.',
                                          icon: 'success',
                                          confirmButtonText: 'Setujui Tugas',
                                          confirmButtonColor: '#16A34A'
                                      })">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-semibold shadow-xs cursor-pointer">✓ Setujui</button>
                                </form>
                            </div>
                        </div>
                    </template>
                    <div x-show="!selectedProject.tasks || selectedProject.tasks.length === 0" class="py-8 text-center text-slate-500 bg-slate-50/50 rounded-xl border border-dashed border-slate-200">
                        Belum ada tugas ditambahkan. Klik tombol "⚡ Template Tugas" di atas untuk mengisi tugas otomatis.
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-slate-100">
                <div>
                    <template x-if="['IN_PROGRESS', 'REVISION'].includes(selectedProject.status)">
                        <button type="button" @click="openProjectDetail = false; openSubmitQc = true" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 font-semibold rounded-lg cursor-pointer">
                            🔍 Ajukan Proyek ke QC
                        </button>
                    </template>
                    @if(Auth::user()->isSuperAdmin())
                    <button type="button" @click="openProjectDetail = false; overrideRecordId = selectedProject.id; openOverride = true" class="px-2.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-200 text-[10px] font-bold rounded-lg cursor-pointer ml-1">
                        ⚡ Admin Override
                    </button>
                    @endif
                </div>
                <button type="button" @click="openProjectDetail = false" class="px-4 py-2 bg-slate-900 text-white text-xs font-semibold rounded-xl cursor-pointer">Tutup</button>
            </div>
        </div>
    </x-viscode-modal>

    <!-- MODAL: TAMBAH TASK KE PROYEK -->
    <x-viscode-modal name="openAddTask" title="Tambah Tugas Anggota Tim" maxWidth="md">
        <form :action="`/projects/${selectedProject.id}/tasks`" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Judul Tugas</label>
                <input type="text" name="title" required placeholder="Desain High-Fidelity Homepage di Figma..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tugaskan ke Anggota (PIC)</label>
                <select name="assigned_to" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($team as $m)
                        <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->role_label }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Deadline Tugas</label>
                <input type="date" name="deadline" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi Tugas</label>
                <textarea name="description" rows="2" placeholder="Instruksi spesifik pengerjaan..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openAddTask = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Tambah Tugas</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: SERAHKAN HASIL TASK -->
    <x-viscode-modal name="openSubmitTask" title="Serahkan Hasil Pengerjaan Tugas" maxWidth="md">
        <form :action="`/tasks/${selectedTask.id}/submit`" method="POST" class="space-y-4">
            @csrf
            <div class="p-3.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-800 text-xs leading-relaxed">
                Tugas: <strong x-text="selectedTask.title"></strong>. Menyerahkan hasil pekerjaan akan mengubah status task menjadi <strong>IN_REVIEW</strong>.
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tautan File Hasil (Figma / GitHub / GDrive)</label>
                <input type="url" name="submission_url" required placeholder="https://..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Catatan Pengerjaan (Opsional)</label>
                <textarea name="submission_notes" rows="2" placeholder="Selesai pengerjaan sesuai brief..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openSubmitTask = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Kirim Hasil Tugas</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- SUPER ADMIN STATUS OVERRIDE -->
    <x-viscode-override-modal module="projects" :statuses="['NOT_STARTED', 'IN_PROGRESS', 'REVIEW', 'REVISION', 'COMPLETED', 'CANCELLED']" />
</x-app-layout>
