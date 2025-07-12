<?php

namespace Tests\Feature;

use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    //use RefreshDatabase;

    /** @test */
    public function puede_listar_los_clientes()
    {
        Customer::factory()->count(3)->create();

        $response = $this->getJson('/api/customers');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         '*' => ['id', 'name', 'email', 'phone', 'created_at', 'updated_at']
                     ]
                 ]);
    }

    /** @test */
    public function puede_crear_un_cliente()
    {
        $data = [
            'name' => 'Juan',
            'email' => 'juan@test.com',
            'phone' => '0999999999'
        ];

        $response = $this->postJson('/api/customers', $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Creado con éxito'
                 ]);

        $this->assertDatabaseHas('customers', $data);
    }

    /** @test */
    public function no_puede_crear_cliente_con_datos_invalidos()
    {
        $response = $this->postJson('/api/customers', [
            'name' => '',
            'email' => 'no-es-un-email',
            'phone' => ''
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => false
                 ])
                 ->assertJsonStructure(['errors']);
    }

    /** @test */
    public function puede_actualizar_un_cliente()
    {
        $customer = Customer::factory()->create();

        $data = [
            'name' => 'Actualizado',
            'email' => 'nuevo@email.com',
            'phone' => '0988888888'
        ];

        $response = $this->putJson("/api/customers/{$customer->id}", $data);

        $response->assertStatus(200)
                 ->assertJson([
                     'status' => true,
                     'message' => 'Actualizado con éxito'
                 ]);

        $this->assertDatabaseHas('customers', array_merge(['id' => $customer->id], $data));
    }

    /** @test */
    public function no_puede_actualizar_cliente_inexistente()
    {
        $response = $this->putJson('/api/customers/999', [
            'name' => 'No existe',
            'email' => 'x@x.com',
            'phone' => '0000'
        ]);

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => false,
                     'message' => 'El cliente no existe'
                 ]);
    }

    /** @test */
    public function no_puede_eliminar_cliente_inexistente()
    {
        $response = $this->deleteJson('/api/customers/999');

        $response->assertStatus(400)
                 ->assertJson([
                     'status' => false,
                     'message' => 'El cliente no existe'
                 ]);
    }
}
