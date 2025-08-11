<?php

use Illuminate\Http\Request;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Rotas da API - Modelo Base Completo
|--------------------------------------------------------------------------
|
| Aqui estão registradas as rotas da API para sua aplicação. Estas rotas
| são carregadas pelo RouteServiceProvider dentro do grupo que possui
| o middleware "api" aplicado automaticamente.
|
| Sistema de autenticação: Tokens simples (sem Passport)
| Compatível com: Laravel 5.4 + PHP 7.4+
|
*/

/*
|--------------------------------------------------------------------------
| Rotas públicas (sem autenticação)
|--------------------------------------------------------------------------
*/

// Rota básica para testar se a API está funcionando
Route::get('test', function () {
    return response()->json([
        'message' => 'API funcionando!',
        'timestamp' => Carbon::now(),
        'laravel_version' => app()->version()
    ]);
});

// Rota para verificar o status geral da API
Route::get('status', function () {
    return response()->json([
        'api' => 'Sistema de Clínicas API',
        'version' => '1.0.0',
        'status' => 'online',
        'timestamp' => Carbon::now(),
        'laravel_version' => app()->version()
    ]);
});

/*
|--------------------------------------------------------------------------
| Rotas de autenticação (públicas)
|--------------------------------------------------------------------------
*/

// Suporte a OPTIONS (CORS preflight)
Route::options('{any}', function () {
    return response('', 200);
})->where('any', '.*');

// Registro de novos usuários
Route::post('register', 'API\AuthController@register');
// Login de usuários existentes
Route::post('login', 'API\AuthController@login');

/*
|--------------------------------------------------------------------------
| Rotas protegidas (requerem autenticação via token)
|--------------------------------------------------------------------------
*/

// Grupo de rotas que requerem autenticação via token Bearer
Route::middleware('auth:api')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Rotas relacionadas ao usuário autenticado
    |--------------------------------------------------------------------------
    */
    Route::get('me', 'API\AuthController@me'); // Dados do usuário logado
    Route::post('logout', 'API\AuthController@logout'); // Logout e revogação do token
    Route::post('refresh', 'API\AuthController@refresh'); // Renovação do token
    Route::post('change-password', 'API\AuthController@changePassword'); // Alterar senha

    /*
    |--------------------------------------------------------------------------
    | Recursos do sistema clínico (CRUD completos)
    |--------------------------------------------------------------------------
    */

    // CRUD para entidades (hospitais, clínicas, etc.)
    Route::apiResource('entidades', 'EntidadesController');

    // CRUD para especialidades médicas
    Route::apiResource('especialidades', 'EspecialidadesController');

    // CRUD para usuários (admin)
    Route::apiResource('users', 'UsersController');

    /*
    |--------------------------------------------------------------------------
    | Rotas auxiliares e específicas
    |--------------------------------------------------------------------------
    */

    // Entidades por região
    Route::get('entidades-regionais', 'EntidadesController@regionais');

    // Especialidades por entidade
    Route::get('entidades-especialidades', 'EntidadesController@especialidades');

    /*
    |--------------------------------------------------------------------------
    | Dashboard e estatísticas
    |--------------------------------------------------------------------------
    */
    
    // Estatísticas gerais do dashboard
    Route::get('dashboard/stats', 'DashboardController@stats');
    
    // Entidades agrupadas por regional
    Route::get('dashboard/entidades-por-regional', 'DashboardController@entidadesPorRegional');
    
    // Especialidades mais populares
    Route::get('dashboard/especialidades-populares', 'DashboardController@especialidadesPopulares');
});
