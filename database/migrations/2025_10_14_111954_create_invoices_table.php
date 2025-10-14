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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('start_date')->index('invoice_start_date');
            $table->date('end_date')->index('invoice_end_date');
            $table->double('total_days')->default(0);
            $table->double('total_hours', 10, 2)->default(0);
            $table->double('total_daily_pay', 10, 2)->default(0);
            $table->double('total_overtime_pay', 10, 2)->default(0);
            $table->text('commission_descriptions')->nullable()->index('invoice_commission_descriptions');
            $table->double('commission_quantity')->default(0);
            $table->double('total_commission', 10, 2)->default(0);
            $table->text('deduction_descriptions')->nullable()->index('invoice_deduction_descriptions');
            $table->double('total_deduction', 10, 2)->default(0);
            $table->double('gross_pay', 10, 2)->default(0);
            $table->double('net_pay', 10, 2)->default(0);


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
