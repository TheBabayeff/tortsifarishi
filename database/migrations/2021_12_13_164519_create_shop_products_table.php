<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shop_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('shop_categories')->cascadeOnDelete();
            $table->string('slug')->unique()->nullable();
            $table->foreignId('product_reasons')->nullable()->constrained('reasons')->cascadeOnDelete();
            $table->foreignId('product_compositions')->nullable()->constrained('product_compositions')->cascadeOnDelete();
            $table->string('preview_image')->nullable();
            $table->string('product_images')->nullable();
            $table->string('original_filename')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_visible')->default(false);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->date('published_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_products');
    }
};
