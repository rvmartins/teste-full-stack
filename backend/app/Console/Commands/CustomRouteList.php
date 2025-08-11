<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Routing\Router;

class CustomRouteList extends Command
{
    /**
     * O nome e assinatura do comando.
     *
     * @var string
     */
    protected $signature = 'route:list-custom {--path= : Filtrar por caminho}';

    /**
     * A descrição do comando.
     *
     * @var string
     */
    protected $description = 'Lista todas as rotas registradas (versão compatível PHP 7.4+)';

    /**
     * A instância do router.
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Criar uma nova instância do comando.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        parent::__construct();
        $this->router = $router;
    }

    /**
     * Executar o comando.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $routes = collect($this->router->getRoutes())->map(function ($route) {
                return $this->getRouteInformation($route);
            })->filter(function ($route) {
                return $this->filterRoute($route);
            })->values();

            if ($routes->isEmpty()) {
                $this->error('Nenhuma rota encontrada.');
                return;
            }

            $this->table(
                ['Método', 'URI', 'Nome', 'Ação', 'Middleware'],
                $routes->toArray()
            );
        } catch (\Exception $e) {
            $this->error('Erro ao processar rotas: ' . $e->getMessage());
            $this->error('Linha: ' . $e->getLine());
            
            // Fallback: listar rotas de forma mais simples
            $this->info('Tentando listar rotas de forma simplificada...');
            $this->listRoutesSimple();
        }
    }

    /**
     * Listar rotas de forma simplificada (fallback).
     *
     * @return void
     */
    protected function listRoutesSimple()
    {
        $routes = [];
        $pathFilter = $this->option('path');

        foreach ($this->router->getRoutes() as $route) {
            try {
                $uri = $route->uri();
                
                if ($pathFilter && strpos($uri, $pathFilter) === false) {
                    continue;
                }

                $routes[] = [
                    implode('|', $route->methods()),
                    $uri,
                    $route->getName() ?: '-',
                    'Ver detalhes no código'
                ];
            } catch (\Exception $e) {
                // Pular rotas problemáticas
                continue;
            }
        }

        if (!empty($routes)) {
            $this->table(['Método', 'URI', 'Nome', 'Ação'], $routes);
        }
    }

    /**
     * Obter informações da rota.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return array
     */
    protected function getRouteInformation($route)
    {
        return [
            'method' => implode('|', $route->methods()),
            'uri' => (string) $route->uri(),
            'name' => $route->getName() ?: '-',
            'action' => $this->getRouteAction($route),
            'middleware' => $this->getRouteMiddleware($route),
        ];
    }

    /**
     * Obter a ação da rota.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return string
     */
    protected function getRouteAction($route)
    {
        try {
            $action = $route->getAction();

            if (isset($action['controller'])) {
                return (string) $action['controller'];
            }

            if (isset($action['uses'])) {
                if ($action['uses'] instanceof \Closure) {
                    return 'Closure';
                }
                return (string) $action['uses'];
            }

            // Verificar se é uma closure diretamente
            if ($action instanceof \Closure) {
                return 'Closure';
            }

            // Se for um array com 'uses' como closure
            if (is_array($action) && isset($action[0]) && $action[0] instanceof \Closure) {
                return 'Closure';
            }

            return 'Closure';
        } catch (\Exception $e) {
            return 'Error';
        }
    }

    /**
     * Obter middleware da rota.
     *
     * @param  \Illuminate\Routing\Route  $route
     * @return string
     */
    protected function getRouteMiddleware($route)
    {
        try {
            $middleware = $route->gatherMiddleware();
            
            if (empty($middleware)) {
                return '-';
            }

            // Converter todos os valores para string de forma segura
            $middlewareStrings = array_map(function($item) {
                if ($item instanceof \Closure) {
                    return 'Closure';
                }
                if (is_object($item)) {
                    return get_class($item);
                }
                if (is_array($item)) {
                    return 'Array';
                }
                return (string) $item;
            }, $middleware);

            return implode(', ', $middlewareStrings);
        } catch (\Exception $e) {
            return 'Error';
        }
    }

    /**
     * Filtrar rota por caminho.
     *
     * @param  array  $route
     * @return bool
     */
    protected function filterRoute($route)
    {
        if ($path = $this->option('path')) {
            return strpos($route['uri'], $path) !== false;
        }

        return true;
    }
}