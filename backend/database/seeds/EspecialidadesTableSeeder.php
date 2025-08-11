<?php

use Illuminate\Database\Seeder;
use App\Especialidade;

class EspecialidadesTableSeeder extends Seeder
{
    public function run()
    {
        $especialidades = [
            ['nome' => 'Cardiologia', 'descricao' => 'Especialidade médica que cuida do coração e sistema cardiovascular', 'ativa' => true],
            ['nome' => 'Neurologia', 'descricao' => 'Especialidade que trata do sistema nervoso', 'ativa' => true],
            ['nome' => 'Ortopedia', 'descricao' => 'Especialidade que cuida dos ossos, músculos e articulações', 'ativa' => true],
            ['nome' => 'Dermatologia', 'descricao' => 'Especialidade que trata da pele e seus anexos', 'ativa' => true],
            ['nome' => 'Pediatria', 'descricao' => 'Especialidade que cuida da saúde de crianças e adolescentes', 'ativa' => true],
            ['nome' => 'Ginecologia', 'descricao' => 'Especialidade que cuida da saúde da mulher', 'ativa' => true],
            ['nome' => 'Oftalmologia', 'descricao' => 'Especialidade que cuida dos olhos e da visão', 'ativa' => true],
            ['nome' => 'Otorrinolaringologia', 'descricao' => 'Especialidade que cuida de ouvido, nariz e garganta', 'ativa' => true],
            ['nome' => 'Urologia', 'descricao' => 'Especialidade que cuida do sistema urinário', 'ativa' => true],
            ['nome' => 'Psiquiatria', 'descricao' => 'Especialidade que cuida da saúde mental', 'ativa' => true],
            ['nome' => 'Endocrinologia', 'descricao' => 'Especialidade que cuida das glândulas e hormônios', 'ativa' => true],
            ['nome' => 'Gastroenterologia', 'descricao' => 'Especialidade que cuida do sistema digestivo', 'ativa' => true],
        ];

        foreach ($especialidades as $especialidade) {
            Especialidade::create($especialidade);
        }
    }
}
