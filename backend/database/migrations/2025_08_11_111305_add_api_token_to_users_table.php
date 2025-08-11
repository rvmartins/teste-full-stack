<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddApiTokenToUsersTable extends Migration
{
    /**
     * Executa as migrações para adicionar o campo api_token
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Adiciona campo api_token após o campo password
            $table->string('api_token', 80)->after('password')
                                ->unique() // Campo único
                                ->nullable() // Pode ser nulo
                                ->default(null); // Valor padrão nulo
        });
    }

    /**
     * Reverte as migrações removendo o campo api_token
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove o campo api_token da tabela
            $table->dropColumn(['api_token']);
        });
    }
}