<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            if (!Schema::hasColumn('sliders', 'text_color')) {
                $table->string('text_color')->nullable()->after('details');
            }
            if (!Schema::hasColumn('sliders', 'title_color')) {
                $table->string('title_color')->nullable()->after('text_color');
            }
            if (!Schema::hasColumn('sliders', 'text_position')) {
                $table->string('text_position')->default('left')->after('title_color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn(['text_color', 'title_color', 'text_position']);
        });
    }
};