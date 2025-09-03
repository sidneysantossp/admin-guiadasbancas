<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressFieldsToVendorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('address', 500)->nullable()->after('pix_key');
            $table->string('city', 100)->nullable()->after('address');
            $table->string('state', 100)->nullable()->after('city');
            $table->string('zip_code', 20)->nullable()->after('state');
            $table->text('description')->nullable()->after('zip_code');
            $table->integer('delivery_time')->nullable()->after('description');
            $table->decimal('minimum_order', 8, 2)->nullable()->after('delivery_time');
            $table->decimal('delivery_charge', 8, 2)->nullable()->after('minimum_order');
            $table->decimal('free_delivery_over', 8, 2)->nullable()->after('delivery_charge');
            $table->decimal('delivery_radius', 8, 2)->nullable()->after('free_delivery_over');
            $table->text('delivery_areas')->nullable()->after('delivery_radius');
        });
    }

    /**
     * Reverse the migrations.
     *
     *
     * @return void
     */
    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'address',
                'city', 
                'state',
                'zip_code',
                'description',
                'delivery_time',
                'minimum_order',
                'delivery_charge',
                'free_delivery_over',
                'delivery_radius',
                'delivery_areas'
            ]);
        });
    }
}