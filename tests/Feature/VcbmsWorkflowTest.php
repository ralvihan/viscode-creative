<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Project;
use App\Models\PurchaseRequest;
use App\Models\Quotation;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VcbmsWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_website_renders_and_records_lead_from_consultation_form(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('VISCODE');
        $response->assertSee('Konsultasikan Proyek Anda');

        $leadData = [
            'customer_name' => 'John Doe Corp',
            'whatsapp' => '081298765432',
            'email' => 'johndoe@example.com',
            'company_name' => 'Doe Enterprise',
            'service_interest' => 'Website Development',
            'estimated_budget' => 3000000,
            'notes' => 'Butuh landing page modern.',
        ];

        $postResponse = $this->post(route('public.lead.store'), $leadData);
        $postResponse->assertSessionHas('public_success');

        $this->assertDatabaseHas('leads', [
            'customer_name' => 'John Doe Corp',
            'customer_phone' => '081298765432',
            'status' => Lead::STATUS_NEW,
            'source' => 'website',
        ]);
    }

    public function test_order_status_transitions_to_confirmed_when_dp_payment_is_verified(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $finance = User::factory()->create(['role' => User::ROLE_FINANCE]);

        $customer = Customer::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@test.com',
            'phone' => '081234567890',
            'segment' => 'umkm',
        ]);

        $service = Service::create([
            'name' => 'Landing Page UMKM',
            'category' => 'web_development',
            'base_price' => 500000,
            'description' => 'Paket landing page cepat.',
            'estimated_days' => 5,
        ]);

        // Create order
        $order = Order::create([
            'order_code' => 'VSC-ORD-202611-001',
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'agreed_price' => 1000000,
            'down_payment_required' => 500000,
            'brief_description' => 'Website profil toko pakaian.',
            'status' => Order::STATUS_PENDING_PAYMENT,
            'payment_status' => Order::PAYMENT_UNPAID,
        ]);

        $this->assertEquals(Order::STATUS_PENDING_PAYMENT, $order->status);

        // Record a Payment
        $payment = Payment::create([
            'payment_code' => 'PAY-202611-001',
            'order_id' => $order->id,
            'amount' => 500000,
            'payment_type' => 'dp',
            'payment_method' => 'Transfer Bank',
            'payment_date' => now(),
            'status' => Payment::STATUS_PENDING,
            'recorded_by' => $finance->id,
        ]);

        // Acting as Finance, verify the payment
        $response = $this->actingAs($finance)->post(route('payments.verify', $payment));
        $response->assertSessionHas('success');

        $payment->refresh();
        $this->assertEquals(Payment::STATUS_VERIFIED, $payment->status);

        $order->refresh();
        $this->assertEquals(Order::PAYMENT_PARTIALLY_PAID, $order->payment_status);
        $this->assertEquals(Order::STATUS_CONFIRMED, $order->status);

        // Verify automatic transaction entry created in cashbook
        $this->assertDatabaseHas('transactions', [
            'payment_id' => $payment->id,
            'order_id' => $order->id,
            'type' => 'income',
            'amount' => 500000,
        ]);
    }

    public function test_purchase_request_full_workflow_from_approval_to_cashbook_finalization(): void
    {
        $ceo = User::factory()->create(['role' => User::ROLE_CEO]);
        $finance = User::factory()->create(['role' => User::ROLE_FINANCE]);
        $dev = User::factory()->create(['role' => User::ROLE_WEB_DEVELOPER]);

        // Developer creates a purchase request
        $this->actingAs($dev)->post(route('purchases.store'), [
            'title' => 'Lisensi Domain Klien',
            'purpose' => 'Pembelian domain .com untuk proyek aktif',
            'estimated_amount' => 175000,
        ]);

        $purchase = PurchaseRequest::first();
        $this->assertNotNull($purchase);
        $this->assertEquals(PurchaseRequest::STATUS_PENDING, $purchase->status);

        // CEO approves
        $this->actingAs($ceo)->post(route('purchases.approve', $purchase), [
            'approval_notes' => 'Disetujui segera dibeli.',
        ]);
        $purchase->refresh();
        $this->assertEquals(PurchaseRequest::STATUS_APPROVED, $purchase->status);

        // Team records purchase
        $this->actingAs($dev)->post(route('purchases.record-purchase', $purchase), [
            'actual_amount' => 170000,
        ]);
        $purchase->refresh();
        $this->assertEquals(PurchaseRequest::STATUS_PURCHASED, $purchase->status);

        // Finance finalizes and books expense into cashbook
        $this->actingAs($finance)->post(route('purchases.finalize', $purchase), [
            'category' => 'Domain & Hosting',
            'transaction_date' => now()->toDateString(),
            'payment_method' => 'Transfer Bank Operasional',
        ]);
        $purchase->refresh();
        $this->assertEquals(PurchaseRequest::STATUS_COMPLETED, $purchase->status);

        // Cashbook transaction created
        $this->assertDatabaseHas('transactions', [
            'purchase_request_id' => $purchase->id,
            'type' => 'expense',
            'amount' => 170000,
        ]);
    }

    public function test_super_admin_can_override_status_with_activity_log(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $lead = Lead::create([
            'lead_code' => 'VSC-LEAD-202611-001',
            'customer_name' => 'CV Maju Jaya',
            'customer_phone' => '0855112233',
            'source' => 'direct',
            'status' => Lead::STATUS_NEW,
        ]);

        $response = $this->actingAs($superAdmin)->post(route('status-override'), [
            'module' => 'leads',
            'record_id' => $lead->id,
            'new_status' => Lead::STATUS_OFFERED,
            'reason' => 'Perintah khusus klien saat tatap muka langsung.',
        ]);

        $response->assertSessionHas('success');
        $lead->refresh();
        $this->assertEquals(Lead::STATUS_OFFERED, $lead->status);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'Leads',
            'activity' => 'SUPER_ADMIN_STATUS_OVERRIDE',
            'reason' => 'Perintah khusus klien saat tatap muka langsung.',
        ]);
    }

    public function test_unauthorized_user_cannot_approve_purchase_or_access_finance_reports(): void
    {
        $dev = User::factory()->create(['role' => User::ROLE_WEB_DEVELOPER]);

        $purchase = PurchaseRequest::create([
            'request_code' => 'VSC-PR-202611-999',
            'requested_by' => $dev->id,
            'title' => 'Monitor Tambahan',
            'purpose' => 'Kebutuhan coding',
            'estimated_amount' => 2000000,
            'status' => PurchaseRequest::STATUS_PENDING,
        ]);

        // Dev tries to approve own purchase request -> Forbidden (403)
        $response = $this->actingAs($dev)->post(route('purchases.approve', $purchase));
        $response->assertStatus(403);

        // Dev tries to access Reports module -> Forbidden (403)
        $reportResponse = $this->actingAs($dev)->get(route('reports.index'));
        $reportResponse->assertStatus(403);
    }

    public function test_project_creation_with_task_template(): void
    {
        $pm = User::factory()->create(['role' => User::ROLE_CEO]);
        $customer = Customer::create([
            'name' => 'PT Cipta Karya',
            'phone' => '081234567890',
            'type' => 'umkm',
        ]);
        $service = Service::create([
            'name' => 'Custom Web App',
            'category' => 'web_development',
            'base_price' => 2000000,
            'description' => 'Aplikasi web kustom.',
            'estimated_days' => 14,
        ]);
        $order = Order::create([
            'order_code' => 'VSC-ORD-TMP-001',
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'agreed_price' => 2000000,
            'down_payment_required' => 1000000,
            'brief_description' => 'Aplikasi kasir online.',
            'status' => Order::STATUS_CONFIRMED,
            'payment_status' => Order::PAYMENT_PARTIALLY_PAID,
        ]);

        $response = $this->actingAs($pm)->post(route('projects.store'), [
            'order_id' => $order->id,
            'title' => 'Proyek Web App Kasir',
            'project_manager_id' => $pm->id,
            'start_date' => now()->toDateString(),
            'target_completion_date' => now()->addDays(14)->toDateString(),
            'apply_template' => 'web_development',
        ]);

        $response->assertSessionHas('success');

        $project = Project::where('order_id', $order->id)->first();
        $this->assertNotNull($project);
        $this->assertCount(5, $project->tasks);
        $this->assertDatabaseHas('tasks', [
            'project_id' => $project->id,
            'title' => 'Briefing & Sitemap Kebutuhan',
        ]);
    }

    public function test_order_delivery_and_automatic_completion_upon_full_payment(): void
    {
        $pm = User::factory()->create(['role' => User::ROLE_CEO]);
        $finance = User::factory()->create(['role' => User::ROLE_FINANCE]);
        $customer = Customer::create([
            'name' => 'Studio Foto Abadi',
            'phone' => '089988776655',
            'type' => 'umkm',
        ]);
        $service = Service::create([
            'name' => 'Website Portofolio',
            'category' => 'web_development',
            'base_price' => 1000000,
            'description' => 'Portofolio fotografer.',
            'estimated_days' => 7,
        ]);
        $order = Order::create([
            'order_code' => 'VSC-ORD-DLV-001',
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'agreed_price' => 1000000,
            'down_payment_required' => 500000,
            'brief_description' => 'Portofolio wedding photography.',
            'status' => Order::STATUS_IN_PROGRESS,
            'payment_status' => Order::PAYMENT_PARTIALLY_PAID,
        ]);

        // 1. Deliver the project result files (Stage 5)
        $deliveryResponse = $this->actingAs($pm)->post(route('orders.delivery', $order), [
            'delivery_date' => now()->toDateString(),
            'result_file_url' => 'https://drive.google.com/viscode-test-deliverables',
            'delivery_notes' => 'Aset web & credentials hosting diserahkan ke klien.',
        ]);
        $deliveryResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('https://drive.google.com/viscode-test-deliverables', $order->result_file_url);
        // Payment is only partial, so order remains IN_PROGRESS awaiting final payment
        $this->assertEquals(Order::STATUS_IN_PROGRESS, $order->status);

        // 2. Finance records and verifies final payment (Settlement)
        $settlementPayment = Payment::create([
            'payment_code' => 'PAY-SETTLE-001',
            'order_id' => $order->id,
            'amount' => 1000000, // Pays off the total balance
            'payment_type' => 'full',
            'payment_method' => 'QRIS',
            'payment_date' => now(),
            'status' => Payment::STATUS_PENDING,
            'recorded_by' => $finance->id,
        ]);

        $this->actingAs($finance)->post(route('payments.verify', $settlementPayment));

        $order->refresh();
        $this->assertEquals(Order::PAYMENT_PAID, $order->payment_status);
        // Because deliverables are delivered AND payment is 100% PAID, order transitions to COMPLETED automatically!
        $this->assertEquals(Order::STATUS_COMPLETED, $order->status);
    }

    public function test_unified_sales_and_orders_and_non_duplicate_landing_page_intake(): void
    {
        $sales = User::factory()->create(['role' => User::ROLE_SALES]);
        $service = Service::create([
            'name' => 'Paket UMKM Digital',
            'category' => 'bundling',
            'base_price' => 450000,
            'description' => 'Website, logo, dan konten sosial.',
            'estimated_days' => 7,
            'is_active' => true,
        ]);

        // Submit twice from public landing page with same phone -> must NOT duplicate Customer
        $this->post(route('public.lead.store'), [
            'customer_name' => 'Flynn Keith',
            'whatsapp' => '081122334455',
            'email' => 'flynn@example.com',
            'company_name' => 'Keith Studio',
            'service_interest' => 'Paket UMKM Digital (Bundling)',
            'estimated_budget' => 447000,
            'notes' => 'Butuh paket UMKM lengkap.',
        ])->assertSessionHas('public_success');

        $this->post(route('public.lead.store'), [
            'customer_name' => 'Flynn Keith',
            'whatsapp' => '081122334455',
            'service_interest' => 'Website Development',
            'estimated_budget' => 447000,
        ])->assertSessionHas('public_success');

        $this->assertEquals(1, Customer::where('phone', '081122334455')->count());

        $lead = Lead::where('customer_phone', '081122334455')->first();
        $this->assertNotNull($lead);

        // Sales converts Lead to Quotation -> Send -> Accept -> Convert to Order
        $this->actingAs($sales)->post(route('leads.convert-quotation', $lead), [
            'title' => 'Penawaran UMKM Digital Flynn Keith',
            'service_id' => $service->id,
            'scope_description' => 'Landing page + Logo + 3 Konten',
            'total_amount' => 447000,
            'valid_until' => now()->addDays(14)->toDateString(),
        ]);

        $quotation = Quotation::where('lead_id', $lead->id)->first();
        $this->assertNotNull($quotation);

        // Unified Sales & Orders view renders both Quotations and Orders tabs
        $salesOrdersPage = $this->actingAs($sales)->get(route('orders.index'));
        $salesOrdersPage->assertStatus(200);
        $salesOrdersPage->assertSee('Waiting for Response');
        $salesOrdersPage->assertSee($quotation->quotation_code);

        // Send -> Accept -> Convert to Order (Reusing customer, service, price)
        $this->actingAs($sales)->post(route('quotations.send', $quotation));
        $this->actingAs($sales)->post(route('quotations.accept', $quotation));
        $this->actingAs($sales)->post(route('quotations.convert-order', $quotation));

        $this->assertDatabaseHas('orders', [
            'customer_id' => $quotation->customer_id,
            'quotation_id' => $quotation->id,
            'service_id' => $service->id,
            'agreed_price' => 447000,
            'status' => Order::STATUS_PENDING_PAYMENT,
        ]);
    }

    public function test_sales_pipeline_full_workflow_new_to_reviewed_to_follow_up_to_quotation_sent(): void
    {
        $sales = User::factory()->create(['role' => User::ROLE_SALES]);
        $dev = User::factory()->create(['role' => User::ROLE_WEB_DEVELOPER]);

        $service = Service::create([
            'name' => 'Website Profil / UMKM',
            'category' => 'web_development',
            'base_price' => 350000,
            'description' => 'Multi-halaman responsif.',
            'estimated_days' => 7,
            'is_active' => true,
        ]);

        // 1. Public website lead submission -> status NEW
        $this->post(route('public.lead.store'), [
            'customer_name' => 'Andi Pratama',
            'whatsapp' => '081299887766',
            'email' => 'andi@umkm.id',
            'company_name' => 'Kopi Nusantara',
            'service_interest' => 'Website Profil / UMKM',
            'estimated_budget' => 450000,
            'notes' => 'Butuh katalog produk kopi dan tombol order WA.',
        ])->assertSessionHas('public_success');

        $lead = Lead::where('customer_phone', '081299887766')->first();
        $this->assertNotNull($lead);
        $this->assertEquals(Lead::STATUS_NEW, $lead->status);

        // Unauthorized role (developer) cannot review lead -> 403
        $this->actingAs($dev)->post(route('leads.review', $lead))->assertStatus(403);

        // 2. Sales reviews the NEW lead -> status becomes REVIEWED
        $this->actingAs($sales)->post(route('leads.review', $lead), [
            'service_id' => $service->id,
            'estimated_value' => 450000,
            'assigned_to' => $sales->id,
            'review_notes' => 'Data lengkap, kebutuhan jelas untuk UMKM Kopi.',
        ])->assertSessionHas('success');

        $lead->refresh();
        $this->assertEquals(Lead::STATUS_REVIEWED, $lead->status);
        $this->assertNotNull($lead->reviewed_at);
        $this->assertEquals($sales->id, $lead->reviewed_by);

        $this->assertDatabaseHas('activity_logs', [
            'module' => 'Leads',
            'record_id' => $lead->lead_code,
            'activity' => 'STATUS_CHANGE',
        ]);

        // 3. Sales logs Follow-up on REVIEWED lead -> status becomes FOLLOW_UP
        $this->actingAs($sales)->post(route('leads.communication', $lead), [
            'contact_date' => now()->toDateString(),
            'follow_up_result' => 'Tertarik & Siap Diberikan Penawaran',
            'notes' => 'Klien setuju dengan estimasi fitur dan minta dikirimkan penawaran resmi.',
            'next_follow_up_date' => now()->addDays(3)->toDateString(),
        ])->assertSessionHas('success');

        $lead->refresh();
        $this->assertEquals(Lead::STATUS_FOLLOW_UP, $lead->status);
        $this->assertEquals('Tertarik & Siap Diberikan Penawaran', $lead->follow_up_result);

        // 4. Sales creates & sends Quotation (with price & discount) -> status becomes QUOTATION_SENT
        $this->actingAs($sales)->post(route('leads.convert-quotation', $lead), [
            'title' => 'Penawaran Website Kopi Nusantara',
            'service_id' => $service->id,
            'scope_description' => 'Desain UI/UX, Katalog Produk, Integrasi WhatsApp',
            'price' => 500000,
            'discount_amount' => 50000,
            'estimated_deadline' => now()->addDays(10)->toDateString(),
            'valid_until' => now()->addDays(14)->toDateString(),
            'notes' => 'Diskon khusus UMKM 10%',
            'submit_action' => 'send',
        ])->assertSessionHas('success');

        $lead->refresh();
        $this->assertEquals(Lead::STATUS_QUOTATION_SENT, $lead->status);

        $quotation = Quotation::where('lead_id', $lead->id)->first();
        $this->assertNotNull($quotation);
        $this->assertEquals(Quotation::STATUS_SENT, $quotation->status);
        $this->assertEquals(500000, (float) $quotation->price);
        $this->assertEquals(50000, (float) $quotation->discount_amount);
        $this->assertEquals(450000, (float) $quotation->total_amount);

        // 5. Attempting to create a duplicate quotation for the same lead is blocked
        $this->actingAs($sales)->post(route('leads.convert-quotation', $lead), [
            'title' => 'Penawaran Duplikat',
            'service_id' => $service->id,
            'scope_description' => 'Duplikat',
            'price' => 500000,
            'valid_until' => now()->addDays(14)->toDateString(),
            'submit_action' => 'send',
        ])->assertSessionHasErrors('quotation_error');

        $this->assertEquals(1, Quotation::where('lead_id', $lead->id)->count());
    }

    public function test_super_admin_can_render_all_views_with_override_modals(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        $routes = [
            'dashboard',
            'customers.index',
            'leads.index',
            'quotations.index',
            'orders.index',
            'payments.index',
            'projects.index',
            'purchases.index',
            'reports.index',
            'documentations.index',
            'transactions.index',
            'activity-logs.index',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($superAdmin)->get(route($route));
            $response->assertStatus(200);
        }
    }
}
