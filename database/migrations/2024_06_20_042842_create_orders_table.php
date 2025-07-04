<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_code'); 
            $table->string('price');
            $table->integer('quantity');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('list_id');
            $table->string('product_order_image');
            $table->timestamps();

            // Define foreign keys if applicable
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('list_id')->references('id')->on('lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
