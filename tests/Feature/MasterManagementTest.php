<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_and_ceo_can_access_master_management_while_sales_is_forbidden(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $ceo = User::factory()->create(['role' => User::ROLE_CEO]);
        $sales = User::factory()->create(['role' => User::ROLE_SALES]);

        $this->actingAs($superAdmin)->get(route('master.index'))
            ->assertOk()
            ->assertSee('Master Data &amp; User Management', false);

        $this->actingAs($ceo)->get(route('master.index'))
            ->assertOk();

        $this->actingAs($sales)->get(route('master.index'))
            ->assertForbidden();
    }

    public function test_super_admin_can_crud_users_and_cannot_delete_self_or_last_super_admin(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        // Create user
        $this->actingAs($superAdmin)->post(route('master.users.store'), [
            'name' => 'Raka Developer',
            'email' => 'raka@viscode.id',
            'role' => User::ROLE_WEB_DEVELOPER,
            'password' => 'viscode2026',
            'password_confirmation' => 'viscode2026',
        ])->assertRedirect(route('master.index', ['tab' => 'users']));

        $created = User::where('email', 'raka@viscode.id')->firstOrFail();
        $this->assertEquals(User::ROLE_WEB_DEVELOPER, $created->role);

        // Update user role
        $this->actingAs($superAdmin)->put(route('master.users.update', $created), [
            'name' => 'Raka Lead Dev',
            'email' => 'raka@viscode.id',
            'role' => User::ROLE_QC_ADMIN,
        ])->assertRedirect(route('master.index', ['tab' => 'users']));

        $this->assertDatabaseHas('users', [
            'id' => $created->id,
            'name' => 'Raka Lead Dev',
            'role' => User::ROLE_QC_ADMIN,
        ]);

        // Prevent demoting sole super admin
        $this->actingAs($superAdmin)->put(route('master.users.update', $superAdmin), [
            'name' => $superAdmin->name,
            'email' => $superAdmin->email,
            'role' => User::ROLE_CEO,
        ])->assertSessionHas('error');

        $this->assertEquals(User::ROLE_SUPER_ADMIN, $superAdmin->fresh()->role);

        // Prevent self deletion
        $this->actingAs($superAdmin)->delete(route('master.users.destroy', $superAdmin))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $superAdmin->id]);

        // Delete created user
        $this->actingAs($superAdmin)->delete(route('master.users.destroy', $created))
            ->assertRedirect(route('master.index', ['tab' => 'users']));

        $this->assertDatabaseMissing('users', ['id' => $created->id]);
    }

    public function test_ceo_cannot_assign_super_admin_role(): void
    {
        $ceo = User::factory()->create(['role' => User::ROLE_CEO]);

        $this->actingAs($ceo)->post(route('master.users.store'), [
            'name' => 'Unauthorized Super',
            'email' => 'unauth@viscode.id',
            'role' => User::ROLE_SUPER_ADMIN,
            'password' => 'viscode2026',
            'password_confirmation' => 'viscode2026',
        ])->assertSessionHas('error');

        $this->assertDatabaseMissing('users', ['email' => 'unauth@viscode.id']);
    }

    public function test_master_services_crud_toggle_and_safe_delete_protection(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);

        // Create service
        $this->actingAs($superAdmin)->post(route('master.services.store'), [
            'name' => 'Website Company Profile Pro',
            'category' => 'web_development',
            'base_price' => 1250000,
            'description' => 'Website profesional multi-halaman dengan CMS.',
            'is_active' => 1,
        ])->assertRedirect(route('master.index', ['tab' => 'services']));

        $service = Service::where('name', 'Website Company Profile Pro')->firstOrFail();
        $this->assertEquals(1250000, (float) $service->base_price);
        $this->assertTrue($service->is_active);

        // Update service
        $this->actingAs($superAdmin)->put(route('master.services.update', $service), [
            'name' => 'Website Company Profile Enterprise',
            'category' => 'bundling',
            'base_price' => 1500000,
            'description' => 'Termasuk hosting & maintenance.',
            'is_active' => 1,
        ])->assertRedirect(route('master.index', ['tab' => 'services']));

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Website Company Profile Enterprise',
            'category' => 'bundling',
            'base_price' => 1500000,
        ]);

        // Toggle status
        $this->actingAs($superAdmin)->post(route('master.services.toggle', $service))
            ->assertRedirect(route('master.index', ['tab' => 'services']));
        $this->assertFalse($service->fresh()->is_active);

        // Link to an Order -> deletion must be blocked
        $customer = Customer::create([
            'name' => 'PT Contoh Klien',
            'phone' => '081234567899',
            'type' => 'umkm',
        ]);

        Order::create([
            'order_code' => 'ORD-TEST-001',
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'brief_description' => 'Testing order reference',
            'agreed_price' => 1500000,
            'down_payment_required' => 750000,
            'status' => Order::STATUS_PENDING_PAYMENT,
            'payment_status' => Order::PAYMENT_UNPAID,
        ]);

        $this->actingAs($superAdmin)->delete(route('master.services.destroy', $service))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('services', ['id' => $service->id]);

        // Unused service can be deleted cleanly
        $unusedService = Service::create([
            'name' => 'Desain Sementara',
            'category' => 'graphic_design',
            'base_price' => 50000,
            'is_active' => true,
        ]);

        $this->actingAs($superAdmin)->delete(route('master.services.destroy', $unusedService))
            ->assertRedirect(route('master.index', ['tab' => 'services']));
        $this->assertDatabaseMissing('services', ['id' => $unusedService->id]);

        // Customer with order cannot be deleted, unused customer can
        $this->actingAs($superAdmin)->delete(route('master.customers.destroy', $customer))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('customers', ['id' => $customer->id]);

        $unusedCustomer = Customer::create([
            'name' => 'Klien Tanpa Transaksi',
            'phone' => '089999999999',
            'type' => 'individu',
        ]);

        $this->actingAs($superAdmin)->delete(route('master.customers.destroy', $unusedCustomer))
            ->assertRedirect(route('master.index', ['tab' => 'customers']));
        $this->assertDatabaseMissing('customers', ['id' => $unusedCustomer->id]);
    }
}
