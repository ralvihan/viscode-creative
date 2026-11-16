<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->after('order_id')->constrained('payments')->nullOnDelete();
            $table->foreignId('purchase_request_id')->nullable()->after('payment_id')->constrained('purchase_requests')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropForeign(['purchase_request_id']);
            $table->dropColumn(['payment_id', 'purchase_request_id']);
        });
    }
};
