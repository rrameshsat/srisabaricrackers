<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\CountrySeeder;

class IndiaStatesEdgeCaseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed India with its states for the positive path
        $this->seed(CountrySeeder::class);
    }

    public function test_registration_without_bill_state_id_for_india_fails_validation()
    {
        // Prepare payload omitting bill_state_id for India
        $india = \DB::table('countries')->where('name','India')->first();
        $payload = [
            'first_name' => 'Edge',
            'last_name' => 'Case',
            'phone' => '1111111111',
            'email' => 'edge@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'honeypot' => '',
            'bill_address1' => 'Addr 1',
            'bill_city' => 'City',
            'bill_country' => 'India',
        ];
        if ($india) {
            // Do not send bill_state_id
        }
        // Shipping fields
        $payload['ship_address1'] = 'Ship Addr';
        $payload['ship_city'] = 'Ship City';
        $payload['ship_zip'] = '12345';
        $payload['ship_country'] = 'India';
        // Do not include ship_state_id

        $response = $this->post('/register-submit', $payload);
        $response->assertStatus(422);
    }

    public function test_registration_with_bill_state_id_for_india_succeeds()
    {
        $india = \DB::table('countries')->where('name','India')->first();
        $state = \DB::table('states')->where('country_id', $india->id)->first();
        $payload = [
            'first_name' => 'Edge',
            'last_name' => 'Case',
            'phone' => '2222222222',
            'email' => 'edge2@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'honeypot' => '',
            'bill_address1' => 'Addr 1',
            'bill_city' => 'City',
            'bill_country' => 'India',
            'bill_state_id' => $state->id ?? null,
        ];
        // Shipping fields
        $payload['ship_address1'] = 'Ship Addr';
        $payload['ship_city'] = 'Ship City';
        $payload['ship_zip'] = '12345';
        $payload['ship_country'] = 'India';
        if ($state) {
            $payload['ship_state_id'] = $state->id;
        }

        $response = $this->post('/register-submit', $payload);
        $response->assertStatus(302);
    }
}
