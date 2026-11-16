<x-app-layout x-data="{ openCreateTrx: {{ request()->has('create') ? 'true' : 'false' }} }">
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="font-bold text-2xl text-slate-900 tracking-tight flex items-center gap-3">
                    <span class="w-2.5 h-7 bg-blue-600 rounded-full inline-block"></span>
                    Buku Kas Pemasukan & Pengeluaran
                </h2>
                <p class="text-sm text-slate-500 mt-1">Pencatatan arus kas operasional bisnis, mutasi keuangan, dan audit transaksi</p>
            </div>
            <button type="button" @click="openCreateTrx = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Catat Transaksi
            </button>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <!-- Segmented Workspace Navigation -->
            <x-viscode-workspace-tabs workspace="finance" />

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

            <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-200 text-xs uppercase font-semibold text-slate-500 bg-slate-50/50">
                            <th class="py-3.5 px-4">Tanggal & Kode</th>
                            <th class="py-3.5 px-4">Kategori & Keterangan</th>
                            <th class="py-3.5 px-4">Dicatat Oleh</th>
                            <th class="py-3.5 px-4 text-right">Nominal</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="text-slate-900 font-medium text-xs">{{ \Carbon\Carbon::parse($trx->transaction_date)->format('d M Y') }}</div>
                                <div class="font-mono text-[11px] text-slate-400 mt-0.5">{{ $trx->transaction_code }}</div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-150 inline-block mb-1">
                                    {{ $trx->category }}
                                </span>
                                <div class="text-xs text-slate-600">{{ $trx->description }}</div>
                            </td>
                            <td class="py-3.5 px-4 text-xs text-slate-600 font-medium">{{ $trx->recorder->name }}</td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-sm {{ $trx->type === 'income' ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $trx->type === 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <form action="{{ route('transactions.destroy', $trx) }}" method="POST" 
                                      onsubmit="return window.confirmFormSubmit(event, {
                                          title: 'Batalkan Transaksi {{ $trx->transaction_code }}?',
                                          text: 'Transaksi sebesar Rp {{ number_format($trx->amount, 0, ',', '.') }} ({{ $trx->category }}) akan dihapus dari buku kas.',
                                          icon: 'warning',
                                          confirmText: 'Ya, Batalkan Transaksi',
                                          confirmColor: '#DC2626'
                                      })">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50 border border-transparent hover:border-rose-200 rounded-lg transition-colors cursor-pointer">Batal</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div class="font-medium text-slate-600">Belum ada transaksi tercatat</div>
                                <p class="text-xs text-slate-400 mt-1">Gunakan tombol "+ Catat Transaksi" untuk memasukkan arus kas.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $transactions->links() }}</div>
            </div>
        </div>
    </div>

    <!-- POP-UP MODAL: CATAT TRANSAKSI -->
    <x-viscode-modal name="openCreateTrx" title="Catat Transaksi Keuangan">
        <form action="{{ route('transactions.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Arus Kas</label>
                    <select name="type" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="income">Pemasukan (Income)</option>
                        <option value="expense">Pengeluaran (Expense)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Kategori</label>
                    <input type="text" name="category" placeholder="DP / Pelunasan / Domain / Internet" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tautkan ke Pesanan (Khusus Pemasukan)</label>
                <select name="order_id" class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">-- Tidak terkait pesanan / Pengeluaran Umum --</option>
                    @foreach($orders as $order)
                        <option value="{{ $order->id }}">{{ $order->order_code }} - {{ $order->customer->name }} (Deal: Rp {{ number_format($order->agreed_price, 0, ',', '.') }})</option>
                    @endforeach
                </select>
            </div>

            <x-viscode-rupiah-input
                name="amount"
                label="Nominal Transaksi"
                placeholder="Contoh: 500.000"
                :required="true"
            />

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Transaksi <span class="text-rose-500">*</span></label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Metode Pembayaran <span class="text-rose-500">*</span></label>
                    <input type="text" name="payment_method" value="Transfer / QRIS" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Keterangan Detail</label>
                <input type="text" name="description" placeholder="Misal: DP 50% Pembuatan Landing Page" required class="w-full rounded-xl bg-white border-slate-300 text-slate-900 text-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Upload Bukti Transfer / Struk (Opsional)</label>
                <input type="file" name="proof_image" accept="image/*" class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-slate-100">
                <button type="button" @click="openCreateTrx = false" class="px-4 py-2.5 bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-sm font-semibold rounded-xl cursor-pointer">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-sm cursor-pointer">Simpan Transaksi</button>
            </div>
        </form>
    </x-viscode-modal>
</x-app-layout>
