<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Service;
use App\Models\User;
use App\Services\Workflow\LeadWorkflowService;
use App\Services\Workflow\QuotationWorkflowService;
use Illuminate\Http\Request;
use InvalidArgumentException;

class LeadController extends Controller
{
    public function index()
    {
        abort_if(! auth()->user()->canAccessModule('leads'), 403, 'Anda tidak memiliki hak akses modul Prospek & Leads.');

        $leads = Lead::with(['service', 'customer', 'assignedUser', 'reviewer', 'quotations'])
            ->latest()
            ->paginate(15);

        $services = Service::where('is_active', true)->get();
        $team = User::where('role', '!=', 'customer')->get();
        $customers = Customer::orderBy('name')->get();

        return view('leads.index', compact('leads', 'services', 'team', 'customers'));
    }

    public function store(Request $request, LeadWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('leads'), 403, 'Anda tidak memiliki hak akses modul Prospek & Leads.');

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:25',
            'customer_email' => 'nullable|email|max:255',
            'service_id' => 'nullable|exists:services,id',
            'source' => 'required|in:website,whatsapp,instagram,referral,direct,lainnya',
            'estimated_value' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
            'communication_notes' => 'nullable|string',
        ]);

        $lead = $workflow->createLead($validated);

        return back()->with('success', 'Lead baru '.$lead->lead_code.' berhasil dicatat!');
    }

    public function publicStore(Request $request, LeadWorkflowService $workflow)
    {
        $notes = $request->input('communication_notes', $request->input('notes', ''));
        if ($request->filled('company_name')) {
            $notes = 'Instansi/Brand: '.$request->input('company_name')."\n".$notes;
        }
        if ($request->filled('service_interest')) {
            $notes = 'Minat Paket/Layanan: '.$request->input('service_interest')."\n".$notes;
        }

        $serviceId = $request->input('service_id');
        if (! $serviceId && $request->filled('service_interest')) {
            $interest = strtolower($request->input('service_interest'));
            $matchedService = Service::where('is_active', true)->get()->first(function ($s) use ($interest) {
                return str_contains($interest, strtolower($s->name))
                    || (str_contains($interest, 'web') && $s->category === 'web_development')
                    || (str_contains($interest, 'desain') && $s->category === 'graphic_design')
                    || (str_contains($interest, 'bundling') && $s->category === 'bundling')
                    || (str_contains($interest, 'umkm') && $s->category === 'bundling');
            });
            $serviceId = $matchedService?->id;
        }

        $request->merge([
            'customer_phone' => $request->input('customer_phone', $request->input('whatsapp')),
            'customer_email' => $request->input('customer_email', $request->input('email')),
            'estimated_value' => $request->input('estimated_value', $request->input('estimated_budget', 0)),
            'service_id' => $serviceId,
            'communication_notes' => trim($notes),
        ]);

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:50',
            'customer_email' => 'nullable|email|max:255',
            'service_id' => 'nullable|exists:services,id',
            'estimated_value' => 'nullable|numeric|min:0',
            'communication_notes' => 'nullable|string',
        ]);

        $validated['source'] = 'website';
        $lead = $workflow->createLead($validated);

        return back()->with('public_success', 'Terima kasih! Konsultasi Anda telah kami terima (Kode: '.$lead->lead_code.'). Tim Viscode Creative akan segera menghubungi Anda via WhatsApp.');
    }

    public function review(Request $request, Lead $lead, LeadWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('leads'), 403, 'Anda tidak memiliki hak akses Sales.');

        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'estimated_value' => 'nullable|numeric|min:0',
            'assigned_to' => 'nullable|exists:users,id',
            'review_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $workflow->reviewLead($lead, $validated);

            return back()->with('success', 'Review prospek '.$lead->lead_code.' selesai! Status berhasil diperbarui menjadi REVIEWED.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['review_error' => $e->getMessage()]);
        }
    }

    public function logCommunication(Request $request, Lead $lead, LeadWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('leads'), 403, 'Anda tidak memiliki hak akses Sales.');

        $validated = $request->validate([
            'notes' => 'required|string',
            'contact_date' => 'nullable|date',
            'follow_up_result' => 'nullable|string|max:150',
            'next_follow_up_date' => 'nullable|date',
        ]);

        try {
            $workflow->logCommunication($lead, $validated['notes'], $validated);

            return back()->with('success', 'Catatan follow-up prospek '.$lead->lead_code.' berhasil disimpan! Status: '.$lead->status.'.');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['communication_error' => $e->getMessage()]);
        }
    }

    public function markLost(Request $request, Lead $lead, LeadWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('leads'), 403, 'Anda tidak memiliki hak akses Sales.');

        $request->validate([
            'lost_reason' => 'required|string',
        ]);

        $workflow->markLost($lead, $request->lost_reason);

        return back()->with('success', 'Lead '.$lead->lead_code.' telah ditandai LOST.');
    }

    public function convertToQuotation(Request $request, Lead $lead, QuotationWorkflowService $workflow)
    {
        abort_if(! auth()->user()->canAccessModule('quotations'), 403, 'Anda tidak memiliki hak akses Sales & Quotation.');

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'service_id' => 'required|exists:services,id',
            'scope_description' => 'required|string',
            'price' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required_without:price|nullable|numeric|min:0',
            'estimated_deadline' => 'nullable|date',
            'valid_until' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'submit_action' => 'nullable|in:draft,send',
        ]);

        $validated['lead_id'] = $lead->id;
        $validated['customer_id'] = $lead->customer_id ?? Customer::firstOrCreate(
            ['phone' => $lead->customer_phone],
            [
                'name' => $lead->customer_name,
                'email' => $lead->customer_email,
                'type' => 'individu',
            ]
        )->id;

        if (! $lead->customer_id) {
            $lead->update(['customer_id' => $validated['customer_id']]);
        }

        try {
            $quotation = $workflow->createQuotation($validated);

            if (($validated['submit_action'] ?? 'draft') === 'send') {
                return back()->with('success', 'Quotation '.$quotation->quotation_code.' berhasil diterbitkan dan dikirimkan! Status prospek berubah menjadi QUOTATION_SENT.');
            }

            return back()->with('success', 'Draft Quotation '.$quotation->quotation_code.' berhasil disimpan untuk prospek '.$lead->lead_code.'!');
        } catch (InvalidArgumentException $e) {
            return back()->withErrors(['quotation_error' => $e->getMessage()]);
        }
    }
}
