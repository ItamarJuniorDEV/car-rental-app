<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandTest extends TestCase
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

    public function test_pode_listar_marcas()
    {
        Brand::create(['name' => 'Toyota', 'image' => 'toyota.png']);
        Brand::create(['name' => 'Honda', 'image' => 'honda.png']);

        $response = $this->actingAs($this->operador, 'sanctum')->getJson('/api/brands');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonCount(2, 'data');
    }

    public function test_admin_pode_criar_marca()
    {
        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/brands', [
            'name' => 'Volkswagen',
            'image' => 'vw.png',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('brands', ['name' => 'Volkswagen']);
    }

    public function test_operador_nao_pode_criar_marca()
    {
        $response = $this->actingAs($this->operador, 'sanctum')->postJson('/api/brands', [
            'name' => 'Volkswagen',
            'image' => 'vw.png',
        ]);

        $response->assertStatus(403);
    }

    public function test_nome_de_marca_deve_ser_unico()
    {
        Brand::create(['name' => 'Toyota', 'image' => 'toyota.png']);

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/brands', [
            'name' => 'Toyota',
            'image' => 'outra.png',
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_pode_atualizar_marca()
    {
        $brand = Brand::create(['name' => 'Toiota', 'image' => 'toyota.png']);

        $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/brands/{$brand->id}", [
            'name' => 'Toyota',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('brands', ['id' => $brand->id, 'name' => 'Toyota']);
    }

    public function test_operador_nao_pode_atualizar_marca()
    {
        $brand = Brand::create(['name' => 'Toyota', 'image' => 'toyota.png']);

        $response = $this->actingAs($this->operador, 'sanctum')->putJson("/api/brands/{$brand->id}", [
            'name' => 'Toyota 2',
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_pode_deletar_marca()
    {
        $brand = Brand::create(['name' => 'Toyota', 'image' => 'toyota.png']);

        $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/brands/{$brand->id}");

        $response->assertOk();
        $this->assertSoftDeleted('brands', ['id' => $brand->id]);
    }

    public function test_operador_nao_pode_deletar_marca()
    {
        $brand = Brand::create(['name' => 'Toyota', 'image' => 'toyota.png']);

        $response = $this->actingAs($this->operador, 'sanctum')->deleteJson("/api/brands/{$brand->id}");

        $response->assertStatus(403);
    }

    public function test_sem_autenticacao_retorna_401()
    {
        $response = $this->postJson('/api/brands', [
            'name' => 'Toyota',
            'image' => 'toyota.png',
        ]);

        $response->assertStatus(401);
    }

    public function test_marca_inexistente_retorna_404()
    {
        $response = $this->actingAs($this->operador, 'sanctum')->getJson('/api/brands/9999');

        $response->assertStatus(404);
    }
}
