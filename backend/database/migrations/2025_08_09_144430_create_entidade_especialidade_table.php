<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntidadeEspecialidadeTable extends Migration
{
    public function up()
    {
        Schema::create('entidade_especialidade', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('entidade_id')->unsigned();
            $table->integer('especialidade_id')->unsigned();
            $table->timestamps();

            $table->foreign('entidade_id')->references('id')->on('entidades')->onDelete('cascade');
            $table->foreign('especialidade_id')->references('id')->on('especialidades')->onDelete('cascade');
            
            $table->unique(['entidade_id', 'especialidade_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('entidade_especialidade');
    }
}