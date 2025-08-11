<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Rotas da API
|--------------------------------------------------------------------------
|
| Aqui estão registradas as rotas da API para sua aplicação. Estas rotas
| são carregadas pelo RouteServiceProvider dentro do grupo que possui
| o middleware "api" aplicado automaticamente.
|
*/

/*
|--------------------------------------------------------------------------
| Rotas públicas (sem autenticação)
|--------------------------------------------------------------------------
*/

// Rota básica para testar se a API está funcionando
Route::get('test', function() {
    return ['message' => 'API funcionando', 'timestamp' => date('Y-m-d H:i:s')];
});

// Rota para verificar o status geral da API
Route::get('status', function() {
    return [
        'api' => 'Sistema de Clínicas API',
        'version' => '1.0.0',
        'status' => 'online',
        'timestamp' => date('Y-m-d H:i:s')
    ];
});

/*
|--------------------------------------------------------------------------
| Rotas de autenticação (públicas)
|--------------------------------------------------------------------------
*/

// Registro de novos usuários
Route::post('register', 'API\AuthController@register');
// Login de usuários existentes
Route::post('login', 'API\AuthController@login');

/*
|--------------------------------------------------------------------------
| Rotas protegidas (requerem autenticação)
|--------------------------------------------------------------------------
*/

// Grupo de rotas que requerem autenticação via token
Route::middleware('auth:api')->group(function () {
    
    // Rotas relacionadas ao usuário autenticado
    Route::get('me', 'API\AuthController@me'); // Dados do usuário logado
    Route::post('logout', 'API\AuthController@logout'); // Logout e revogação do token
    Route::post('refresh', 'API\AuthController@refresh'); // Renovação do token
    Route::post('change-password', 'API\AuthController@changePassword'); // Alterar senha
    
    // Rotas básicas para teste do CRUD
    Route::get('entidades', 'EntidadesController@index');
    Route::get('especialidades', 'EspecialidadesController@index');
    
});