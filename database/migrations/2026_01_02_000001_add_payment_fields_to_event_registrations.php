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
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->string('payment_receipt_path')->nullable()->after('admin_notes');
            $table->enum('payment_status', ['pending', 'approved', 'rejected'])->nullable()->after('payment_receipt_path');
            $table->timestamp('payment_submitted_at')->nullable()->after('payment_status');
            $table->timestamp('payment_approved_at')->nullable()->after('payment_submitted_at');
            $table->text('payment_notes')->nullable()->after('payment_approved_at')->comment('EO notes for payment approval/rejection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'payment_receipt_path',
                'payment_status',
                'payment_submitted_at',
                'payment_approved_at',
                'payment_notes'
            ]);
        });
    }
};
