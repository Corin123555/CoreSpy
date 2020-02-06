<?php


namespace App\GameSpy\Servers\GPCM\Commands;


use App\Console\Commands\StartGPCM;
use App\GameSpy\Common\ErrorCodes;
use App\GameSpy\Common\Utils\GSPassword;
use App\User;

/**
 * NewUser call
 *
 * Checks to see if an email address exists in the database
 * Incoming example: \newuser\\email\a@a.com\nick\Corin\passwordenc\J8DH\productid\10588\gamename\area51pc\namespaceid\0\uniquenick\\id\1\final\
 * Outgoing example: \nur\\userid\1\profileid\1\id\1\final\
 *
 * I think nur stands for newuser response
 *
 * https://github.com/nitrocaster/GameSpy/blob/master/src/GameSpy/GP/gpiConnect.c#L483 Call
 * https://github.com/nitrocaster/GameSpy/blob/master/src/GameSpy/GP/gpiConnect.c#L637 Response
 *
 * @package App\GameSpy\Servers\GPCM\Calls
 */
class NewUser {
	public static function handle(StartGPCM $parent) {
		$data = $parent->data->data;

		$email_exists = User::where('email', $data['email'])->exists();

		if ($email_exists) {
			$return = $parent->data->buildError(ErrorCodes::GP_NEWUSER_UNIQUENICK_INUSE);
			$parent->server->send($parent->fd, $return);
			return;
		}

		$user = new User();
		$user->

		$password = GSPassword::decode($data['passwordenc']);


		$parent->server->send($parent->fd, $return);
	}
}