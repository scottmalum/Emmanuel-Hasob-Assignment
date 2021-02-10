<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemCheckOutsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_check_outs', function (Blueprint $table) {
            $table->integer('checkout_id')->nullable();
            $table->integer('product_id')->nullable();
            $table->float('price');
            $table->integer('quantity');
            $table->float('sub_total');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_check_outs');
    }
}
