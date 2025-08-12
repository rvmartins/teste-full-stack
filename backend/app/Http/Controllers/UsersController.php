<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $users = User::all();
        
        // Se for requisição de API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($users);
        }
        
        // Se for requisição web, retorna view
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(Request $request)
    {
        // API não precisa de formulário
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Use POST to create user'], 405);
        }
        
        return view('users.create');
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'ativo' => 'boolean',
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
            return redirect('users/create')
                        ->withErrors($validator)
                        ->withInput();
        }

        $user = User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => $request->password,
            'ativo' => $request->get('ativo', true),
        ]);

        // Gerar token para novos usuários
        $user->generateApiToken();

        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($user, 201);
        }

        // Se for web, redireciona
        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso!');
    }

    /**
     * Display the specified user.
     */
    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($user);
        }
        
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(Request $request, $id)
    {
        // API não precisa de formulário
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Use PUT to update user'], 405);
        }
        
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'ativo' => 'boolean',
        ]);

        if ($validator->fails()) {
            // Se for API, retorna erros em JSON
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect("users/{$id}/edit")
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = [
            'nome' => $request->nome,
            'email' => $request->email,
            'ativo' => $request->get('ativo', false),
        ];

        // Só atualiza a senha se foi fornecida
        if ($request->has('password') && $request->get('password') != '') {
            $data['password'] = $request->password;
        }

        $user->update($data);

        // Se não tem token, gerar um
        if (!$user->api_token) {
            $user->generateApiToken();
        }

        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json($user);
        }

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        // Se for API, retorna JSON
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Usuário excluído com sucesso!']);
        }

        return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso!');
    }
}