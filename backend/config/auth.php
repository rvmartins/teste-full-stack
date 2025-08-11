<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configurações Padrão de Autenticação
    |--------------------------------------------------------------------------
    |
    | Esta opção controla o "guard" de autenticação padrão e as opções de
    | redefinição de senha para sua aplicação. Você pode alterar estes
    | padrões conforme necessário, mas são um ótimo ponto de partida.
    |
    */

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Guards de Autenticação
    |--------------------------------------------------------------------------
    |
    | Guards definem como os usuários são autenticados para cada requisição.
    | Por padrão, temos um guard "web" que usa sessions e um guard "api" que
    | usa tokens. Você pode definir quantos guards quiser.
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session', // Usa sessões para autenticação web
            'provider' => 'users',
        ],

        'api' => [
            'driver' => 'token', // Usa tokens para autenticação da API
            'provider' => 'users',
            'hash' => false, // Tokens não são hasheados (para Laravel 5.4)
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Provedores de Usuário
    |--------------------------------------------------------------------------
    |
    | Todos os guards de autenticação têm um provedor de usuário. Isto define
    | como os usuários são realmente recuperados do seu banco de dados ou
    | outros mecanismos de armazenamento usados pela aplicação.
    |
    */

    'providers' => [
        'users' => [
            'driver' => 'eloquent', // Usa Eloquent ORM
            'model' => App\User::class, // Modelo do usuário
        ],

        // Alternativa usando driver de banco de dados direto:
        // 'users' => [
        //     'driver' => 'database',
        //     'table' => 'users',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Redefinição de Senhas
    |--------------------------------------------------------------------------
    |
    | Aqui você pode definir as configurações para redefinição de senhas,
    | incluindo a tabela utilizada para armazenar os tokens de redefinição
    | e o tempo de expiração dos tokens.
    |
    */

    'passwords' => [
        'users' => [
            'provider' => 'users', // Usa o provedor 'users' definido acima
            'table' => 'password_resets', // Tabela para tokens de redefinição
            'expire' => 60, // Tokens expiram em 60 minutos
        ],
    ],

];