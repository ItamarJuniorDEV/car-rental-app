<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/register',
        summary: 'Cadastrar novo usuário',
        tags: ['Autenticação'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name',                  type: 'string',  example: 'João Silva'),
                    new OA\Property(property: 'email',                 type: 'string',  format: 'email', example: 'joao@exemplo.com'),
                    new OA\Property(property: 'password',              type: 'string',  format: 'password', example: 'senha123'),
                    new OA\Property(property: 'password_confirmation', type: 'string',  format: 'password', example: 'senha123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Usuário criado',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'token', type: 'string', example: 'abc123...')]
                )
            ),
            new OA\Response(response: 422, description: 'Dados inválidos'),
        ]
    )]
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|string|email|max:255|unique:users',
            'password'              => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'api_token' => Str::random(80),
        ]);

        return response()->json(['token' => $user->api_token], 201);
    }

    #[OA\Post(
        path: '/api/login',
        summary: 'Autenticar usuário',
        tags: ['Autenticação'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['email', 'password'],
                properties: [
                    new OA\Property(property: 'email',    type: 'string', format: 'email', example: 'joao@exemplo.com'),
                    new OA\Property(property: 'password', type: 'string', format: 'password', example: 'senha123'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Autenticado',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'token', type: 'string', example: 'abc123...')]
                )
            ),
            new OA\Response(response: 422, description: 'Credenciais inválidas'),
        ]
    )]
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        $user->api_token = Str::random(80);
        $user->save();

        return response()->json(['token' => $user->api_token]);
    }

    #[OA\Post(
        path: '/api/logout',
        summary: 'Encerrar sessão',
        tags: ['Autenticação'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout realizado',
                content: new OA\JsonContent(
                    properties: [new OA\Property(property: 'msg', type: 'string', example: 'Logout realizado com sucesso.')]
                )
            ),
            new OA\Response(response: 401, description: 'Não autenticado'),
        ]
    )]
    public function logout(Request $request)
    {
        $request->user()->update(['api_token' => null]);

        return response()->json(['msg' => 'Logout realizado com sucesso.']);
    }
}
