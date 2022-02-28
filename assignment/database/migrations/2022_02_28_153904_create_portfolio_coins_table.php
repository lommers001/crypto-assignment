<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortfolioCoinsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portfolio_coins', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('coin_id', 60)->nullable();
            $table->integer('portfolio_id')->nullable();
            $table->unsignedInteger('amount')->nullable();
            
            $table->foreign('coin_id', 'coin_id')->references('idname')->on('coins');
            $table->foreign('portfolio_id', 'portfolio_id')->references('id')->on('portfolios');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('portfolio_coins');
    }
}
