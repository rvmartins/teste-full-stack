<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Entidade extends Model
{
    protected $fillable = [
        'razao_social', 'nome_fantasia', 'cnpj', 'regional', 
        'data_inauguracao', 'ativa'
    ];

    protected $casts = [
        'ativa' => 'boolean',
        'data_inauguracao' => 'date',
    ];

    public function especialidades()
    {
        return $this->belongsToMany(Especialidade::class, 'entidade_especialidade');
    }

    public function especialidadesAtivas()
    {
        return $this->especialidades()->where('especialidades.ativa', true); // Era 'ativo', deve ser 'ativa'
    }

    public function scopeAtivas($query)
    {
        return $query->where('ativa', true);
    }

    public function getCnpjFormatadoAttribute()
    {
        $cnpj = preg_replace('/\D/', '', $this->cnpj);
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
    }

    // CORREÇÃO: havia erro de sintaxe no nome do método
    public function getDataInauguracaoFormattedAttribute()
    {
        return $this->data_inauguracao ? $this->data_inauguracao->format('d/m/Y') : null;
    }

    public function getStatusAttribute()
    {
        return $this->ativa ? 'Ativa' : 'Inativa';
    }

    public function setCnpjAttribute($value)
    {
        $this->attributes['cnpj'] = preg_replace('/\D/', '', $value);
    }

    public function temMuitasEspecialidades()
    {
        return $this->especialidades->count() >= 5;
    }

    public function primeirasEspecialidades()
    {
        return $this->especialidades->take(4);
    }

    public function especialidadesRestantes()
    {
        return $this->especialidades->slice(4);
    }

    // ADICIONAR: método para opções de regionais
    public static function getRegionaisOptions()
    {
        return [
            'Norte' => 'Norte',
            'Nordeste' => 'Nordeste',
            'Centro-Oeste' => 'Centro-Oeste',
            'Sudeste' => 'Sudeste',
            'Sul' => 'Sul'
        ];
    }

    // ADICIONAR: scope para filtrar (usado no controller)
    public function scopeFiltrar($query, $busca)
    {
        return $query->where(function($q) use ($busca) {
            $q->where('razao_social', 'like', "%{$busca}%")
              ->orWhere('nome_fantasia', 'like', "%{$busca}%")
              ->orWhere('cnpj', 'like', "%{$busca}%");
        });
    }
}