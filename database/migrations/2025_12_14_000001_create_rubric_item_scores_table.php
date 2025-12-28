<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rubric_item_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jury_mapping_id')->constrained('jury_mappings')->onDelete('cascade');
            $table->foreignId('rubric_item_id')->constrained('rubric_items')->onDelete('cascade');
            $table->foreignId('event_paper_id')->nullable()->constrained('event_papers')->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->integer('score'); // The actual score (1-5)
            $table->text('comment')->nullable();
            $table->timestamps();
            
            // Prevent duplicate scores for same evaluator + rubric item + paper
            $table->unique(['jury_mapping_id', 'rubric_item_id'], 'unique_rubric_score');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rubric_item_scores');
    }
};
