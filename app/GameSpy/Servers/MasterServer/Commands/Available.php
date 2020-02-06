<?php


namespace App\GameSpy\Servers\MasterServer\Commands;


use App\Console\Commands\StartGSMS;
use App\GameSpy\Servers\MasterServer;

/**
 * AVAILABLE command processor
 *
 * A basic packet with the format of
 * 09 - Command byte
 * 00 00 00 00 - Currently disabled services
 * x to 00 - Game name with an NT byte
 *
 * Reply with server byte + services that are available (00), eg
 * FE FD - Server byte
 * 09 - Command byte
 * 00 00 00 00 - All services OK
 *
 * Alternatively 01 can be returned for "Temporarily Unavailable" or 02 for "Unavailable"
 * https://github.com/nitrocaster/GameSpy/blob/master/src/GameSpy/common/gsAvailable.c#L174
 *
 */
class Available {

	public static function handle(StartGSMS $parent) {
		$response = MasterServer::SERVER_REPLY_BYTES . MasterServer::CMD_AVAILABLE;

		// Extract game name from request
		fseek($parent->data, 4, SEEK_CUR);
		$game_name = stream_get_contents($parent->data);
		$game_name = rtrim($game_name, "\x00"); // Remove NT

		$response .= "\x00\x00\x00\x00"; // All OK for now.

		$parent->comment('Responding with: '. strtoupper(bin2hex($response)));
		$parent->server->sendto($parent->remote_address, $parent->remote_port, $response, $parent->socket);
	}
}