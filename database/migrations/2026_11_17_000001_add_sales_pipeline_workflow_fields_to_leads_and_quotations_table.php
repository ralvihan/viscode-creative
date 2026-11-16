<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('status', 40)->default('NEW')->change();
            $table->timestamp('reviewed_at')->nullable()->after('status');
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')->constrained('users')->nullOnDelete();
            $table->text('review_notes')->nullable()->after('reviewed_by');
            $table->date('last_contact_date')->nullable()->after('communication_notes');
            $table->string('follow_up_result')->nullable()->after('last_contact_date');
            $table->date('next_follow_up_date')->nullable()->after('follow_up_result');
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->nullable()->after('scope_description');
            $table->decimal('discount_amount', 12, 2)->default(0)->after('price');
            $table->date('estimated_deadline')->nullable()->after('valid_until');
            $table->text('notes')->nullable()->after('rejection_reason');
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn([
                'reviewed_at',
                'review_notes',
                'last_contact_date',
                'follow_up_result',
                'next_follow_up_date',
            ]);
        });

        Schema::table('quotations', function (Blueprint $table) {
            $table->dropColumn([
                'price',
                'discount_amount',
                'estimated_deadline',
                'notes',
            ]);
        });
    }
};
