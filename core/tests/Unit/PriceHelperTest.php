<?php

namespace Tests\Unit;

use App\Helpers\PriceHelper;
use PHPUnit\Framework\TestCase;

class PriceHelperTest extends TestCase
{
    public function test_get_item_id_supports_composite_cart_keys(): void
    {
        $this->assertSame(15, PriceHelper::GetItemId('15-blue-xl'));
        $this->assertSame(9, PriceHelper::GetItemId(9));
    }
}
