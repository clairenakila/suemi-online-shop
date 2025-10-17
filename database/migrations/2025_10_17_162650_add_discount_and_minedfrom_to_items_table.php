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
            $table->double('discount')->default(0)->nullable()->index("item_discount");
            $table->enum('mined_from', ['Shoppee', 'Facebook'])->default('Shoppee')->index('item_mined_from');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
             $table->double('discount');
              $table->enum('mined_from');
        });
    }
};
