<?php


namespace App\GameSpy\Servers\MasterServer\Commands;

use App\Console\Commands\StartGSMS;
use App\GameSpy\Servers\MasterServer;
use App\MSServer;

/**
 * KEEPALIVE command processor
 *
 * Literally the most basic thing ever
 * 08 - Command byte
 * XX XX XX XX - Client ID
 *
 * Reply with server byte + command byte only
 * FE FD - Server byte
 * 08 - Command byte
 *
 * This should be used to refresh the server in the database
 *
 * https://github.com/nitrocaster/GameSpy/blob/master/src/GameSpy/qr2/qr2.c#L1653
 *
 */
class Keepalive {
	public static function handle(StartGSMS $parent) {
		$client_id = bin2hex(stream_get_contents($parent->data));

		$server = MSServer::where('client_id', $client_id)->where('ip', $parent->remote_address)->first();
		if ($server) {
			$server->touch();
			$response = MasterServer::SERVER_REPLY_BYTES . MasterServer::CMD_KEEPALIVE;
			$parent->comment('Responding to '.strtoupper($client_id).' with: '. strtoupper(bin2hex($response)));
			$parent->server->sendto($parent->remote_address, $parent->remote_port, $response, $parent->socket);
		}

	}
}