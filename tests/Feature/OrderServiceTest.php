<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Services\Order\OrderService;
use App\Services\Packing\PackingService;
use App\Models\SelectedOrder;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coordinates;
use App\Models\Rack;
use App\Models\Trailer;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    // public function testGetCoordinatesWithOrders()
    // {
    //     $service = new OrderService();

    //     $order = Order::factory()->create([
    //         'delivery_address' => 'Test Address',
    //     ]);

    //     Coordinates::factory()->create([
    //         'latitude' => 52.1,
    //         'longitude' => 21.2,
    //         'order_id' => $order->id,
    //     ]);

    //     SelectedOrder::factory()->create([
    //         'order_id' => $order->id,
    //     ]);

    //     $coordinates = $service->getCoordinatesWithOrders();

    //     $this->assertNotEmpty($coordinates);
    //     $this->assertArrayHasKey('latitude', $coordinates[1]);
    //     $this->assertEquals(52.1, $coordinates[1]['latitude']);
    // }

}
