<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckoutRoutesTest extends TestCase
{
    public function test_checkout_routes_are_registered(): void
    {
        $this->assertTrue($this->app['router']->has('front.checkout'));
        $this->assertTrue($this->app['router']->has('front.checkout.submit'));
        $this->assertTrue($this->app['router']->has('front.paytm.submit'));
    }
}
