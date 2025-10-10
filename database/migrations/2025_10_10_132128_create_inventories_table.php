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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->timestamp('date_arrived')->nullable()->index('inventory_date_arrived');
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade')->index('inventory_category_id');
            $table->foreignId('supplier_id')->nullable()->constrained()->onDelete('cascade')->index('inventory_supplier_id');
            $table->string('box_number')->nullable()->index('inventory_box_number');
            $table->integer('quantity')->default(0)->nullable()->index("inventory_quantity");
            $table->integer('amount')->default(0)->nullable()->index("inventory_amount");
            $table->integer('total')->nullable()->index("inventory_total");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
