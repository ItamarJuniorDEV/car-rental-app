<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_pode_listar_marcas()
    {
        Brand::create(['name' => 'Toyota', 'image' => 'toyota.png']);
        Brand::create(['name' => 'Honda', 'image' => 'honda.png']);

        $response = $this->withToken($this->user->api_token)->getJson('/api/brands');

        $response->assertOk()->assertJsonCount(2, 'data');
    }

    public function test_pode_criar_marca()
    {
        $response = $this->withToken($this->user->api_token)->postJson('/api/brands', [
            'name'  => 'Volkswagen',
            'image' => 'vw.png',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('brands', ['name' => 'Volkswagen']);
    }

    public function test_nome_de_marca_deve_ser_unico()
    {
        Brand::create(['name' => 'Toyota', 'image' => 'toyota.png']);

        $response = $this->withToken($this->user->api_token)->postJson('/api/brands', [
            'name'  => 'Toyota',
            'image' => 'outra.png',
        ]);

        $response->assertStatus(422);
    }

    public function test_pode_atualizar_marca()
    {
        $brand = Brand::create(['name' => 'Toiota', 'image' => 'toyota.png']);

        $response = $this->withToken($this->user->api_token)->putJson("/api/brands/{$brand->id}", [
            'name' => 'Toyota',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('brands', ['id' => $brand->id, 'name' => 'Toyota']);
    }

    public function test_pode_deletar_marca()
    {
        $brand = Brand::create(['name' => 'Toyota', 'image' => 'toyota.png']);

        $response = $this->withToken($this->user->api_token)->deleteJson("/api/brands/{$brand->id}");

        $response->assertOk();
        $this->assertSoftDeleted('brands', ['id' => $brand->id]);
    }

    public function test_sem_autenticacao_retorna_401()
    {
        $response = $this->postJson('/api/brands', [
            'name'  => 'Toyota',
            'image' => 'toyota.png',
        ]);

        $response->assertStatus(401);
    }

    public function test_marca_inexistente_retorna_404()
    {
        $response = $this->withToken($this->user->api_token)->getJson('/api/brands/9999');

        $response->assertStatus(404);
    }
}
