<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpiConfigsTable extends Migration
{
    public function up()
    {
        Schema::create('upi_configs', function (Blueprint $table) {
            $table->id();
            $table->boolean('enabled')->default(false);
            $table->string('merchant_id')->nullable();
            $table->string('endpoint')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('upi_configs');
    }
}
