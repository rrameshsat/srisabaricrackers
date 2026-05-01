<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('extra_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('extra_settings', 'is_state_delivery_charge')) {
                $table->tinyInteger('is_state_delivery_charge')->default(0)->after('minimum_order_message');
            }
        });
    }

    public function down(): void
    {
        Schema::table('extra_settings', function (Blueprint $table) {
            if (Schema::hasColumn('extra_settings', 'is_state_delivery_charge')) {
                $table->dropColumn('is_state_delivery_charge');
            }
        });
    }
};