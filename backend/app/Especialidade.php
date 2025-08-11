<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Especialidade extends Model
{
    protected $fillable = [
        'nome', 'descricao', 'ativa' // Era 'ativo', deve ser 'ativa' para consistência
    ];

    protected $casts = [
        'ativa' => 'boolean', // Era 'ativo', deve ser 'ativa'
    ];

    public function entidades()
    {
        return $this->belongsToMany(Entidade::class, 'entidade_especialidade');
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativa', true); // Era 'ativo', deve ser 'ativa'
    }
}