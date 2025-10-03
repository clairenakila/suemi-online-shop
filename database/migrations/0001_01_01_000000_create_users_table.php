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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->index("user_name");
            $table->string('email')->unique()->index("user_email");
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
            $table->string('contact_number')->nullable()->index("user_contact_number");
            $table->string('sss_number')->nullable()->index("user_sss_number");
            $table->string('pagibig_number')->nullable()->index("user_pagibig_number");
            $table->string('philhealth_number')->nullable()->index("user_philhealth_number");
            $table->integer('hourly_rate')->nullable()->index("user_hourly_rate");
            $table->integer('daily_rate')->nullable()->index("user_daily_rate");
            $table->string('signature')->nullable()->index("user_signature");

            




        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
