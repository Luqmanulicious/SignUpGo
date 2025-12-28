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
        Schema::create('paper_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_paper_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('evaluation_rubric_id')->constrained()->onDelete('cascade');
            $table->integer('score');
            $table->text('comment')->nullable();
            $table->timestamps();
            
            // Prevent duplicate evaluations for same paper+evaluator+rubric
            $table->unique(['event_paper_id', 'evaluator_id', 'evaluation_rubric_id'], 'unique_paper_evaluator_rubric');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paper_evaluations');
    }
};
