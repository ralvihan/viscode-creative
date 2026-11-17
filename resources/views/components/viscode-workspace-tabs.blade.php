@props(['workspace' => 'business', 'active' => ''])

@php
    $counts = $navCounts ?? [];
@endphp

@if($workspace === 'business')
<div class="bg-white border border-slate-200 rounded-2xl p-1.5 shadow-2xs flex items-center gap-1.5 overflow-x-auto">
    @if(Auth::user()->canAccessModule('orders') || Auth::user()->canAccessModule('quotations'))
    @php
        $isSalesOrdersActive = request()->routeIs('orders.*', 'quotations.*') && $active !== 'services' && request('tab') !== 'services';
        $salesOrdersBadge = $counts['sales_orders_total_active'] ?? 0;
    @endphp
    <a href="{{ route('orders.index') }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isSalesOrdersActive ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
        <span>Sales &amp; Orders (Quotations &amp; Orders)</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isSalesOrdersActive ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
            {{ $salesOrdersBadge }}
        </span>
    </a>
    @endif

    @if(Auth::user()->canAccessModule('leads'))
    @php
        $isLeadsActive = request()->routeIs('leads.*');
        $leadsBadge = $counts['leads_total_active'] ?? 0;
        $leadsNew = $counts['leads_new'] ?? 0;
    @endphp
    <a href="{{ route('leads.index') }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isLeadsActive ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
        <span>Sales Pipeline (Leads)</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isLeadsActive ? 'bg-blue-500 text-white' : ($leadsNew > 0 ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-700') }}">
            {{ $leadsBadge }}
        </span>
        @if($leadsNew > 0)
            <span class="px-1.5 py-0.5 rounded-full text-[9px] font-mono font-extrabold uppercase {{ $isLeadsActive ? 'bg-amber-400 text-slate-900' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                {{ $leadsNew }} NEW
            </span>
        @endif
    </a>
    @endif

    @if(Auth::user()->canAccessModule('customers'))
    @php
        $isCustomersActive = request()->routeIs('customers.*');
    @endphp
    <a href="{{ route('customers.index') }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isCustomersActive ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
        <span>Customers</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isCustomersActive ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">
            {{ $counts['customers_total'] ?? 0 }}
        </span>
    </a>
    @endif

    @if(Auth::user()->canAccessModule('orders') || Auth::user()->canAccessModule('quotations'))
    @php
        $isServicesActive = request('tab') === 'services' || $active === 'services';
    @endphp
    <a href="{{ route('orders.index', ['tab' => 'services']) }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isServicesActive ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
        <span>Services &amp; Packages</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isServicesActive ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">
            {{ $counts['services_total'] ?? 0 }}
        </span>
    </a>
    @endif
</div>
@elseif($workspace === 'operations')
<div class="bg-white border border-slate-200 rounded-2xl p-1.5 shadow-2xs flex items-center gap-1.5 overflow-x-auto">
    @if(Auth::user()->canAccessModule('projects'))
    @php
        $isProjectsTab = request()->routeIs('projects.*') && request('tab', 'projects') === 'projects';
        $isMyTasksTab = request()->routeIs('projects.*') && request('tab') === 'my_tasks';
        $isQcTab = request()->routeIs('projects.*') && request('tab') === 'qc_review';
    @endphp
    <a href="{{ route('projects.index', ['tab' => 'projects']) }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isProjectsTab ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
        <span>Projects</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isProjectsTab ? 'bg-blue-500 text-white' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
            {{ $counts['projects_active'] ?? 0 }}
        </span>
    </a>
    <a href="{{ route('projects.index', ['tab' => 'my_tasks']) }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isMyTasksTab ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>My Tasks</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isMyTasksTab ? 'bg-blue-500 text-white' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
            {{ $counts['my_tasks_pending'] ?? 0 }}
        </span>
    </a>
    <a href="{{ route('projects.index', ['tab' => 'qc_review']) }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isQcTab ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
        <span>Review &amp; QC</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isQcTab ? 'bg-blue-500 text-white' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
            {{ $counts['qc_review_pending'] ?? 0 }}
        </span>
    </a>
    @endif

    @if(Auth::user()->canAccessModule('documentation'))
    @php
        $isDocTab = request()->routeIs('documentations.*');
    @endphp
    <a href="{{ route('documentations.index') }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isDocTab ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
        <span>Deliverables &amp; Assets</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isDocTab ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">
            {{ $counts['documentations_total'] ?? 0 }}
        </span>
    </a>
    @endif
</div>
@elseif($workspace === 'finance')
<div class="bg-white border border-slate-200 rounded-2xl p-1.5 shadow-2xs flex items-center gap-1.5 overflow-x-auto">
    @if(Auth::user()->canAccessModule('payments'))
    @php
        $isPaymentsTab = request()->routeIs('payments.*');
    @endphp
    <a href="{{ route('payments.index') }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isPaymentsTab ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Payments &amp; Receivables</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isPaymentsTab ? 'bg-blue-500 text-white' : (($counts['payments_pending'] ?? 0) > 0 ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600') }}">
            {{ $counts['payments_pending'] ?? 0 }}
        </span>
    </a>
    @endif

    @if(Auth::user()->canAccessModule('purchases'))
    @php
        $isPurchasesTab = request()->routeIs('purchases.*');
    @endphp
    <a href="{{ route('purchases.index') }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isPurchasesTab ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
        <span>Purchases &amp; Expenses</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isPurchasesTab ? 'bg-blue-500 text-white' : 'bg-purple-50 text-purple-700 border border-purple-200' }}">
            {{ $counts['purchases_active'] ?? 0 }}
        </span>
    </a>
    @endif

    @if(Auth::user()->canAccessModule('financials'))
    @php
        $isTransactionsTab = request()->routeIs('transactions.*');
    @endphp
    <a href="{{ route('transactions.index') }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isTransactionsTab ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
        <span>Financial Overview (Cashbook)</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isTransactionsTab ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">
            {{ $counts['transactions_total'] ?? 0 }}
        </span>
    </a>
    @endif

    @if(Auth::user()->canAccessModule('reports'))
    @php
        $isReportsTab = request()->routeIs('reports.*');
    @endphp
    <a href="{{ route('reports.index') }}"
       class="px-4 py-2 rounded-xl text-xs font-bold transition-all duration-200 flex items-center gap-2 whitespace-nowrap {{ $isReportsTab ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        <span>Monthly Reports</span>
        <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono {{ $isReportsTab ? 'bg-blue-500 text-white' : 'bg-slate-100 text-slate-600' }}">
            {{ $counts['reports_pending'] ?? 0 }}
        </span>
    </a>
    @endif
</div>
@endif
