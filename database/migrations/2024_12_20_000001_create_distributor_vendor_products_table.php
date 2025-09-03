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
        Schema::create('distributor_vendor_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id')->comment('ID do distribuidor');
            $table->unsignedBigInteger('vendor_id')->comment('ID do jornaleiro/vendor');
            $table->unsignedBigInteger('food_id')->comment('ID do produto');
            $table->decimal('vendor_price', 24, 2)->default(0)->comment('Preço para o jornaleiro');
            $table->decimal('margin_percentage', 5, 2)->default(0)->comment('Margem de lucro %');
            $table->integer('min_quantity')->default(1)->comment('Quantidade mínima para pedido');
            $table->integer('stock_quantity')->default(0)->comment('Estoque disponível');
            $table->boolean('status')->default(1)->comment('Status ativo/inativo');
            $table->boolean('auto_approve')->default(0)->comment('Aprovação automática de pedidos');
            $table->timestamps();

            $table->foreign('distributor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('food_id')->references('id')->on('food')->onDelete('cascade');
            
            $table->unique(['distributor_id', 'vendor_id', 'food_id'], 'unique_distributor_vendor_product');
            $table->index(['distributor_id', 'status']);
            $table->index(['vendor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_vendor_products');
    }
};
