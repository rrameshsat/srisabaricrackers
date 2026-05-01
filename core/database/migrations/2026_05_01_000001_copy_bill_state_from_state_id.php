<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }
        // Copy existing state_id into bill_state_id where bill_state_id is null
        DB::table('users')
            ->whereNull('bill_state_id')
            ->whereNotNull('state_id')
            ->update(['bill_state_id' => DB::raw('state_id')]);
    }

    public function down(): void
    {
        // Data migrations are typically not reversible; no-op
    }
};
