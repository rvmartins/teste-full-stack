<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Especialidade;
use Illuminate\Support\Facades\Validator;

class EspecialidadesController extends Controller
{
    /**
     * Display a listing of especialidades.
     */
    public function index(Request $request)
    {
        $query = Especialidade::with('entidades');

        // Filtros
        if ($request->has('busca') && $request->get('busca') != '') {
            $query->where('nome', 'like', "%{$request->busca}%");
        }

        if ($request->has('ativa') && $request->get('ativa') != '') {
            $query->where('ativa', $request->ativa);
        }

        // Ordenação
        $sortBy = $request->get('sort_by', 'nome');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        // Paginação (removida temporariamente para teste)
        $especialidades = $query->get();

        // Se for requisição de API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'data' => $especialidades,
                'total' => $especialidades->count(),
            ]);
        }

        // Se for requisição web, retorna view
        return view('especialidades.index', compact('especialidades'));
    }

    /**
     * Show the form for creating a new especialidade.
     */
    public function create(Request $request)
    {
        // API não precisa de formulário
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Use POST to create especialidade'], 405);
        }
        
        return view('especialidades.create');
    }

    /**
     * Store a newly created especialidade in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:especialidades',
            'descricao' => 'nullable|string',
            'ativa' => 'boolean',
        ], [
            'nome.unique' => 'Esta especialidade já está cadastrada.',
            'nome.required' => 'O nome da especialidade é obrigatório.',
        ]);

        if ($validator->fails()) {
            // Se for API, retorna erros em JSON
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Se for web, redireciona com erros
            return redirect('especialidades/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $especialidade = Especialidade::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'ativa' => $request->has('ativa') ? true : false,
        ]);

        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($especialidade, 201);
        }

        // Se for web, redireciona
        return redirect()->route('especialidades.index')->with('success', 'Especialidade criada com sucesso!');
    }

    /**
     * Display the specified especialidade.
     */
    public function show(Request $request, $id)
    {
        $especialidade = Especialidade::with('entidades')->findOrFail($id);
        
        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($especialidade);
        }
        
        return view('especialidades.show', compact('especialidade'));
    }

    /**
     * Show the form for editing the specified especialidade.
     */
    public function edit(Request $request, $id)
    {
        // API não precisa de formulário
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Use PUT to update especialidade'], 405);
        }

        $especialidade = Especialidade::findOrFail($id);
        return view('especialidades.edit', compact('especialidade'));
    }

    /**
     * Update the specified especialidade in storage.
     */
    public function update(Request $request, $id)
    {
        $especialidade = Especialidade::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255|unique:especialidades,nome,' . $especialidade->id,
            'descricao' => 'nullable|string',
            'ativa' => 'boolean',
        ], [
            'nome.unique' => 'Esta especialidade já está cadastrada.',
            'nome.required' => 'O nome da especialidade é obrigatório.',
        ]);

        if ($validator->fails()) {
            // Se for API, retorna erros em JSON
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect("especialidades/{$id}/edit")
                        ->withErrors($validator)
                        ->withInput();
        }

        $especialidade->update([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'ativa' => $request->has('ativa') ? true : false,
        ]);

        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($especialidade);
        }

        return redirect()->route('especialidades.index')->with('success', 'Especialidade atualizada com sucesso!');
    }

    /**
     * Remove the specified especialidade from storage.
     */
    public function destroy(Request $request, $id)
    {
        $especialidade = Especialidade::findOrFail($id);
        
        // Verificar se a especialidade está sendo usada
        if ($especialidade->entidades()->count() > 0) {
            $message = 'Não é possível excluir esta especialidade pois ela está vinculada a clínicas.';
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json(['message' => $message], 422);
            }
            
            return redirect()->route('especialidades.index')->with('error', $message);
        }
        
        $especialidade->delete();

        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Especialidade excluída com sucesso!']);
        }

        return redirect()->route('especialidades.index')->with('success', 'Especialidade excluída com sucesso!');
    }
}