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
            if (!Schema::hasColumn('event_registrations', 'registration_code')) {
                $table->string('registration_code')->nullable()->after('event_id');
            }
            if (!Schema::hasColumn('event_registrations', 'qr_code')) {
                $table->string('qr_code')->nullable()->after('rejected_reason');
            }
            if (!Schema::hasColumn('event_registrations', 'qr_image_path')) {
                $table->string('qr_image_path')->nullable()->after('qr_code');
            }
            if (!Schema::hasColumn('event_registrations', 'checked_in_at')) {
                $table->timestamp('checked_in_at')->nullable()->after('qr_image_path');
            }
            if (!Schema::hasColumn('event_registrations', 'check_in_method')) {
                $table->string('check_in_method')->nullable()->after('checked_in_at');
            }
            if (!Schema::hasColumn('event_registrations', 'attendance_mode')) {
                $table->string('attendance_mode')->nullable()->after('check_in_method');
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
                'registration_code',
                'qr_code',
                'qr_image_path',
                'checked_in_at',
                'check_in_method',
                'attendance_mode'
            ]);
        });
    }
};
