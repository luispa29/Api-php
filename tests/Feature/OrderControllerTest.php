<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Order;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderControllerTest extends TestCase
{
    //use RefreshDatabase;

    public function test_index_returns_paginated_orders()
    {
        //Customer::factory()->hasOrders(3)->create();

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status', 'current_page', 'data', 'last_page', 'per_page', 'total'
                 ]);
    }

    public function test_store_creates_new_order()
    {
        $customer = Customer::factory()->create();

        $payload = [
            'name' => 'Orden de prueba',
            'description' => 'Descripción',
            'price' => 100,
            'weight' => 2.5,
            'customer_id' => $customer->id
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201)
                 ->assertJson(['status' => true, 'message' => 'Creado con éxito']);

        $this->assertDatabaseHas('orders', ['name' => 'Orden de prueba']);
    }



    public function test_update_modifies_existing_order()
    {
        $order = Order::factory()->create();
        $payload = [
            'name' => 'Nuevo nombre',
            'description' => 'Actualizado',
            'price' => 200,
            'weight' => 3,
            'customer_id' => $order->customer_id
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $payload);

        $response->assertStatus(200)
                 ->assertJson(['status' => true, 'message' => 'Actualizado con éxito']);
    }

    // public function test_destroy_deletes_order()
    // {
    //     $order = Order::factory()->create();

    //     $response = $this->deleteJson("/api/orders/{$order->id}");

    //     $response->assertStatus(200)
    //              ->assertJson(['status' => true, 'message' => 'Eliminado con éxito']);
    // }

    public function test_update_status_changes_order_status()
    {
        $order = Order::factory()->create(['status' => 'pendiente']);

        $response = $this->putJson("/api/orders/updateStatus/{$order->id}", [
            'status' => 'enviado'
        ]);

        $response->assertStatus(200)
                 ->assertJson(['status' => true]);

        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'enviado']);
    }

    public function test_get_filters_returns_unique_status_and_customers()
    {

        $response = $this->getJson('/api/orders/getFilters');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => ['status', 'customers']
                 ]);
    }





    public function test_dashboard_returns_statistics()
    {
        Customer::factory()->count(2)->create();
        Order::factory()->count(3)->create();

        $response = $this->getJson('/api/orders/dashboard');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'totalOrders',
                     'complete',
                     'earring',
                     'activeCustomers',
                     'activity'
                 ]);
    }
}
