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
use Illuminate\Support\Facades\Http;

class OrderServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testGetCoordinatesWithOrders()
    {
        $service = new OrderService();

        $order = Order::factory()->create([
            'delivery_address' => 'Test Address',
        ]);

        Coordinates::factory()->create([
            'latitude' => 52.1,
            'longitude' => 21.2,
            'order_id' => $order->id,
        ]);

        SelectedOrder::factory()->create([
            'order_id' => $order->id,
        ]);

        $coordinates = $service->getCoordinatesWithOrders();

        $this->assertNotEmpty($coordinates);
        $this->assertArrayHasKey('latitude', $coordinates[1]);
        $this->assertEquals(52.1, $coordinates[1]['latitude']);
    }

    public function testFetchCoordinatesFromApi()
    {
        Http::fake([
            'https://api.tomtom.com/*' => Http::response([
                'results' => [
                    [
                        'position' => [
                            'lat' => 52.2297,
                            'lon' => 21.0122,
                        ]
                    ]
                ]
            ], 200),
        ]);

        $service = new \App\Services\Order\OrderService();
        $coordinates = $service->fetchCoordinatesFromApi('Warsaw, Poland');

        $this->assertNotNull($coordinates);
        $this->assertEquals(52.2297, $coordinates['lat']);
        $this->assertEquals(21.0122, $coordinates['lon']);
    }
}