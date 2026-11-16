<x-app-layout x-data="{
    openCreate: false,
    openDelete: false,
    openDetail: false,
    selectedDoc: null,
    deleteUrl: '',
    filterCategory: 'all',
    setDelete(doc) {
        this.selectedDoc = doc;
        this.deleteUrl = '{{ url('/documentations') }}/' + doc.id;
        this.openDelete = true;
    },
    setDetail(doc) {
        this.selectedDoc = doc;
        this.openDetail = true;
    }
}">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-7 bg-cyan-500 rounded-full inline-block"></span>
                    Dokumentasi, Deliverable & Aset Proyek
                </h2>
                <p class="text-sm text-slate-500 mt-1">Penyimpanan terpusat untuk deliverables klien, berkas kontrak, SOP internal, dan aset desain</p>
            </div>
            <button type="button" @click="openCreate = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Simpan Dokumen / Aset
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Segmented Workspace Navigation -->
            <x-viscode-workspace-tabs workspace="operations" />

            @if(session('success'))
                <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
                </div>
            @endif

            @if($errors->any())
                <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
                    <div class="font-bold mb-1">Perhatian:</div>
                    <ul class="list-disc list-inside space-y-1 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Quick Category Pills -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2">
                <button type="button" @click="filterCategory = 'all'" :class="filterCategory === 'all' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 border border-slate-200 shadow-xs'" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition shrink-0 cursor-pointer">
                    Semua Berkas
                </button>
                <button type="button" @click="filterCategory = 'project_deliverable'" :class="filterCategory === 'project_deliverable' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 border border-slate-200 shadow-xs'" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition shrink-0 flex items-center gap-1.5 cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Deliverables Klien
                </button>
                <button type="button" @click="filterCategory = 'contract'" :class="filterCategory === 'contract' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 border border-slate-200 shadow-xs'" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition shrink-0 flex items-center gap-1.5 cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span> Kontrak & Perjanjian
                </button>
                <button type="button" @click="filterCategory = 'asset'" :class="filterCategory === 'asset' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 border border-slate-200 shadow-xs'" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition shrink-0 flex items-center gap-1.5 cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-purple-500"></span> Aset Digital & Desain
                </button>
                <button type="button" @click="filterCategory = 'sop'" :class="filterCategory === 'sop' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 border border-slate-200 shadow-xs'" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition shrink-0 flex items-center gap-1.5 cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span> SOP & Panduan
                </button>
                <button type="button" @click="filterCategory = 'presentation'" :class="filterCategory === 'presentation' ? 'bg-blue-600 text-white shadow-xs' : 'bg-white text-slate-600 hover:text-slate-900 hover:bg-slate-50 border border-slate-200 shadow-xs'" class="px-3.5 py-1.5 rounded-xl text-xs font-semibold transition shrink-0 flex items-center gap-1.5 cursor-pointer">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span> Pitch & Presentasi
                </button>
            </div>

            <!-- Documents Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse($documentations as $doc)
                <div x-show="filterCategory === 'all' || filterCategory === '{{ $doc->category }}'" class="bg-white border border-slate-200 hover:border-slate-300 rounded-2xl p-5 shadow-sm hover:shadow transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between gap-3">
                            @php
                                $catMap = [
                                    'project_deliverable' => ['Deliverable Klien', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                    'contract' => ['Kontrak Klien', 'bg-amber-50 text-amber-700 border-amber-200'],
                                    'sop' => ['SOP Internal', 'bg-blue-50 text-blue-700 border-blue-200'],
                                    'asset' => ['Aset Desain', 'bg-purple-50 text-purple-700 border-purple-200'],
                                    'presentation' => ['Pitch / Presentasi', 'bg-rose-50 text-rose-700 border-rose-200'],
                                    'other' => ['Dokumen Lain', 'bg-slate-100 text-slate-700 border-slate-200'],
                                ];
                                $catInfo = $catMap[$doc->category] ?? ['Dokumen', 'bg-slate-100 text-slate-700 border-slate-200'];
                            @endphp
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $catInfo[1] }}">
                                {{ $catInfo[0] }}
                            </span>

                            <div class="flex items-center gap-1">
                                <button type="button" @click="setDetail({{ json_encode($doc) }})" class="p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors cursor-pointer" title="Lihat Rincian">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </button>
                                <button type="button" @click="setDelete({{ json_encode($doc) }})" class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors cursor-pointer" title="Hapus Dokumen">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>

                        <h4 class="font-bold text-slate-900 text-sm mt-3 leading-snug group-hover:text-blue-600 transition-colors">{{ $doc->title }}</h4>

                        @if($doc->project)
                            <div class="mt-2 text-xs text-slate-500 flex items-center gap-1.5">
                                <span class="font-mono text-blue-600 font-semibold">{{ $doc->project->project_code }}</span>
                                <span>&bull;</span>
                                <span class="truncate">{{ $doc->project->title }}</span>
                            </div>
                        @endif

                        @if($doc->description)
                            <p class="text-xs text-slate-500 mt-2 line-clamp-2 leading-relaxed">{{ $doc->description }}</p>
                        @endif
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                        <div class="text-[11px] text-slate-500">
                            <span class="font-medium text-slate-700">Oleh {{ $doc->uploader->name ?? 'Tim' }}</span>
                            <div class="text-[10px] text-slate-400 mt-0.5">{{ $doc->created_at->format('d M Y') }}</div>
                        </div>

                        <div class="flex items-center gap-2">
                            @if($doc->file_path)
                                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 hover:text-slate-900 rounded-xl text-xs font-semibold transition-colors flex items-center gap-1.5" title="Unduh / Buka Berkas Terunggah">
                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Berkas
                                </a>
                            @endif

                            @if($doc->file_url)
                                <a href="{{ $doc->file_url }}" target="_blank" rel="noopener noreferrer" class="px-3 py-1.5 bg-blue-50 hover:bg-blue-100 border border-blue-200 text-blue-700 rounded-xl text-xs font-semibold transition-colors flex items-center gap-1.5" title="Buka Link Eksternal (Drive / Figma / Repository)">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    Buka Link
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-16 text-center bg-white border border-slate-200 rounded-2xl shadow-sm">
                    <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="text-sm font-bold text-slate-900">Belum ada dokumen atau aset tersimpan</div>
                    <p class="text-xs text-slate-500 mt-1">Gunakan tombol "+ Simpan Dokumen / Aset" di atas untuk menambahkan berkas atau tautan proyek.</p>
                </div>
                @endforelse
            </div>

            @if($documentations->hasPages())
                <div class="mt-6">
                    {{ $documentations->links() }}
                </div>
            @endif

        </div>
    </div>

    <!-- MODAL: SIMPAN DOKUMEN / ASET BARU -->
    <x-viscode-modal name="openCreate" title="Unggah Dokumen / Tautkan Deliverable Proyek" maxWidth="lg">
        <form action="{{ route('documentations.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Judul Dokumen / Nama Aset <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required placeholder="Contoh: Desain UI Final Figma / Surat Perjanjian MoU" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kategori Dokumen <span class="text-rose-500">*</span></label>
                    <select name="category" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="project_deliverable">Deliverable Klien (Hasil Proyek)</option>
                        <option value="contract">Kontrak / MoU Klien</option>
                        <option value="sop">SOP & Panduan Standar Internal</option>
                        <option value="asset">Aset Desain / Brand Assets</option>
                        <option value="presentation">Pitch Deck & Bahan Presentasi</option>
                        <option value="other">Dokumen Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tautkan ke Proyek (Opsional)</label>
                    <select name="project_id" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">-- Berkas Umum (Tidak terkait proyek tertentu) --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->project_code }} - {{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tautan Link Eksternal (Google Drive / Figma / GitHub / dsb.)</label>
                <input type="url" name="file_url" placeholder="https://drive.google.com/... atau https://figma.com/..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Atau Unggah Berkas Langsung (Maks. 10MB - PDF/ZIP/DOCX/JPG/PNG)</label>
                <input type="file" name="file" class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Deskripsi & Catatan</label>
                <textarea name="description" rows="3" placeholder="Informasi versi berkas, petunjuk akses, atau catatan penting..." class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreate = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Simpan Dokumen</button>
            </div>
        </form>
    </x-viscode-modal>

    <!-- MODAL: DETAIL DOKUMEN -->
    <x-viscode-modal name="openDetail" title="Rincian Dokumen & Aset" maxWidth="md">
        <div class="space-y-4 text-xs" x-show="selectedDoc">
            <div>
                <span class="text-slate-500 font-semibold uppercase tracking-wider">Judul Berkas:</span>
                <div class="text-sm font-bold text-slate-900 mt-1" x-text="selectedDoc ? selectedDoc.title : ''"></div>
            </div>

            <div class="grid grid-cols-2 gap-3 p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700">
                <div>
                    <span class="text-slate-500">Kategori:</span>
                    <div class="font-bold text-blue-600 mt-0.5" x-text="selectedDoc ? selectedDoc.category : ''"></div>
                </div>
                <div>
                    <span class="text-slate-500">Diunggah:</span>
                    <div class="font-semibold text-slate-900 mt-0.5" x-text="selectedDoc ? selectedDoc.created_at : ''"></div>
                </div>
            </div>

            <template x-if="selectedDoc && selectedDoc.description">
                <div>
                    <span class="text-slate-500 font-semibold uppercase tracking-wider">Deskripsi:</span>
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-700 mt-1 whitespace-pre-line leading-relaxed" x-text="selectedDoc.description"></div>
                </div>
            </template>

            <div class="space-y-2 pt-2">
                <template x-if="selectedDoc && selectedDoc.file_url">
                    <a :href="selectedDoc.file_url" target="_blank" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-xl shadow-sm transition-colors">
                        <span>Buka Tautan Eksternal</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                </template>

                <template x-if="selectedDoc && selectedDoc.file_path">
                    <a :href="'{{ asset('storage') }}/' + selectedDoc.file_path" target="_blank" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold rounded-xl shadow-xs transition-colors">
                        <span>Unduh Berkas Tersimpan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    </a>
                </template>
            </div>

            <div class="flex justify-end pt-3 border-t border-slate-100">
                <button type="button" @click="openDetail = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Tutup</button>
            </div>
        </div>
    </x-viscode-modal>

    <!-- MODAL: KONFIRMASI HAPUS DOKUMEN -->
    <x-viscode-modal name="openDelete" title="Konfirmasi Hapus Dokumen" maxWidth="sm">
        <form :action="deleteUrl" method="POST" 
              onsubmit="return window.confirmFormSubmit(event, {
                  title: 'Hapus Dokumen ' + (selectedDoc ? selectedDoc.title : '') + '?',
                  text: 'Berkas dan riwayat dokumen ini akan dihapus secara permanen.',
                  icon: 'warning',
                  confirmText: 'Ya, Hapus Dokumen',
                  confirmColor: '#DC2626'
              })"
              class="space-y-4">
            @csrf
            @method('DELETE')
            <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 text-xs text-rose-800 leading-relaxed">
                Apakah Anda yakin ingin menghapus dokumen <strong class="text-rose-900" x-text="selectedDoc ? selectedDoc.title : ''"></strong>? Berkas fisik yang tersimpan juga akan dihapus.
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" @click="openDelete = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Ya, Hapus</button>
            </div>
        </form>
    </x-viscode-modal>
</x-app-layout>
