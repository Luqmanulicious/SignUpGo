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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('job_title')->nullable()->after('phone');
            $table->string('organization')->nullable()->after('job_title');
            $table->string('certificate_path')->nullable()->after('organization');
            $table->text('address')->nullable()->after('certificate_path');
            $table->string('postcode')->nullable()->after('address');
            $table->string('website')->nullable()->after('postcode');
            $table->string('resume_path')->nullable()->after('website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'job_title',
                'organization',
                'certificate_path',
                'address',
                'postcode',
                'website',
                'resume_path'
            ]);
        });
    }
};
