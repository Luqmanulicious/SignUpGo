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
        Schema::create('event_papers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('paper_category_id')->nullable()->constrained()->onDelete('set null');
            $table->string('title');
            $table->text('abstract');
            $table->string('poster_path')->nullable();
            $table->text('video_url')->nullable();
            $table->enum('status', ['draft', 'submitted', 'under_review', 'approved', 'rejected'])->default('draft');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_papers');
    }
};
