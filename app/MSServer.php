<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MSServer extends Model
{
	protected $table = 'ms_servers';

	protected $fillable = [
		'client_id',
		'gamename',
		'hostname',
		'ip',
		'port',
		'server_data',
		'player_data',
		'team_data'
	];

	protected $casts = [
		'server_data' => 'array',
		'player_data' => 'array',
		'team_data' => 'array'
	];
}
