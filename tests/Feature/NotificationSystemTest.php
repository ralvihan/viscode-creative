<?php

namespace Tests\Feature;

use App\Models\SystemNotification;
use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_activity_logger_dispatches_categorized_notifications_and_topbar_hides_public_website_button(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $sales = User::factory()->create(['role' => User::ROLE_SALES]);

        // Log a new order creation -> should notify Super Admin and Sales
        ActivityLogger::log('Orders', 'ORD-2026-0001', 'CREATE_ORDER', null, ['status' => 'PENDING_PAYMENT'], 'Pesanan baru dari PT Maju', $sales->id);

        // Log a master service change -> should notify Super Admin only (not Sales)
        ActivityLogger::log('MasterServices', '1', 'UPDATE', null, null, 'Update harga dasar layanan', $superAdmin->id);

        $this->assertEquals(2, SystemNotification::where('user_id', $superAdmin->id)->unread()->count());
        $this->assertEquals(1, SystemNotification::where('user_id', $sales->id)->unread()->count());

        $orderNotif = SystemNotification::where('user_id', $superAdmin->id)
            ->where('module', 'Orders')
            ->firstOrFail();

        $this->assertEquals(SystemNotification::CATEGORY_PESANAN_BARU, $orderNotif->category);
        $this->assertEquals(route('orders.index'), $orderNotif->action_url);

        $masterNotif = SystemNotification::where('user_id', $superAdmin->id)
            ->where('module', 'MasterServices')
            ->firstOrFail();

        $this->assertEquals(SystemNotification::CATEGORY_PERUBAHAN_DATA, $masterNotif->category);

        // Verify topbar renders Notification Center and does not render Website Publik link
        $response = $this->actingAs($superAdmin)->get(route('dashboard'));
        $response->assertOk();
        $response->assertSee('Notifikasi Aktivitas Sistem');
        $response->assertDontSee('Website Publik');
    }

    public function test_user_can_read_visit_delete_and_clear_notifications_with_unread_count_decreasing(): void
    {
        $superAdmin = User::factory()->create(['role' => User::ROLE_SUPER_ADMIN]);
        $otherUser = User::factory()->create(['role' => User::ROLE_SALES]);

        ActivityLogger::log('Orders', 'ORD-1', 'CREATE', null, null, 'Pesanan baru #1', $superAdmin->id);
        ActivityLogger::log('Leads', 'LD-1', 'PUBLIC_SUBMISSION', null, null, 'Lead baru website', null);
        ActivityLogger::log('Payments', 'PAY-1', 'VERIFY', null, null, 'Pembayaran diverifikasi', $superAdmin->id);

        $notifications = SystemNotification::where('user_id', $superAdmin->id)->orderBy('id')->get();
        $this->assertCount(3, $notifications);

        // 1. Mark first notification as read via JSON -> unread drops from 3 to 2
        $this->actingAs($superAdmin)
            ->postJson(route('notifications.read', $notifications[0]))
            ->assertOk()
            ->assertJson(['unread_count' => 2]);

        $this->assertNotNull($notifications[0]->fresh()->read_at);

        // 2. Visit second notification -> marks read (unread drops from 2 to 1) & redirects to target URL
        $this->actingAs($superAdmin)
            ->get(route('notifications.visit', $notifications[1]))
            ->assertRedirect(route('leads.index'));

        $this->assertNotNull($notifications[1]->fresh()->read_at);
        $this->assertEquals(1, SystemNotification::where('user_id', $superAdmin->id)->unread()->count());

        // 3. Delete third (unread) notification -> unread drops from 1 to 0 and record is removed
        $this->actingAs($superAdmin)
            ->deleteJson(route('notifications.destroy', $notifications[2]))
            ->assertOk()
            ->assertJson(['unread_count' => 0]);

        $this->assertDatabaseMissing('system_notifications', ['id' => $notifications[2]->id]);

        // 4. Another user cannot read, visit, or delete superAdmin's notification
        $this->actingAs($otherUser)
            ->postJson(route('notifications.read', $notifications[0]))
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->get(route('notifications.visit', $notifications[0]))
            ->assertForbidden();

        $this->actingAs($otherUser)
            ->deleteJson(route('notifications.destroy', $notifications[0]))
            ->assertForbidden();

        // 5. Clear all remaining notifications
        $this->actingAs($superAdmin)
            ->deleteJson(route('notifications.clear-all'))
            ->assertOk()
            ->assertJson(['unread_count' => 0]);

        $this->assertEquals(0, SystemNotification::where('user_id', $superAdmin->id)->count());
    }
}
