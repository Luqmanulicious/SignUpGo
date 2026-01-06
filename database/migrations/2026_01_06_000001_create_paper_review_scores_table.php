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
        Schema::create('paper_review_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_review_id')->constrained('paper_reviews')->onDelete('cascade');
            $table->foreignId('rubric_item_id')->constrained('evaluation_rubrics')->onDelete('cascade');
            $table->integer('score'); // The actual score given (0 to max_score)
            $table->timestamps();
            
            // One score per rubric item per review
            $table->unique(['paper_review_id', 'rubric_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_review_scores');
    }
};
