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
        Schema::create('trailers', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->float('height');
            $table->float('width');
            $table->float('length');
            $table->float('max_weight');
            $table->float('total_height');
            $table->float('total_length');
            $table->float('axle_weight');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trailers');
    }
};
