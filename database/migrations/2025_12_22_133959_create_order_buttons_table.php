<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderButtonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_buttons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->string('jaap_num')->nullable();
            $table->string('neck_num')->nullable();
            $table->string('neck_count')->nullable();
            $table->string('japz_num')->nullable();
            $table->string('japz_count')->nullable();
            $table->string('cabk_num')->nullable();
            $table->string('cabk_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_buttons');
    }
}