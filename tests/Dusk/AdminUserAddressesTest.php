<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminUserAddressesTest extends DuskTestCase
{
    public function test_admin_can_update_india_billing_and_shipping_addresses()
    {
        // Prepare India and a state if available
        $india = DB::table('countries')->where('name', 'India')->first();
        $state = null;
        if ($india) {
            $state = DB::table('states')->where('country_id', $india->id)->first();
        }
        // Pick a user to update (admin will edit the first user)
        $user = User::first();

        $this->browse(function (Browser $browser) use ($user, $india, $state) {
            // Admin login
            $browser->visit('/admin/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->pause(800);

            // Open user detail/edit page
            $browser->visit('/back/user/' . $user->id)
                // Billing: India
                ->select('bill_country', $india ? 'India' : '')
                ->pause(600)
                ->waitFor('#admin-bill-state', 2000)
                ;
            if ($state) {
                $browser->select('bill_state_id', (string) $state->id);
            }
            // Shipping: India
            $browser->select('ship_country', $india ? 'India' : '')
                ->pause(600)
                ->waitFor('#admin-ship-state', 2000);
            if ($state) {
                $browser->select('ship_state_id', (string) $state->id);
            }
            // Submit
            $browser->press('Submit')
                ->pause(1000);
        });

        // DB assertions
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'bill_country' => $india ? 'India' : null,
            'bill_state_id' => $state ? $state->id : null,
            'ship_country' => $india ? 'India' : null,
            'ship_state_id' => $state ? $state->id : null,
        ]);
    }
}
