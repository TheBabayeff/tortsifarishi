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
        Schema::create('internal_orders', function (Blueprint $table) {
            $table->id()->startingValue(1000);
            $table->foreignId('shop_customer_id')->nullable()->constrained()->nullOnDelete();
            $table->double('total')->default(0);
            $table->double('beh')->nullable();
            $table->enum('status', ['new', 'processing', 'shipped', 'delivered', 'cancelled'])->default('new');
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->timestamps();
            $table->softDeletes();
            //$table->foreign('customer_id')->references('id')->on('shop_customers');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('internal_orders');
    }
};
