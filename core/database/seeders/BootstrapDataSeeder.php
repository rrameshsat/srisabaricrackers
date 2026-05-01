<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class BootstrapDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedSettings();
        $this->seedExtraSettings();
        $this->seedMenu();
        $this->seedCurrency();
        $this->seedAdmin();
        $this->seedPaymentSettings();
    }

    protected function seedSettings(): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')->updateOrInsert(
            ['id' => 1],
            array_filter([
                'title' => 'SS Crackers',
                'theme' => 'theme1',
                'currency_direction' => 1,
                'is_guest_checkout' => 1,
                'is_single_checkout' => 0,
                'is_queue_enabled' => 0,
                'is_twilio' => 0,
                'is_decimal' => 1,
                'decimal_separator' => '.',
                'thousand_separator' => ',',
            ], fn ($value) => $value !== null)
        );
    }

    protected function seedExtraSettings(): void
    {
        if (!Schema::hasTable('extra_settings')) {
            return;
        }

        DB::table('extra_settings')->updateOrInsert(['id' => 1], []);
    }

    protected function seedMenu(): void
    {
        if (!Schema::hasTable('menus')) {
            return;
        }

        DB::table('menus')->updateOrInsert(['id' => 1], []);
    }

    protected function seedCurrency(): void
    {
        if (!Schema::hasTable('currencies')) {
            return;
        }

        DB::table('currencies')->updateOrInsert(
            ['id' => 1],
            [
                'name' => 'USD',
                'sign' => '$',
                'value' => 1,
                'is_default' => 1,
            ]
        );
    }

    protected function seedAdmin(): void
    {
        if (!Schema::hasTable('admins')) {
            return;
        }

        DB::table('admins')->updateOrInsert(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'phone' => '0000000000',
                'role_id' => 0,
                'password' => Hash::make('password'),
            ]
        );
    }

    protected function seedPaymentSettings(): void
    {
        if (!Schema::hasTable('payment_settings')) {
            return;
        }

        $gateways = [
            'stripe' => 'Stripe',
            'paypal' => 'Paypal',
            'mollie' => 'Mollie',
            'paystack' => 'Paystack',
            'bank' => 'Bank',
        ];

        foreach ($gateways as $keyword => $name) {
            DB::table('payment_settings')->updateOrInsert(
                ['unique_keyword' => $keyword],
                [
                    'name' => $name,
                    'type' => $name,
                    'information' => json_encode([]),
                    'status' => 0,
                    'text' => $name,
                ]
            );
        }
    }
}
