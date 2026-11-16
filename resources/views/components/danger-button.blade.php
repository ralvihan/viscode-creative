<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-rose-600 hover:bg-rose-500 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-wider shadow-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>
