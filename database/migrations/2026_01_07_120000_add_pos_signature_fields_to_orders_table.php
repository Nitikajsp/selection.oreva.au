<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPosSignatureFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'pos_customer_name')) {
                $table->string('pos_customer_name')->nullable();
            }

            if (!Schema::hasColumn('orders', 'pos_customer_signature')) {
                $table->longText('pos_customer_signature')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'pos_customer_signature')) {
                $table->dropColumn('pos_customer_signature');
            }

            if (Schema::hasColumn('orders', 'pos_customer_name')) {
                $table->dropColumn('pos_customer_name');
            }
        });
    }
}
