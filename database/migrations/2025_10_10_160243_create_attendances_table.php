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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('date')->required()->index('attendance_date');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->index('attendance_user_id');
            $table->time('time_in')->required()->index('attendance_time_in');
            $table->time('time_out')->required()->index('attendance_time_out');
            $table->enum('work_shift_status', ['Whole Day', 'Half Day','Overtime','Absent'])->index('attendance_work_shift_status');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
