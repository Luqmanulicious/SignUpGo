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
            // Add paper_path for conference Word/PDF documents
            $table->string('paper_path')->nullable()->after('poster_path');
            
            // Add paper_theme for conference themes
            $table->string('paper_theme')->nullable()->after('product_theme');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_papers', function (Blueprint $table) {
            $table->dropColumn(['paper_path', 'paper_theme']);
        });
    }
};
