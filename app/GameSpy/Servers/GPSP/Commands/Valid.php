<?php


namespace App\GameSpy\Servers\GPSP\Commands;


use App\Console\Commands\StartGPSP;
use App\User;

/**
 * Valid call
 *
 * Checks to see if an email address exists in the database
 * Incoming example: \valid\\email\a@a.com\gamename\area51pc\final\
 * Outgoing example: \vr\1\final\
 * Where 1 is a boolean if the entry exists or not
 * I think vr stands for Valid Response
 *
 * https://github.com/nitrocaster/GameSpy/blob/master/src/GameSpy/GP/gpiSearch.c#L978
 *
 * @package App\GameSpy\Servers\GPSP\Calls
 */
class Valid {
	public static function handle(StartGPSP $parent) {
		$data = $parent->data->data;
		$query = User::where('email', $data['email'])->exists();

		$return = $parent->data->buildOutgoing(['vr' => $query], true);

		$parent->server->send($parent->fd, $return);
	}
}