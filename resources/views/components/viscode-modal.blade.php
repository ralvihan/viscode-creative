@props(['name', 'title', 'maxWidth' => '2xl'])

@php
$widthClasses = match ($maxWidth) {
    'sm' => 'max-w-md',
    'md' => 'max-w-xl',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-3xl',
    '2xl' => 'max-w-3xl',
    '3xl' => 'max-w-4xl',
    '4xl' => 'max-w-5xl',
    default => 'max-w-2xl',
};
@endphp

<div x-show="{{ $name }}" style="display: none;" 
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="{{ $name }} = false">
    
    <div class="min-h-full flex items-center justify-center p-4 sm:p-6">
        <!-- Area Klik Luar untuk Menutup -->
        <div @click="{{ $name }} = false" class="fixed inset-0"></div>

        <!-- Konten Kotak Pop-Up (Pure White #FFFFFF Modern Luxury Card) -->
        <div class="relative bg-white border border-slate-200 rounded-2xl {{ $widthClasses }} w-full p-6 sm:p-8 shadow-2xl z-10 text-slate-800 transition-all transform my-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             @click.stop>
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6">
                <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                    <span class="w-1.5 h-5 bg-blue-600 rounded-full inline-block shrink-0"></span>
                    <span>{{ $title }}</span>
                </h3>
                <button type="button" @click="{{ $name }} = false" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 text-lg leading-none transition cursor-pointer shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            
            <!-- Modal Body Content -->
            {{ $slot }}
        </div>
    </div>
</div>
