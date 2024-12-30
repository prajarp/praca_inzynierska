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
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'window_quantity',
                'other_elements_quantity',
                'windows_weight',
                'total_weight',
                'window_area',
                'window_dimensions',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('window_quantity')->nullable();
            $table->integer('other_elements_quantity')->nullable();
            $table->float('windows_weight')->nullable();
            $table->float('total_weight')->nullable();
            $table->float('window_area')->nullable();
            $table->string('window_dimensions')->nullable();
        });
    }
};
