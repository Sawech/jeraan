<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSizeGownsOptionsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('size_gowns_options_translations', function (Blueprint $table) {
            $table->id();
            //$table->integer('size_gown_option_id');
            $table->foreignId('size_gown_option_id')->constrained('size_gowns_options')->onUpdate('cascade')->onDelete('cascade'); 
            $table->string('name');    
            $table->string('locale');                    
            $table->timestamps();
            //$table->unique(['size_gown_option_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('size_gowns_options_translations');
    }
}
