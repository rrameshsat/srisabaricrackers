<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Country;
use App\Models\State as StateModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BillingStateNonIndiaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed India data for contrast, but also create USA data here
        // Allow a toggle to run USA tests in non-India environments; default to skipped
        if (env('RUN_USA_TESTS') != true) {
            $this->markTestSkipped('USA tests are disabled in this environment.');
        }
        $this->seed(\Database\Seeders\CountrySeeder::class);
    }

    public function test_billing_state_relation_for_non_india_country()
    {
        // Create United States country and a state (California)
        $usa = Country::create(['name' => 'United States', 'iso_code' => 'US', 'status' => 1]);
        $cal = StateModel::create(['country_id' => $usa->id, 'name' => 'California', 'code' => 'CA', 'status' => 1]);

        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '1234567890',
            'email' => 'ususer@example.com',
            'password' => Hash::make('password'),
            'bill_address1' => '1 Main St',
            'bill_city' => 'San Francisco',
            'bill_country' => 'United States',
            'bill_state_id' => $cal->id,
        ]);

        $user->refresh();
        $this->assertNotNull($user->billingState);
        $this->assertEquals('California', $user->billingState->name);
    }
}
