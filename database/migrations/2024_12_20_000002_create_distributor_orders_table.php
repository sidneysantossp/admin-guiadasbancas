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
        Schema::create('distributor_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique()->comment('Número do pedido');
            $table->unsignedBigInteger('distributor_id')->comment('ID do distribuidor');
            $table->unsignedBigInteger('vendor_id')->comment('ID do jornaleiro/vendor');
            $table->decimal('total_amount', 24, 2)->default(0)->comment('Valor total do pedido');
            $table->integer('total_items')->default(0)->comment('Total de itens');
            $table->enum('status', ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled'])
                ->default('pending')->comment('Status do pedido');
            $table->text('notes')->nullable()->comment('Observações do pedido');
            $table->timestamp('delivery_date')->nullable()->comment('Data prevista de entrega');
            $table->text('delivery_address')->nullable()->comment('Endereço de entrega');
            $table->enum('payment_status', ['pending', 'paid', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['cash', 'transfer', 'credit'])->default('cash');
            $table->timestamps();

            $table->foreign('distributor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            
            $table->index(['distributor_id', 'status']);
            $table->index(['vendor_id', 'status']);
            $table->index('order_number');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('distributor_orders');
    }
};
