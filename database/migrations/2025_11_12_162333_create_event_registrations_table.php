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
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('event_id');
            $table->string('role'); // jury, reviewer, participant
            $table->string('status')->default('pending'); // pending, approved, rejected
            
            // Essential contact information
            $table->string('phone')->nullable();
            $table->string('organization')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            
            // Certificate for jury role
            $table->string('certificate_path')->nullable(); // for jury applications
            $table->string('certificate_filename')->nullable();
            
            // Notes
            $table->text('application_notes')->nullable();
            $table->text('admin_notes')->nullable();
            
            // Approval tracking
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejected_reason')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('event_id')->references('id')->on('events')->onDelete('cascade');
            $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('user_id');
            $table->index('event_id');
            $table->index('status');
            $table->index('role');

            // Unique constraint - user can only register once per event
            $table->unique(['user_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_registrations');
    }
};
