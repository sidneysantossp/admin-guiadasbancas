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
        Schema::create('distributor_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_order_id')->comment('ID do pedido do distribuidor');
            $table->unsignedBigInteger('food_id')->comment('ID do produto');
            $table->string('product_name')->comment('Nome do produto no momento do pedido');
            $table->decimal('unit_price', 24, 2)->comment('Preço unitário');
            $table->integer('quantity')->comment('Quantidade pedida');
            $table->decimal('total_price', 24, 2)->comment('Preço total do item');
            $table->text('product_details')->nullable()->comment('Detalhes do produto (JSON)');
            $table->timestamps();

            $table->foreign('distributor_order_id')->references('id')->on('distributor_orders')->onDelete('cascade');
            $table->foreign('food_id')->references('id')->on('food')->onDelete('cascade');
            
            $table->index('distributor_order_id');
            $table->index('food_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_order_items');
    }
};
