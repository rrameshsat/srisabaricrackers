<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\DB;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminBillingStateBindingTest extends DuskTestCase
{
    public function test_admin_billing_state_binding_with_non_india_country()
    {
        // Seed USA country and a state
        $usa = DB::table('countries')->insertGetId(['name' => 'United States', 'iso_code' => 'US', 'status' => 1]);
        $stateId = DB::table('states')->insertGetId(['country_id' => $usa, 'name' => 'California', 'code' => 'CA', 'status' => 1]);

        // Create a user to edit
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '1234567890',
            'email' => 'dusertest@example.com',
            'password' => Hash::make('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user, $usa, $stateId) {
            // Admin login
            $browser->visit('/admin/login')
                ->type('email', 'admin@example.com')
                ->type('password', 'password')
                ->press('Login')
                ->pause(800)
                // Open user detail/edit page
                ->visit('/back/user/' . $user->id)
                // Billing: United States
                ->select('bill_country', 'United States')
                ->pause(600)
                ->waitFor('#admin-bill-state', 2000)
                ->select('bill_state_id', (string) $stateId)
                // Submit
                ->press('Submit')
                ->pause(1000);
        });

        // DB assertions
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'bill_country' => 'United States',
            'bill_state_id' => $stateId,
        ]);
    }
}
