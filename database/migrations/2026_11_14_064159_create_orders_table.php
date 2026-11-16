<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_code')->unique();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->restrictOnDelete();
            $table->foreignId('pic_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('brief_description');
            $table->decimal('agreed_price', 12, 2);
            $table->date('deadline')->nullable();
            $table->enum('status', [
                'order_masuk',
                'menunggu_konfirmasi',
                'menunggu_dp',
                'dalam_pengerjaan',
                'review_revisi',
                'menunggu_pelunasan',
                'selesai',
                'dibatalkan',
            ])->default('order_masuk');
            $table->string('result_file_url')->nullable();
            $table->text('revision_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
