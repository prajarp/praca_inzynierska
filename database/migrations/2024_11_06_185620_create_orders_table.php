<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->text('delivery_address');
            $table->date('expected_delivery_date');
            $table->integer('window_quantity');
            $table->integer('other_elements_quantity');
            $table->float('windows_weight');
            $table->float('total_weight');
            $table->float('window_area');
            $table->json('window_dimensions'); //JSON format
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
