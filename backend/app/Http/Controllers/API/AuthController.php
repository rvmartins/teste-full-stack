<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;

class AuthController extends Controller
{
    /**
     * Realiza o login do usuário e retorna token de acesso
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Debug: Log quando usuário não é encontrado
            \Log::info('Login falhou: usuário não encontrado para email: ' . $request->email);
            return response()->json([
                'success' => false,
                'message' => 'Email ou senha incorretos',
            ], 401);
        }

        // Debug: Log tentativa de login
        \Log::info('Tentativa de login para: ' . $user->email . ' | Senha digitada: ' . $request->password . ' | Hash no banco: ' . $user->password);

        if (!password_verify($request->password, $user->password)) {
            // Debug: Log quando senha não confere
            \Log::info('Login falhou: senha incorreta para: ' . $user->email);
            return response()->json([
                'success' => false,
                'message' => 'Email ou senha incorretos',
            ], 401);
        }

        // Verificar se o usuário está ativo
        if (!$user->ativo) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário inativo. Entre em contato com o administrador.',
            ], 403);
        }

        // Gerar token
        $token = $user->generateApiToken();

        return response()->json([
            'success' => true,
            'message' => 'Login realizado com sucesso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'ativo' => $user->ativo,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    /**
     * Registra um novo usuário no sistema
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'nome' => $request->nome,
            'email' => $request->email,
            'password' => $request->password, // Será hasheada pelo mutator
            'ativo' => true, // Novo usuário ativo por padrão
        ]);

        // Gerar token
        $token = $user->generateApiToken();

        return response()->json([
            'success' => true,
            'message' => 'Usuário registrado com sucesso',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'ativo' => $user->ativo,
                ],
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 201);
    }

    /**
     * Retorna as informações do usuário autenticado
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'nome' => $user->nome,
                    'email' => $user->email,
                    'ativo' => $user->ativo,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ]
        ], 200);
    }

    /**
     * Realiza o logout do usuário, revogando o token atual
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->revokeApiToken();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ], 200);
    }

    /**
     * Renova o token de acesso do usuário autenticado
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        
        // Gerar novo token (o método já salva automaticamente)
        $token = $user->generateApiToken();

        return response()->json([
            'success' => true,
            'message' => 'Token renovado com sucesso',
            'data' => [
                'token' => $token,
                'token_type' => 'Bearer',
            ]
        ], 200);
    }

    /**
     * Permite ao usuário alterar sua senha atual
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Dados de validação inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        // Verificar senha atual
        if (!password_verify($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Senha atual incorreta',
            ], 422);
        }

        // Atualizar senha
        $user->update([
            'password' => $request->new_password
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Senha alterada com sucesso'
        ], 200);
    }
}