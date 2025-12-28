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
            // Drop the old unique constraint that only includes user_id and event_id
            $table->dropUnique(['user_id', 'event_id']);
            
            // Add new unique constraint that includes role
            // This allows the same user to register as different roles for the same event
            $table->unique(['user_id', 'event_id', 'role'], 'event_registrations_user_event_role_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            // Drop the new unique constraint
            $table->dropUnique('event_registrations_user_event_role_unique');
            
            // Restore the old unique constraint
            $table->unique(['user_id', 'event_id'], 'event_registrations_user_id_event_id_unique');
        });
    }
};
