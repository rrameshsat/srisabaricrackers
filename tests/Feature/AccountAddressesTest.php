<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Country;
use App\Models\State as StateModel;
use Database\Seeders\CountrySeeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\WithFaker;

class AccountAddressesTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed India data
        $this->seed(CountrySeeder::class);
    }

    public function test_update_billing_and_shipping_addresses_with_india_state()
    {
        $india = Country::where('name','India')->first();
        $state = StateModel::where('country_id',$india->id)->first();
        $user = \App\Models\User::factory ? \App\Models\User::factory()->create() : \App\Models\User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '1234567890',
            'email' => 'test+acct@example.com',
            'password' => Hash::make('password'),
            'state_id' => $state ? $state->id : null,
            'bill_address1' => 'Addr 1',
            'bill_city' => 'City',
            'bill_country' => 'India',
        ]);

        $this->be($user);

        // Update billing
        $billing = [
            'bill_address1' => 'New Addr 1',
            'bill_city' => 'New City',
            'bill_zip' => '12345',
            'bill_country' => 'India',
            'state_id' => $state->id,
        ];
        $this->post('/billing/addresses', $billing)->assertStatus(302);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'bill_address1' => 'New Addr 1', 'bill_city' => 'New City', 'bill_country' => 'India', 'bill_state_id' => $state->id]);

        // Update shipping
        $shipping = [
            'ship_address1' => 'Shipping Addr',
            'ship_city' => 'Ship City',
            'ship_zip' => '54321',
            'ship_country' => 'India',
            'ship_state_id' => $state->id,
        ];
        $this->post('/shipping/addresses', $shipping)->assertStatus(302);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'ship_address1' => 'Shipping Addr', 'ship_city' => 'Ship City', 'ship_state_id' => $state->id]);
    }
}
