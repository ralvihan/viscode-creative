<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_code')->unique();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->string('title');
            $table->text('purpose');
            $table->decimal('estimated_amount', 12, 2);
            $table->decimal('actual_amount', 12, 2)->nullable();
            $table->string('receipt_proof')->nullable();
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED', 'PURCHASED', 'COMPLETED', 'CANCELLED'])->default('PENDING');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('approval_notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
