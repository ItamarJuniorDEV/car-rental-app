<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Car;
use App\Models\Client;
use App\Models\Line;
use App\Models\Rental;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RentalTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Client $client;
    private Car $car;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();

        $brand = Brand::create(['name' => 'Toyota', 'image' => 'toyota.png']);

        $line = Line::create([
            'brand_id'   => $brand->id,
            'name'       => 'Corolla',
            'image'      => 'corolla.png',
            'door_count' => 4,
            'seats'      => 5,
            'air_bag'    => true,
            'abs'        => true,
        ]);

        $this->car = Car::create([
            'line_id'   => $line->id,
            'plate'     => 'ABC-1D23',
            'available' => true,
            'km'        => 15000,
        ]);

        $this->client = Client::factory()->create();
    }

    private function dadosLocacao(array $override = []): array
    {
        return array_merge([
            'client_id'                => $this->client->id,
            'car_id'                   => $this->car->id,
            'period_start_date'        => '2026-03-01 08:00:00',
            'period_expected_end_date' => '2026-03-05 08:00:00',
            'daily_rate'               => 200.00,
            'initial_km'               => 15000,
        ], $override);
    }

    public function test_pode_criar_locacao()
    {
        $response = $this->withToken($this->user->api_token)
            ->postJson('/api/rentals', $this->dadosLocacao());

        $response->assertStatus(201);
        $this->assertFalse($this->car->fresh()->available);
    }

    public function test_carro_indisponivel_nao_pode_ser_locado()
    {
        $this->car->update(['available' => false]);

        $response = $this->withToken($this->user->api_token)
            ->postJson('/api/rentals', $this->dadosLocacao());

        $response->assertStatus(422);
    }

    public function test_km_final_menor_que_inicial_retorna_erro()
    {
        $rental = $this->criarLocacao();

        $response = $this->withToken($this->user->api_token)
            ->putJson("/api/rentals/{$rental->id}", [
                'period_actual_end_date' => '2026-03-05 08:00:00',
                'final_km'               => 14000,
            ]);

        $response->assertStatus(422);
    }

    public function test_data_devolucao_anterior_ao_inicio_retorna_erro()
    {
        $rental = $this->criarLocacao();

        $response = $this->withToken($this->user->api_token)
            ->putJson("/api/rentals/{$rental->id}", [
                'period_actual_end_date' => '2026-02-28 08:00:00',
                'final_km'               => 15500,
            ]);

        $response->assertStatus(422);
    }

    public function test_devolucao_libera_carro_e_atualiza_km()
    {
        $rental = $this->criarLocacao();

        $this->withToken($this->user->api_token)
            ->putJson("/api/rentals/{$rental->id}", [
                'period_actual_end_date' => '2026-03-05 08:00:00',
                'final_km'               => 15800,
            ])
            ->assertOk();

        $this->assertTrue($this->car->fresh()->available);
        $this->assertEquals(15800, $this->car->fresh()->km);
    }

    public function test_devolucao_com_atraso_calcula_multa()
    {
        $rental = $this->criarLocacao();

        $response = $this->withToken($this->user->api_token)
            ->putJson("/api/rentals/{$rental->id}", [
                'period_actual_end_date' => '2026-03-07 08:00:00',
                'final_km'               => 15800,
            ]);

        $response->assertOk();

        $data = $response->json('data');
        $this->assertNotNull($data['late_fee']);
        $this->assertEquals(200.0, $data['late_fee']);
    }

    public function test_nao_pode_deletar_carro_com_locacao_ativa()
    {
        $this->criarLocacao();

        $response = $this->withToken($this->user->api_token)
            ->deleteJson("/api/cars/{$this->car->id}");

        $response->assertStatus(422);
    }

    public function test_nao_pode_deletar_cliente_com_locacao_ativa()
    {
        $this->criarLocacao();

        $response = $this->withToken($this->user->api_token)
            ->deleteJson("/api/clients/{$this->client->id}");

        $response->assertStatus(422);
    }

    private function criarLocacao(): Rental
    {
        $response = $this->withToken($this->user->api_token)
            ->postJson('/api/rentals', $this->dadosLocacao());

        return Rental::find($response->json('data.id'));
    }
}
