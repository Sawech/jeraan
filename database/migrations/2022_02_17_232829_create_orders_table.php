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
            $table->integer('user_id');
            $table->integer('category_id');
            $table->integer('fabric_id');
            $table->integer('design_id');
            $table->float('deposit_amount')->nullable();
            $table->float('amount')->nullable();
            $table->string('payment_image')->nullable();
            $table->enum('status', ['new', 'waiting_payment','cut_case','sewing_case','button_case','delivered'])->default('new');
            $table->text('description')->nullable();
            $table->date('delivery_date')->nullable();
            $table->softDeletes();
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
        Schema::dropIfExists('orders');
    }
}
