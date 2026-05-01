<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class CountryStateDropdownTest extends DuskTestCase
{
    public function test_india_state_dropdown_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                    ->select('bill_country', 'India')
                    ->pause(1000)
                    ->assertPresent('select#reg-bill-state')
                    ->assertNotEmpty('select#reg-bill-state option')
                    ->screenshot('india-states');
        });
    }
}
