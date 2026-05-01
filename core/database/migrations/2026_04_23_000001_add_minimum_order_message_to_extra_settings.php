<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('extra_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('extra_settings', 'is_min_order_message')) {
                $table->tinyInteger('is_min_order_message')->default(0)->after('is_t4_falsh');
            }

            if (!Schema::hasColumn('extra_settings', 'minimum_order_amount')) {
                $table->decimal('minimum_order_amount', 12, 2)->default(3000)->after('is_min_order_message');
            }

            if (!Schema::hasColumn('extra_settings', 'minimum_order_message')) {
                $table->text('minimum_order_message')->nullable()->after('minimum_order_amount');
            }
        });

        if (Schema::hasTable('extra_settings')) {
            DB::table('extra_settings')->updateOrInsert(
                ['id' => 1],
                [
                    'is_min_order_message' => 0,
                    'minimum_order_amount' => 3000,
                    'minimum_order_message' => 'Minimum order amount must be above Rs.3000',
                ]
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('extra_settings', function (Blueprint $table) {
            if (Schema::hasColumn('extra_settings', 'minimum_order_message')) {
                $table->dropColumn('minimum_order_message');
            }
            if (Schema::hasColumn('extra_settings', 'minimum_order_amount')) {
                $table->dropColumn('minimum_order_amount');
            }
            if (Schema::hasColumn('extra_settings', 'is_min_order_message')) {
                $table->dropColumn('is_min_order_message');
            }
        });
    }
};
