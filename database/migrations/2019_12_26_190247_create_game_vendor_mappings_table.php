<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameVendorMappingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_vendor_mappings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('game_id')->comment('彩種編號');
            $table->bigInteger('vendor_id')->comment('號源編號');
            $table->boolean('major')->comment('是否為主號源');
            $table->timestamps();

            $table->foreign('game_id')->references('id')->on('games')->onDelete('cascade');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->unique(['game_id', 'vendor_id'], 'game_vendor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_vendor_mappings');
    }
}
