<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateListsTable extends Migration
{
    public function up()
    {
        Schema::create('lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('contact_number');
            $table->string('contact_email');
            $table->string('product_name')->nullable(); // Allow null values
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lists');
    }
}
