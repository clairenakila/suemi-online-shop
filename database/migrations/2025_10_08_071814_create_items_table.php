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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('brand')->index("item_brand");
            $table->string('order_id')->index("item_order_id");
            $table->foreignId('category_id')->nullable()->constrained()->onDelete('cascade')->index('item_category_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade')->index('item_user_id');
            $table->integer('quantity')->nullable()->index("item_quantity");
            $table->integer('capital')->nullable()->index("item_capital");
            $table->integer('selling_price')->nullable()->index("item_selling_price");
            $table->enum('is_returned', ['Yes', 'No'])->default('No')->index('item_is_returned');
            $table->timestamp('date_returned')->nullable()->index('item_date_returned');
            $table->timestamp('date_shipped')->nullable()->index('item_date_shipped');
            $table->string('live_seller')->nullable()->index('item_live_seller');
            $table->integer('shoppee_commission')->nullable()->index("item_shoppee_commission");
            $table->integer('total_gross_sale')->nullable()->index("item_total_gross_sale");






        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
