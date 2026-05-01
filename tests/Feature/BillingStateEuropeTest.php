<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Country;
use App\Models\State as StateModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BillingStateEuropeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (env('RUN_EURO_TESTS') != true) {
            $this->markTestSkipped('European tests are disabled in this environment.');
        }
        // Seed Europe data
        // Germany and Bavaria (state) will be created in this test for isolation
    }

    public function test_billing_state_binding_for_european_country()
    {
        // Create Germany and Bavaria for the test
        $ger = Country::create(['name' => 'Germany', 'iso_code' => 'DE', 'status' => 1]);
        $bay = StateModel::create(['country_id' => $ger->id, 'name' => 'Bavaria', 'code' => 'BY', 'status' => 1]);

        $user = User::create([
            'first_name' => 'Hans',
            'last_name' => 'Meyer',
            'phone' => '0301234567',
            'email' => 'hans@example.de',
            'password' => Hash::make('password'),
            'bill_address1' => 'Unter den Linden 1',
            'bill_city' => 'Berlin',
            'bill_country' => 'Germany',
            'bill_state_id' => $bay->id,
        ]);

        $user->refresh();
        $this->assertNotNull($user->billingState);
        $this->assertEquals('Bavaria', $user->billingState->name);
    }
}
