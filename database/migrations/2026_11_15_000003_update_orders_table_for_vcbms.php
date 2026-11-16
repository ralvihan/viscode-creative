<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status', 30)->default('PENDING_PAYMENT')->change();
            $table->enum('payment_status', ['UNPAID', 'PARTIALLY_PAID', 'PAID'])->default('UNPAID')->after('status');
            $table->decimal('down_payment_required', 12, 2)->default(0)->after('agreed_price');
            $table->foreignId('quotation_id')->nullable()->after('customer_id')->constrained('quotations')->nullOnDelete();
            $table->text('cancellation_reason')->nullable()->after('revision_notes');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['quotation_id']);
            $table->dropColumn(['payment_status', 'down_payment_required', 'quotation_id', 'cancellation_reason']);
        });
    }
};
