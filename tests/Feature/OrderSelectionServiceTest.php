<?php

namespace Tests\Unit\Services\OrderSelection;

use App\Models\Order;
use App\Services\OrderSelection\OrderSelectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSelectionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected OrderSelectionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new OrderSelectionService();
    }

    public function testIndexReturnsOrdersWithRelations()
    {
        $order = Order::factory()
            ->hasOrderItems(3)
            ->hasCoordinates(1)
            ->create();

        $query = $this->service->index();
        $orders = $query->get();

        $this->assertNotEmpty($orders);
        $this->assertCount(1, $orders);
        $this->assertTrue($orders->first()->relationLoaded('orderItems'));
        $this->assertTrue($orders->first()->relationLoaded('coordinates'));
    }

    // public function testVoivodeshipsDropdownReturnsUniqueVoivodeships()
    // {
    //     // Arrange
    //     Order::factory()->create(['voivodeship' => 'Mazowieckie']);
    //     Order::factory()->create(['voivodeship' => 'Podlaskie']);
    //     Order::factory()->create(['voivodeship' => 'Mazowieckie']); // Duplicate

    //     // Act
    //     $voivodeships = $this->service->voivodeshipsDropdown();

    //     // Assert
    //     $this->assertCount(2, $voivodeships);
    //     $this->assertContains('Mazowieckie', $voivodeships->pluck('voivodeship')->toArray());
    //     $this->assertContains('Podlaskie', $voivodeships->pluck('voivodeship')->toArray());
    // }
}
