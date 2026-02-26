<?php

namespace Tests\Feature;

use App\Models\Car;
use App\Models\Line;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CarTest extends TestCase
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

    public function test_pode_listar_carros()
    {
        Car::factory()->count(3)->create();

        $response = $this->actingAs($this->operador, 'sanctum')->getJson('/api/cars');

        $response->assertOk()
            ->assertJsonStructure(['data', 'links', 'meta'])
            ->assertJsonCount(3, 'data');
    }

    public function test_filtro_por_placa()
    {
        Car::factory()->create(['plate' => 'ABC-1D23']);
        Car::factory()->create(['plate' => 'XYZ-9Z99']);

        $response = $this->actingAs($this->operador, 'sanctum')->getJson('/api/cars?plate=ABC');

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_filtro_disponiveis()
    {
        Car::factory()->create(['available' => true]);
        Car::factory()->create(['available' => false]);
        Car::factory()->create(['available' => false]);

        $response = $this->actingAs($this->operador, 'sanctum')->getJson('/api/cars?available=true');

        $response->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_admin_pode_criar_carro()
    {
        $line = Line::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')->postJson('/api/cars', [
            'line_id' => $line->id,
            'plate' => 'ABC-1D23',
            'available' => true,
            'km' => 10000,
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('cars', ['plate' => 'ABC-1D23']);
    }

    public function test_operador_nao_pode_criar_carro()
    {
        $line = Line::factory()->create();

        $response = $this->actingAs($this->operador, 'sanctum')->postJson('/api/cars', [
            'line_id' => $line->id,
            'plate' => 'ABC-1D23',
            'available' => true,
            'km' => 10000,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_pode_atualizar_carro()
    {
        $car = Car::factory()->create(['km' => 10000]);

        $response = $this->actingAs($this->admin, 'sanctum')->putJson("/api/cars/{$car->id}", [
            'km' => 12000,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('cars', ['id' => $car->id, 'km' => 12000]);
    }

    public function test_operador_nao_pode_atualizar_carro()
    {
        $car = Car::factory()->create();

        $response = $this->actingAs($this->operador, 'sanctum')->putJson("/api/cars/{$car->id}", [
            'km' => 99999,
        ]);

        $response->assertStatus(403);
    }

    public function test_admin_pode_deletar_carro()
    {
        $car = Car::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')->deleteJson("/api/cars/{$car->id}");

        $response->assertOk();
        $this->assertSoftDeleted('cars', ['id' => $car->id]);
    }

    public function test_operador_nao_pode_deletar_carro()
    {
        $car = Car::factory()->create();

        $response = $this->actingAs($this->operador, 'sanctum')->deleteJson("/api/cars/{$car->id}");

        $response->assertStatus(403);
    }

    public function test_carro_inexistente_retorna_404()
    {
        $response = $this->actingAs($this->operador, 'sanctum')->getJson('/api/cars/9999');

        $response->assertStatus(404);
    }

    public function test_sem_autenticacao_retorna_401()
    {
        $response = $this->getJson('/api/cars');

        $response->assertStatus(401);
    }
}
