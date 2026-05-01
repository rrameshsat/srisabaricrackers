<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (!Schema::hasColumn('settings', 'show_footer_faq')) {
                $table->boolean('show_footer_faq')->default(true);
            }

            if (!Schema::hasColumn('settings', 'show_footer_how_it_works')) {
                $table->boolean('show_footer_how_it_works')->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            if (Schema::hasColumn('settings', 'show_footer_faq')) {
                $table->dropColumn('show_footer_faq');
            }

            if (Schema::hasColumn('settings', 'show_footer_how_it_works')) {
                $table->dropColumn('show_footer_how_it_works');
            }
        });
    }
};
