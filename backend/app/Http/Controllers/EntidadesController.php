<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entidade;
use App\Especialidade;
use Illuminate\Support\Facades\Validator;

class EntidadesController extends Controller
{
    /**
     * Display a listing of entidades.
     */
    public function index(Request $request)
    {
        $query = Entidade::with('especialidades');

        // Filtros
        if ($request->has('busca') && $request->get('busca') != '') {
            $query->filtrar($request->busca);
        }

        if ($request->has('regional') && $request->get('regional') != '') {
            $query->where('regional', $request->regional);
        }

        if ($request->has('ativa') && $request->get('ativa') != '') {
            $query->where('ativa', $request->ativa);
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'razao_social');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginação
        $entidades = $query->paginate(15);

        // Se for requisição de API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => $entidades->items(),
                'pagination' => [
                    'current_page' => $entidades->currentPage(),
                    'last_page' => $entidades->lastPage(),
                    'per_page' => $entidades->perPage(),
                    'total' => $entidades->total(),
                ]
            ]);
        }

        // Se for requisição web, retorna view
        return view('entidades.index', compact('entidades'));
    }

    /**
     * Show the form for creating a new entidade.
     */
    public function create(Request $request)
    {
        // API não precisa de formulário
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Use POST to create entidade'], 405);
        }

        $regionais = Entidade::getRegionaisOptions();
        $especialidades = Especialidade::ativas()->orderBy('nome')->get();
        
        return view('entidades.create', compact('regionais', 'especialidades'));
    }

    /**
     * Store a newly created entidade in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log dos dados recebidos
        \Log::info('Dados recebidos no store:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'required|string|max:255',
            'cnpj' => 'required|string|min:14|max:18|unique:entidades',
            'regional' => 'required|string',
            'data_inauguracao' => 'required|date',
            'ativa' => 'boolean',
            'especialidades' => 'required|array|min:2',
            'especialidades.*' => 'exists:especialidades,id',
        ], [
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'especialidades.min' => 'Selecione pelo menos 2 especialidades.',
            'especialidades.required' => 'As especialidades são obrigatórias.',
        ]);

        // Validação customizada do CNPJ
        $validator->after(function ($validator) use ($request) {
            if (!$this->validarCnpj($request->cnpj)) {
                $validator->errors()->add('cnpj', 'CNPJ inválido.');
            }
        });

        if ($validator->fails()) {
            // Debug: Log dos erros de validação
            \Log::info('Erros de validação:', $validator->errors()->toArray());
            
            // Se for API, retorna erros em JSON
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Se for web, redireciona com erros
            return redirect('entidades/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $entidade = Entidade::create([
            'razao_social' => $request->razao_social,
            'nome_fantasia' => $request->nome_fantasia,
            'cnpj' => $request->cnpj,
            'regional' => $request->regional,
            'data_inauguracao' => $request->data_inauguracao,
            'ativa' => $request->get('ativa', false),
        ]);

        // Associar especialidades
        $entidade->especialidades()->attach($request->especialidades);

        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($entidade->load('especialidades'), 201);
        }

        // Se for web, redireciona
        return redirect()->route('entidades.index')->with('success', 'Clínica criada com sucesso!');
    }

    /**
     * Display the specified entidade.
     */
    public function show(Request $request, $id)
    {
        $entidade = Entidade::with('especialidades')->findOrFail($id);
        
        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($entidade);
        }
        
        return view('entidades.show', compact('entidade'));
    }

    /**
     * Show the form for editing the specified entidade.
     */
    public function edit(Request $request, $id)
    {
        // API não precisa de formulário
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Use PUT to update entidade'], 405);
        }

        $entidade = Entidade::with('especialidades')->findOrFail($id);
        $regionais = Entidade::getRegionaisOptions();
        $especialidades = Especialidade::ativas()->orderBy('nome')->get();
        $especialidadesSelecionadas = $entidade->especialidades->pluck('id')->toArray();
        
        return view('entidades.edit', compact('entidade', 'regionais', 'especialidades', 'especialidadesSelecionadas'));
    }

    /**
     * Update the specified entidade in storage.
     */
    public function update(Request $request, $id)
    {
        $entidade = Entidade::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'razao_social' => 'required|string|max:255',
            'nome_fantasia' => 'required|string|max:255',
            'cnpj' => 'required|string|min:14|max:18|unique:entidades,cnpj,' . $entidade->id,
            'regional' => 'required|string',
            'data_inauguracao' => 'required|date',
            'ativa' => 'boolean',
            'especialidades' => 'required|array|min:2',
            'especialidades.*' => 'exists:especialidades,id',
        ], [
            'cnpj.unique' => 'Este CNPJ já está cadastrado.',
            'especialidades.min' => 'Selecione pelo menos 2 especialidades.',
            'especialidades.required' => 'As especialidades são obrigatórias.',
        ]);

        // Validação customizada do CNPJ
        $validator->after(function ($validator) use ($request) {
            if (!$this->validarCnpj($request->cnpj)) {
                $validator->errors()->add('cnpj', 'CNPJ inválido.');
            }
        });

        if ($validator->fails()) {
            // Se for API, retorna erros em JSON
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect("entidades/{$id}/edit")
                        ->withErrors($validator)
                        ->withInput();
        }

        $entidade->update([
            'razao_social' => $request->razao_social,
            'nome_fantasia' => $request->nome_fantasia,
            'cnpj' => $request->cnpj,
            'regional' => $request->regional,
            'data_inauguracao' => $request->data_inauguracao,
            'ativa' => $request->get('ativa', false),
        ]);

        // Atualizar especialidades
        $entidade->especialidades()->sync($request->especialidades);

        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($entidade->load('especialidades'));
        }

        return redirect()->route('entidades.index')->with('success', 'Clínica atualizada com sucesso!');
    }

    /**
     * Remove the specified entidade from storage.
     */
    public function destroy(Request $request, $id)
    {
        $entidade = Entidade::findOrFail($id);
        
        // Remove relacionamentos com especialidades
        $entidade->especialidades()->detach();
        
        $entidade->delete();

        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Clínica excluída com sucesso!']);
        }

        return redirect()->route('entidades.index')->with('success', 'Clínica excluída com sucesso!');
    }

    /**
     * Retorna as opções de regionais para API
     */
    public function regionais()
    {
        return response()->json(Entidade::getRegionaisOptions());
    }

    /**
     * Retorna as especialidades ativas para API
     */
    public function especialidades()
    {
        $especialidades = Especialidade::ativas()->orderBy('nome')->get();
        return response()->json($especialidades);
    }

    /**
     * Valida CNPJ
     */
    private function validarCnpj($cnpj)
    {
        // Remove caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
        
        // Verifica se tem 14 dígitos
        if (strlen($cnpj) != 14) {
            return false;
        }
        
        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }
        
        // Validação dos dígitos verificadores
        for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        
        $resto = $soma % 11;
        
        if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
            return false;
        }
        
        for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
            $soma += $cnpj[$i] * $j;
            $j = ($j == 2) ? 9 : $j - 1;
        }
        
        $resto = $soma % 11;
        
        return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
    }
}