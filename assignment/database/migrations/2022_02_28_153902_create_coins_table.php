<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coins', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('name', 100)->nullable();
            $table->bigInteger('price')->nullable();
            $table->bigInteger('marketcap')->nullable();
            $table->string('image', 250)->nullable();
            $table->string('idname', 60)->nullable()->unique('idname');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coins');
    }
}
