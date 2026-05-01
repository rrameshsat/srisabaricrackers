<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }
        if (!Schema::hasColumn('users', 'ship_state_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('ship_state_id')->nullable()->constrained('states')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'ship_state_id')) {
                $table->dropForeign(['ship_state_id']);
                $table->dropColumn('ship_state_id');
            }
        });
    }
};
