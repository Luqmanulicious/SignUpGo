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
        Schema::table('event_papers', function (Blueprint $table) {
            // Add product_theme column
            $table->string('product_theme')->nullable()->after('paper_category');
            
            // Rename paper_category to product_category
            $table->renameColumn('paper_category', 'product_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_papers', function (Blueprint $table) {
            // Rename back to paper_category
            $table->renameColumn('product_category', 'paper_category');
            
            // Drop product_theme column
            $table->dropColumn('product_theme');
        });
    }
};
