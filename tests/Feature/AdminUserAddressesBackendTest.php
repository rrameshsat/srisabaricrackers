<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Admin;
use App\Models\User;
use App\Models\Country;
use App\Models\State;

class AdminUserAddressesBackendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed India data and related states
        $this->seed(\Database\Seeders\CountrySeeder::class);
    }

    public function test_admin_can_update_india_billing_and_shipping_addresses_via_backend()
    {
        $india = Country::where('name', 'India')->first();
        $state = null;
        if ($india) {
            $state = State::where('country_id', $india->id)->first();
        }

        // Create a backend admin user
        $admin = Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '0000000000',
            'password' => Hash::make('password'),
            'role_id' => 1,
        ]);

        // Create a regular user to update
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '1234567890',
            'email' => 'testuser@example.com',
            'password' => Hash::make('password'),
            'bill_address1' => 'Old Addr',
            'bill_city' => 'Old City',
            'bill_country' => 'India',
        ]);

        $payload = [
            'bill_address1' => 'Addr 1',
            'bill_address2' => 'Suite 100',
            'bill_city' => 'New Delhi',
            'bill_zip' => '110001',
            'bill_country' => 'India',
        ];
        if ($state) {
            $payload['bill_state_id'] = $state->id;
            $payload['ship_state_id'] = $state->id;
        }
        $payload += [
            'ship_address1' => 'Ship Addr',
            'ship_address2' => '',
            'ship_city' => 'New Delhi',
            'ship_zip' => '110001',
            'ship_country' => 'India',
        ];

        // Act as admin on the admin update route
        $response = $this->actingAs($admin, 'admin')->put(route('back.user.update', $user->id), $payload);

        $response->assertStatus(302);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'bill_country' => 'India',
            'bill_state_id' => $state ? $state->id : null,
            'ship_country' => 'India',
            'ship_state_id' => $state ? $state->id : null,
        ]);
    }
}
