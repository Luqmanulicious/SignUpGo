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
        // Drop the incorrect foreign key and recreate with correct reference
        Schema::table('paper_review_scores', function (Blueprint $table) {
            $table->dropForeign(['rubric_item_id']);
        });
        
        Schema::table('paper_review_scores', function (Blueprint $table) {
            $table->foreign('rubric_item_id')->references('id')->on('rubric_items')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('paper_review_scores', function (Blueprint $table) {
            $table->dropForeign(['rubric_item_id']);
        });
        
        Schema::table('paper_review_scores', function (Blueprint $table) {
            $table->foreign('rubric_item_id')->references('id')->on('evaluation_rubrics')->onDelete('cascade');
        });
    }
};
