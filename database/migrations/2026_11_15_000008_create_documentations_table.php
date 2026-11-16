<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documentations', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('category', ['project_deliverable', 'contract', 'sop', 'asset', 'presentation', 'other'])->default('project_deliverable');
            $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            $table->string('file_url')->nullable();
            $table->string('file_path')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentations');
    }
};
