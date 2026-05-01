<?php

namespace Tests\Unit;

use App\Models\State;
use App\Services\OrderPricingService;
use PHPUnit\Framework\TestCase;

class OrderPricingServiceTest extends TestCase
{
    public function test_extract_item_id_uses_cart_key_prefix(): void
    {
        $service = new OrderPricingService();

        $this->assertSame(42, $service->extractItemId('42-red-large'));
        $this->assertSame(7, $service->extractItemId(7));
    }

    public function test_cart_requires_shipping_only_for_normal_items(): void
    {
        $service = new OrderPricingService();

        $this->assertTrue($service->cartRequiresShipping([
            ['item_type' => 'digital'],
            ['item_type' => 'normal'],
        ]));

        $this->assertFalse($service->cartRequiresShipping([
            ['item_type' => 'digital'],
            ['item_type' => 'license'],
        ]));
    }

    public function test_state_charge_supports_fixed_and_percentage_states(): void
    {
        $service = new OrderPricingService();

        $fixed = new State(['type' => 'fixed', 'price' => 15]);
        $percentage = new State(['type' => 'percentage', 'price' => 10]);

        $this->assertSame(15.0, $service->stateCharge($fixed, 100));
        $this->assertSame(10.0, $service->stateCharge($percentage, 100));
        $this->assertSame(0.0, $service->stateCharge(null, 100));
    }
}
