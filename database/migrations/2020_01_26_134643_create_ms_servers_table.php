<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMSServersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ms_servers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('client_id', 8);
            $table->string('gamename', 255);
            $table->string('hostname', 255);
            $table->ipAddress('ip')->index();
            $table->unsignedSmallInteger('port');
            $table->longText('server_data')->nullable();
            $table->longText('player_data')->nullable();
            $table->longText('team_data')->nullable();
            $table->timestamps();

			$table->unique(['client_id', 'gamename']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ms_servers');
    }
}
