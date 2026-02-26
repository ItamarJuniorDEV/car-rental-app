<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_pode_se_registrar()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $response->assertStatus(201)->assertJsonStructure(['token']);
        $this->assertDatabaseHas('users', ['email' => 'joao@example.com']);
    }

    public function test_email_duplicado_retorna_erro()
    {
        User::factory()->create(['email' => 'joao@example.com']);

        $response = $this->postJson('/api/register', [
            'name' => 'Outro João',
            'email' => 'joao@example.com',
            'password' => 'senha1234',
            'password_confirmation' => 'senha1234',
        ]);

        $response->assertStatus(422);
    }

    public function test_usuario_pode_fazer_login()
    {
        $user = User::factory()->create(['password' => bcrypt('senha1234')]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'senha1234',
        ]);

        $response->assertOk()->assertJsonStructure(['token']);
    }

    public function test_credenciais_invalidas_retornam_erro()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'senha_errada',
        ]);

        $response->assertStatus(422);
    }

    public function test_usuario_pode_fazer_logout()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/logout');

        $response->assertOk();
    }

    public function test_requisicao_sem_token_retorna_401()
    {
        $response = $this->getJson('/api/brands');

        $response->assertStatus(401);
    }
}
