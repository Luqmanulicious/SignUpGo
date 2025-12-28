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
            // Add new unique constraint (user_id + event_id + role)
            // This allows same user to register for same event with different roles
            if (!$this->constraintExists('unique_user_event_role')) {
                $table->unique(['user_id', 'event_id', 'role'], 'unique_user_event_role');
            }
        });
    }

    private function constraintExists($constraintName)
    {
        $exists = \DB::select("SELECT constraint_name FROM information_schema.table_constraints WHERE constraint_name = ? AND table_name = 'event_registrations'", [$constraintName]);
        return count($exists) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_registrations', function (Blueprint $table) {
            // Drop the three-column unique constraint
            $table->dropUnique('unique_user_event_role');
            
            // Restore old two-column unique constraint
            $table->unique(['user_id', 'event_id'], 'unique_event_user_registration');
        });
    }
};
