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
            // Add certificate columns
            if (!Schema::hasColumn('event_registrations', 'certificate_path')) {
                $table->string('certificate_path')->nullable()->after('emergency_contact_phone');
            }
            if (!Schema::hasColumn('event_registrations', 'certificate_filename')) {
                $table->string('certificate_filename')->nullable()->after('certificate_path');
            }
            
            // Add application/admin columns
            if (!Schema::hasColumn('event_registrations', 'application_notes')) {
                $table->text('application_notes')->nullable()->after('certificate_filename');
            }
            if (!Schema::hasColumn('event_registrations', 'admin_notes')) {
                $table->text('admin_notes')->nullable()->after('application_notes');
            }
            if (!Schema::hasColumn('event_registrations', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('admin_notes');
            }
            if (!Schema::hasColumn('event_registrations', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('approved_at');
            }
            if (!Schema::hasColumn('event_registrations', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('approved_by');
            }
            if (!Schema::hasColumn('event_registrations', 'rejected_reason')) {
                $table->text('rejected_reason')->nullable()->after('rejected_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            $table->dropColumn([
                'certificate_path',
                'certificate_filename',
                'application_notes',
                'admin_notes',
                'approved_at',
                'approved_by',
                'rejected_at',
                'rejected_reason'
            ]);
        });
    }
};
