<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;

class GenerateUserTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:generate-tokens';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera tokens de API para usuários que não possuem';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $users = User::whereNull('api_token')->get();
        
        if ($users->count() === 0) {
            $this->info('Todos os usuários já possuem tokens de API.');
            return;
        }

        $this->info("Encontrados {$users->count()} usuários sem tokens.");

        foreach ($users as $user) {
            $user->generateApiToken();
            $this->info("Token gerado para: {$user->email}");
        }

        $this->info('Tokens gerados com sucesso!');
    }
}