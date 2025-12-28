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
        Schema::create('feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_registration_id')->constrained('event_registrations')->onDelete('cascade');
            
            // Rating fields (1-5 stars)
            $table->unsignedTinyInteger('overall_rating');
            $table->unsignedTinyInteger('content_rating');
            $table->unsignedTinyInteger('organization_rating');
            $table->unsignedTinyInteger('platform_rating')->nullable();
            $table->unsignedTinyInteger('venue_rating')->nullable();
            
            // Text feedback
            $table->text('comments')->nullable();
            $table->text('suggestions')->nullable();
            $table->text('system_feedback')->nullable();
            
            // Recommendation
            $table->boolean('would_recommend');
            
            // Submission timestamp
            $table->timestamp('submitted_at')->useCurrent();
            
            $table->timestamps();
            
            // Indexes
            $table->index('event_registration_id');
            $table->index('overall_rating');
            $table->index('would_recommend');
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedbacks');
    }
};
