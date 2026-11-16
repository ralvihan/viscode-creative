<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-1.5">Email Pengguna</label>
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@viscode.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider">Kata Sandi</label>
                @if (Route::has('password.request'))
                    <a class="text-xs text-blue-600 hover:text-blue-500 font-medium" href="{{ route('password.request') }}">
                        Lupa kata sandi?
                    </a>
                @endif
            </div>

            <x-text-input id="password" class="block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />

            <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer">
                <input id="remember_me" type="checkbox" class="rounded-md border-slate-300 text-blue-600 shadow-xs focus:ring-blue-500" name="remember">
                <span class="ms-2 text-xs text-slate-600 font-medium">Ingat sesi saya</span>
            </label>
        </div>

        <div class="pt-2">
            <button type="submit" class="w-full flex items-center justify-center py-2.5 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold text-sm rounded-xl shadow-sm transition-all cursor-pointer">
                Masuk ke Dashboard
            </button>
        </div>
    </form>
</x-guest-layout>
