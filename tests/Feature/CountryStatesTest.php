<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Country;
use App\Models\State;
use Database\Seeders\CountrySeeder;

class CountryStatesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed India and its states for the test
        $this->seed(CountrySeeder::class);
    }

    public function test_country_states_endpoint_returns_india_states()
    {
        $india = Country::where('name','India')->first();
        $this->assertNotNull($india, 'India country not seeded');

        $response = $this->getJson('/country-states/' . $india->id);
        $response->assertStatus(200);
        $response->assertJsonStructure(["*" => ["id","name"]]);
        $states = State::where('country_id', $india->id)->get();
        foreach ($states as $s) {
            $response->assertJsonFragment(['id' => $s->id, 'name' => $s->name]);
        }
    }
}
