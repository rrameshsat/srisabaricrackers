<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Country;
use App\Models\State as StateModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BillingStateRelationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed India for a known state
        $india = Country::create(['name' => 'India', 'iso_code' => 'IN', 'status' => 1]);
        StateModel::create(['country_id' => $india->id, 'name' => 'Karnataka', 'code' => 'KA', 'status' => 1]);
    }

    public function test_billing_state_relation_exists()
    {
        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone' => '1234567890',
            'email' => 'billing@example.com',
            'password' => Hash::make('password'),
            'bill_address1' => 'Addr',
            'bill_address2' => '',
            'bill_zip' => '00000',
            'bill_city' => 'City',
            'bill_country' => 'India',
            'bill_state_id' => StateModel::first()->id,
        ]);

        $user->refresh();
        $this->assertNotNull($user->billingState);
        $this->assertEquals('Karnataka', $user->billingState->name);
    }
}
