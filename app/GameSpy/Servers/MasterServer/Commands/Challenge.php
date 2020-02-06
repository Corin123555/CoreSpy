<?php


namespace App\GameSpy\Servers\MasterServer\Commands;


use App\Console\Commands\StartGSMS;
use App\GameSpy\Servers\MasterServer;

/**
 * CHALLENGE command processor
 *
 * Challenge response handler for gameservers
 * Invoked by HEARTBEAT if the server is unrecognised.
 * 01 - Command byte
 * XX - The challenge string sent by HEARTBEAT
 *
 * If the challenge string is recognised, just respond with CLIENT_REGISTERED, else don't respond at all
 * FE FD - Server byte
 * 0A - Command byte (CLIENT_REGISTERED)
 *
 */
class Challenge {

	public static function handle(StartGSMS $parent) {
		$response = MasterServer::SERVER_REPLY_BYTES . MasterServer::CMD_CLIENT_REGISTERED;

		$parent->comment('Responding to '.strtoupper(bin2hex(stream_get_contents($parent->data))).' with: '. strtoupper(bin2hex($response)));
		$parent->server->sendto($parent->remote_address, $parent->remote_port, $response, $parent->socket);
	}
}