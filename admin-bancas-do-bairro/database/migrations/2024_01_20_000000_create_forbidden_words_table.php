<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forbidden_words', function (Blueprint $table) {
            $table->id();
            $table->string('word')->index();
            $table->string('language', 5)->default('pt');
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium');
            $table->string('category')->nullable();
            $table->text('replacement')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->unique(['word', 'language']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forbidden_words');
    }
};