<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'show_in_menu')) {
                $table->boolean('show_in_menu')->default(true);
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'show_in_menu')) {
                $table->dropColumn('show_in_menu');
            }
        });
    }
};
