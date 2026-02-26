<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $operador;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->operador = User::factory()->create();
    }

    public function test_pode_listar_clientes()
    {
        Client::factory()->count(3)->create();

        $response = $this->actingAs($this->operador, 'sanctum')->getJson('/api/clients');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonCount(3, 'data');
    }

    public function test_filtro_por_nome()
    {
        Client::factory()->create(['name' => 'Maria Souza']);
        Client::factory()->create(['name' => 'João Silva']);

        $response = $this->actingAs($this->operador, 'sanctum')->getJson('/api/clients?name=Maria');

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_pode_criar_cliente()
    {
        $response = $this->actingAs($this->operador, 'sanctum')->postJson('/api/clients', [
            'name' => 'Carlos Mendes',
            'cpf' => '123.456.789-00',
            'email' => 'carlos@exemplo.com',
            'phone' => '(51) 99999-1234',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('clients', ['cpf' => '123.456.789-00']);
    }

    public function test_cpf_duplicado_retorna_erro()
    {
        Client::factory()->create(['cpf' => '123.456.789-00']);

        $response = $this->actingAs($this->operador, 'sanctum')->postJson('/api/clients', [
            'name' => 'Outro Carlos',
            'cpf' => '123.456.789-00',
            'email' => 'outro@exemplo.com',
            'phone' => '(51) 99999-0000',
        ]);

        $response->assertStatus(422);
    }

    public function test_pode_atualizar_cliente()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->operador, 'sanctum')->putJson("/api/clients/{$client->id}", [
            'phone' => '(51) 88888-0000',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('clients', ['id' => $client->id, 'phone' => '(51) 88888-0000']);
    }

    public function test_pode_deletar_cliente()
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->operador, 'sanctum')->deleteJson("/api/clients/{$client->id}");

        $response->assertOk();
        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_cliente_inexistente_retorna_404()
    {
        $response = $this->actingAs($this->operador, 'sanctum')->getJson('/api/clients/9999');

        $response->assertStatus(404);
    }

    public function test_sem_autenticacao_retorna_401()
    {
        $response = $this->getJson('/api/clients');

        $response->assertStatus(401);
    }
}
