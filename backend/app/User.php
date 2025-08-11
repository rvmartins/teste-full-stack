<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Atributos que podem ser preenchidos em massa
     *
     * @var array
     */
    protected $fillable = [
        'nome', 'email', 'password', 'ativo', 'api_token',
    ];

    /**
     * Atributos que devem ser ocultados para arrays
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token',
    ];

    /**
     * Atributos que devem ser convertidos para tipos nativos
     *
     * @var array
     */
    protected $casts = [
        'ativo' => 'boolean',
    ];

    /**
     * Mutator para hash da senha
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Verifica se o usuário está ativo
     */
    public function isAtivo()
    {
        return $this->ativo;
    }

    /**
     * Scope para buscar apenas usuários ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * Gerar token de API simples
     */
    public function generateApiToken()
    {
        $this->api_token = hash('sha256', str_random(60));
        $this->save();
        
        return $this->api_token;
    }

    /**
     * Revogar token atual
     */
    public function revokeApiToken()
    {
        $this->api_token = null;
        $this->save();
    }

    /**
     * Verificar se o token é válido
     */
    public function hasValidApiToken($token)
    {
        return $this->api_token && hash_equals($this->api_token, $token);
    }
}