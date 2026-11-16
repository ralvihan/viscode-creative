<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-2xl text-slate-900 tracking-tight flex items-center gap-3">
                <span class="w-2.5 h-7 bg-blue-600 rounded-full inline-block"></span>
                {{ __('Pengaturan Akun & Profil') }}
            </h2>
            <p class="text-sm text-slate-500 mt-1">Kelola data identitas, email akun, kata sandi, dan keamanan profil Anda</p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 sm:p-8 bg-white border border-slate-200 shadow-sm rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-slate-200 shadow-sm rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-slate-200 shadow-sm rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
