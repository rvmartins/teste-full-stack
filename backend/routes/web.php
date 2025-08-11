<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('home');
});

/*
|--------------------------------------------------------------------------
| Rotas Web para Entidades (Clínicas) - Interface Web
|--------------------------------------------------------------------------
| GET  /entidades           - Lista de entidades
| GET  /entidades/create    - Formulário de criação
| POST /entidades           - Processar criação
| GET  /entidades/{id}      - Ver detalhes
| GET  /entidades/{id}/edit - Formulário de edição
| PUT  /entidades/{id}      - Processar edição
| DELETE /entidades/{id}    - Excluir
*/
Route::resource('entidades', 'EntidadesController');

/*
|--------------------------------------------------------------------------
| Rotas Web para Especialidades - Interface Web
|--------------------------------------------------------------------------
*/
Route::resource('especialidades', 'EspecialidadesController');

/*
|--------------------------------------------------------------------------
| Rotas Web para Usuários - Interface Web
|--------------------------------------------------------------------------
*/
Route::resource('users', 'UsersController');



