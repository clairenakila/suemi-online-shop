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
        Schema::table('items', function (Blueprint $table) {
            $table->double('capital', 15, 2)->change();
            $table->double('selling_price', 15, 2)->change();
            $table->double('shoppee_commission', 15, 2)->change();
            $table->double('total_gross_sale', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->integer('shoppee_commission')->change();
            $table->integer('total_gross_sale')->change();
            $table->integer('selling_price')->change();
            $table->integer('capital')->change();
        });
    }
};
