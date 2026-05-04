<?php
namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\UpiConfig;

class UpiConfigTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_and_update_upi_config()
    {
        // Initial store
        $response = $this->post('/upi/config', [
            'enabled' => 'on',
            'merchant_id' => 'TEST_MERCHANT',
            'endpoint' => 'https://upi.example/api',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('upi_configs', ['merchant_id' => 'TEST_MERCHANT']);

        // Update
        $response = $this->post('/upi/config', [
            'enabled' => 'on',
            'merchant_id' => 'UPDATED_MERCHANT',
            'endpoint' => 'https://upi.example/new-api',
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseHas('upi_configs', ['merchant_id' => 'UPDATED_MERCHANT']);
    }

    public function test_can_show_config_page()
    {
        \App\Models\UpiConfig::create(['enabled' => false, 'merchant_id' => 'TEST', 'endpoint' => 'https://upi.example']);
        $response = $this->get('/upi/config');
        $response->assertStatus(200);
    }
}
