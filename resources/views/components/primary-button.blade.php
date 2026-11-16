<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-500 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-wider shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer']) }}>
    {{ $slot }}
</button>
