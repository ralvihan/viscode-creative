<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_code')->unique();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('project_manager_id')->constrained('users')->restrictOnDelete();
            $table->date('start_date')->nullable();
            $table->date('target_completion_date')->nullable();
            $table->string('deliverable_url')->nullable();
            $table->enum('status', ['NOT_STARTED', 'IN_PROGRESS', 'REVIEW', 'REVISION', 'COMPLETED', 'CANCELLED'])->default('NOT_STARTED');
            $table->text('qc_notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
