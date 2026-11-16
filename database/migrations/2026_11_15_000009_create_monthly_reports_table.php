<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->string('report_code')->unique();
            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');
            $table->decimal('total_income', 14, 2)->default(0);
            $table->decimal('total_expense', 14, 2)->default(0);
            $table->decimal('net_profit', 14, 2)->default(0);
            $table->unsignedInteger('total_orders_in')->default(0);
            $table->unsignedInteger('total_orders_completed')->default(0);
            $table->unsignedInteger('total_new_customers')->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['DRAFT', 'UNDER_REVIEW', 'APPROVED', 'FINALIZED'])->default('DRAFT');
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finalized_at')->nullable();
            $table->timestamps();

            $table->unique(['month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
