<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add column as nullable first (without foreign key)
        if (!Schema::hasColumn('states', 'country_id')) {
            Schema::table('states', function (Blueprint $table) {
                $table->unsignedBigInteger('country_id')->nullable()->after('id');
            });
        }

        // Find India country
        $india = DB::table('countries')->where('name', 'India')->first();
        
        if ($india) {
            // Update all states that don't have a country_id to belong to India
            DB::table('states')->whereNull('country_id')->update(['country_id' => $india->id]);
            
            // Now add foreign key constraint
            Schema::table('states', function (Blueprint $table) {
                $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::table('states', function (Blueprint $table) {
            $table->dropForeign(['country_id']);
            $table->dropColumn('country_id');
        });
    }
};