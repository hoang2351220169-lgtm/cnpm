<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            // Danh mục sản phẩm
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->onDelete('cascade');

            // Thông tin sản phẩm
            $table->string('name');
            $table->text('description')->nullable();

            // Giá bán
            $table->decimal('price', 12, 2);

            // Hình ảnh
            $table->string('image')->nullable();

            // Số lượng tồn kho
            $table->integer('quantity')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};