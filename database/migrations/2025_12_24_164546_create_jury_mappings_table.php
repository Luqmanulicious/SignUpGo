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
        Schema::create('jury_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jury_registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->foreignId('participant_registration_id')->constrained('event_registrations')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('status')->default('pending'); // pending, accepted, declined
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Ensure unique assignment - same jury can't be assigned to same participant twice
            $table->unique(['jury_registration_id', 'participant_registration_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jury_mappings');
    }
};
