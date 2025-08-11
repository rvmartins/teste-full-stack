<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEntidadesTable extends Migration
{
    public function up()
    {
        Schema::create('entidades', function (Blueprint $table) {
            $table->increments('id');
            $table->string('razao_social');
            $table->string('nome_fantasia');
            $table->string('cnpj', 14)->unique();
            $table->string('regional');
            $table->date('data_inauguracao');
            $table->boolean('ativa')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entidades');
    }
}