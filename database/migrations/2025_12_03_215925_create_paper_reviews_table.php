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
        Schema::create('paper_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_paper_id')->constrained()->onDelete('cascade');
            $table->foreignId('reviewer_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'selected', 'rejected'])->default('pending');
            $table->integer('overall_score')->nullable();
            $table->text('overall_comment')->nullable();
            $table->timestamps();
            
            // One review per reviewer per paper
            $table->unique(['event_paper_id', 'reviewer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_reviews');
    }
};
