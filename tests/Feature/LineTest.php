<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Line;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LineTest extends TestCase
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

    public function test_pode_listar_linhas()
    {
        Line::factory()->count(3)->create();

        $response = $this->actingAs($this->operador, 'sanctum')->getJson('/api/lines');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonCount(3, 'data');
    }

    public function test_filtro_por_marca()
    {
        $brand = Brand::factory()->create();
        Line::factory()->count(2)->create(['brand_id' => $brand->id]);
        Line::factory()->create();

        $response = $this->actingAs($this->operador, 'sanctum')->getJson("/api/lines?brand_id={$brand->id}");

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_admin_pode_criar_linha()
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/lines', [
            'brand_id' => $brand->id,
            'name' => 'Corolla',
            'image' => 'corolla.png',
            'door_count' => 4,
            'seats' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('lines', ['name' => 'Corolla']);
    }

    public function test_operador_nao_pode_criar_linha()
    {
        $brand = Brand::factory()->create();

        $response = $this->actingAs($this->operador, 'sanctum')->postJson('/api/lines', [
            'brand_id' => $brand->id,
            'name' => 'Corolla',
            'image' => 'corolla.png',
            'door_count' => 4,
            'seats' => 5,
            'air_bag' => true,
            'abs' => true,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_pode_atualizar_linha()
    {
        $line = Line::factory()->create(['name' => 'Corolaa']);

        $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/lines/{$line->id}", [
            'name' => 'Corolla',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('lines', ['id' => $line->id, 'name' => 'Corolla']);
    }

    public function test_operador_nao_pode_atualizar_linha()
    {
        $line = Line::factory()->create();

        $response = $this->actingAs($this->operador, 'sanctum')->putJson("/api/lines/{$line->id}", [
            'name' => 'Novo Nome',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_pode_deletar_linha()
    {
        $line = Line::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/lines/{$line->id}");

        $response->assertOk();
        $this->assertSoftDeleted('lines', ['id' => $line->id]);
    }

    public function test_operador_nao_pode_deletar_linha()
    {
        $line = Line::factory()->create();

        $response = $this->actingAs($this->operador, 'sanctum')->deleteJson("/api/lines/{$line->id}");

        $response->assertStatus(403);
    }

    public function test_linha_inexistente_retorna_404()
    {
        $response = $this->actingAs($this->operador, 'sanctum')->getJson('/api/lines/9999');

        $response->assertStatus(404);
    }

    public function test_sem_autenticacao_retorna_401()
    {
        $response = $this->getJson('/api/lines');

        $response->assertStatus(401);
    }
}
