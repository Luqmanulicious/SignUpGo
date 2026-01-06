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
        Schema::create('paper_review_category_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paper_review_id')->constrained('paper_reviews')->onDelete('cascade');
            $table->foreignId('rubric_category_id')->constrained('rubric_categories')->onDelete('cascade');
            $table->text('comment')->nullable();
            $table->timestamps();
            
            // One comment per category per review
            $table->unique(['paper_review_id', 'rubric_category_id'], 'paper_review_category_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_review_category_comments');
    }
};
