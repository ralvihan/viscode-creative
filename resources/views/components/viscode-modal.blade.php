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

<div x-show="{{ $name }}"
     style="display: none; margin: 0 !important;"
     x-init="$watch('{{ $name }}', val => {
         if (val) {
             document.body.classList.add('overflow-hidden');
             setTimeout(() => window.dispatchEvent(new CustomEvent('viscode-modal-opened', { detail: { modal: $el } })), 50);
         } else {
             document.body.classList.remove('overflow-hidden');
             window.dispatchEvent(new CustomEvent('viscode-modal-closed'));
         }
     })"
     class="fixed inset-0 z-[100] !m-0 flex items-center justify-center p-3 sm:p-6"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="{{ $name }} = false">

    <!-- Fullscreen Viewport Backdrop -->
    <div @click="{{ $name }} = false" class="fixed inset-0 bg-slate-900/65 backdrop-blur-xs"></div>

    <!-- Modal Card (Pinned Header + Scrollable Interactive Body) -->
    <div data-viscode-modal-box
         data-modal-title="{{ $title }}"
         class="relative bg-white border border-slate-200 rounded-2xl {{ $widthClasses }} w-full max-h-[90vh] flex flex-col shadow-2xl z-10 text-slate-800 overflow-hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.stop>

        <!-- Pinned Modal Header -->
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-white shrink-0">
            <h3 class="text-base sm:text-lg font-bold text-slate-900 tracking-tight flex items-center gap-2.5">
                <span class="w-1.5 h-5 bg-blue-600 rounded-full inline-block shrink-0"></span>
                <span>{{ $title }}</span>
            </h3>
            <button type="button" @click="{{ $name }} = false" class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 text-lg leading-none transition cursor-pointer shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Scrollable Modal Body Content -->
        <div data-viscode-modal-body class="px-6 py-5 overflow-y-auto flex-1">
            {{ $slot }}
        </div>
    </div>
</div>
