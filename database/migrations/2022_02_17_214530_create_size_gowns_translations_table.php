<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSizeGownsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('size_gowns_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size_gown_id')->constrained('size_gowns')->onUpdate('cascade')->onDelete('cascade'); 
            $table->string('name');    
            $table->string('locale');    
            $table->timestamps();
            $table->unique(['size_gown_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('size_gowns_translations');
    }
}
