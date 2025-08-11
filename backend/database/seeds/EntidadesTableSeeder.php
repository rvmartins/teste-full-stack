<?php

use Illuminate\Database\Seeder;
use App\Entidade;

class EntidadesTableSeeder extends Seeder
{
    public function run()
    {
        $entidades = [
            [
                'razao_social' => 'Clínica São Paulo Ltda',
                'nome_fantasia' => 'Clínica São Paulo',
                'cnpj' => '11222333000181',
                'regional' => 'Sudeste',
                'data_inauguracao' => '2020-01-15',
                'ativa' => true,
                'especialidades' => [1, 2, 3, 4, 5, 6] // IDs das especialidades
            ],
            [
                'razao_social' => 'Centro Médico Norte Ltda',
                'nome_fantasia' => 'Centro Médico Norte',
                'cnpj' => '22333444000192',
                'regional' => 'Norte',
                'data_inauguracao' => '2019-03-10',
                'ativa' => true,
                'especialidades' => [1, 3, 5, 7, 9, 11]
            ],
            [
                'razao_social' => 'Clínica Nordeste S.A.',
                'nome_fantasia' => 'Clínica Nordeste',
                'cnpj' => '33444555000103',
                'regional' => 'Nordeste',
                'data_inauguracao' => '2021-06-20',
                'ativa' => false,
                'especialidades' => [2, 4, 6, 8, 10, 12]
            ]
        ];

        foreach ($entidades as $entidadeData) {
            $especialidades = $entidadeData['especialidades'];
            unset($entidadeData['especialidades']);
            
            $entidade = Entidade::create($entidadeData);
            $entidade->especialidades()->attach($especialidades);
        }
    }
}
