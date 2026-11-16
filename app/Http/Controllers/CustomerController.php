<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Service;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->canAccessModule('customers'), 403, 'Anda tidak memiliki hak akses modul Pelanggan.');

        $customers = Customer::withCount(['orders', 'quotations', 'leads'])
            ->latest()
            ->paginate(15);

        $services = Service::where('is_active', true)->get();
        $team = User::where('role', '!=', 'customer')->get();

        return view('customers.index', compact('customers', 'services', 'team'));
    }

    public function store(Request $request)
    {
        abort_if(! auth()->user()->canAccessModule('customers'), 403, 'Anda tidak memiliki hak akses modul Pelanggan.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:25',
            'email' => 'nullable|email|max:255',
            'type' => 'required|in:umkm,organisasi,mahasiswa,individu,event_organizer,lainnya',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);
        ActivityLogger::log('Customers', (string) $customer->id, 'CREATE', null, $customer->toArray(), 'Tambah pelanggan baru');

        return back()->with('success', 'Pelanggan '.$customer->name.' berhasil ditambahkan!');
    }

    public function update(Request $request, Customer $customer)
    {
        abort_if(! auth()->user()->canAccessModule('customers'), 403, 'Anda tidak memiliki hak akses modul Pelanggan.');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:25',
            'email' => 'nullable|email|max:255',
            'type' => 'required|in:umkm,organisasi,mahasiswa,individu,event_organizer,lainnya',
            'notes' => 'nullable|string',
        ]);

        $old = $customer->toArray();
        $customer->update($validated);
        ActivityLogger::log('Customers', (string) $customer->id, 'UPDATE', $old, $customer->toArray(), 'Perbaruan data pelanggan');

        return back()->with('success', 'Data pelanggan '.$customer->name.' berhasil diperbarui!');
    }
}
