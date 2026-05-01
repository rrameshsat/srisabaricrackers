<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use App\Models\Country;
use App\Models\State as StateModel;
use Database\Seeders\CountrySeeder;
use App\Models\Setting;
use App\Repositories\Front\UserRepository;
use Mockery;

class RegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed India data
        $this->seed(CountrySeeder::class);
    }

    public function test_registration_calls_repository_and_redirects()
    {
        // Prepare India state for billing (if present)
        $india = Country::where('name','India')->first();
        $state = null;
        if ($india) {
            $state = StateModel::where('country_id',$india->id)->first();
        }

        // Mock repository
        $mock = Mockery::mock(UserRepository::class);
        $mock->shouldReceive('register')->once();
        $this->app()->instance(UserRepository::class, $mock);

        $payload = [
            'first_name' => 'Alice',
            'last_name' => 'Tester',
            'phone' => '9999999999',
            'email' => 'alice@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'honeypot' => '',
            // Billing fields (optional on registration, included to satisfy validation)
            'bill_address1' => '123 Street',
            'bill_city' => 'New Delhi',
            'bill_country' => 'India',
        ];
        // If India state is available, pre-populate billing/shipping states
        if ($state) {
            $payload['bill_state_id'] = $state->id;
            $payload['ship_state_id'] = $state->id;
        }
        // Shipping fields
        $payload['ship_address1'] = '123 Street';
        $payload['ship_city'] = 'New Delhi';
        $payload['ship_zip'] = '110001';
        $payload['ship_country'] = 'India';

        $response = $this->post('/register-submit', $payload);
        $response->assertStatus(302);
    }
}
