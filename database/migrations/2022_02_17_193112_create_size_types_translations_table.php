<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSizeTypesTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('size_types_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('size_type_id')->constrained('size_types')->onUpdate('cascade')->onDelete('cascade');
            $table->string('name');
            $table->string('locale');
            $table->timestamps();
            $table->unique(['size_type_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('size_types_translations');
    }
}
