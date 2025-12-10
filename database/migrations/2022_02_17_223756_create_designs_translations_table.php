<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDesignsTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('designs_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('design_id')->constrained('designs')->onUpdate('cascade')->onDelete('cascade');   
            $table->string('name');   
            $table->text('description'); 
            $table->string('locale'); 
            $table->timestamps();
            $table->unique(['design_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('designs_translations');
    }
}
