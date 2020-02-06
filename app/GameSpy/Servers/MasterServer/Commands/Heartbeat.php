<?php


namespace App\GameSpy\Servers\MasterServer\Commands;


use App\Console\Commands\StartGSMS;
use App\GameSpy\Common\Utils\GSMSFormat;
use App\GameSpy\Servers\MasterServer;
use App\MSServer;
use function Psy\bin;

/**
 * HEARTBEAT command processor
 *
 * More info about the packet itself can be found in \app\GameSpy\Common\Utils\GSMSFormat.php
 *
 * Example packet from Area 51 PC:
 * 44 B7 70 34 6C 6F 63 61 6C 69 70 30 00 31 39 32 2E 31 36 38 2E 30 2E 31 30 30 00 6C 6F 63 61 6C 69 70 31 00 31 39 32 2E 31 36 38 2E 32 31 36 2E 32 30 39 00 6C 6F 63 61 6C 70 6F 72 74 00 32 37 36 36 36 00 6E 61 74 6E 65 67 00 31 00 73 74 61 74 65 63 68 61 6E 67 65 64 00 33 00 67 61 6D 65 6E 61 6D 65 00 61 72 65 61 35 31 70 63 00 70 75 62 6C 69 63 69 70 00 30 00 70 75 62 6C 69 63 70 6F 72 74 00 30 00 68 6F 73 74 6E 61 6D 65 00 59 45 73 00 67 61 6D 65 76 65 72 00 36 31 31 32 00 68 6F 73 74 70 6F 72 74 00 32 37 36 36 36 00 6D 61 70 6E 61 6D 65 00 47 61 7A 65 62 6F 00 67 61 6D 65 74 79 70 65 00 44 65 61 74 68 6D 61 74 63 68 00 67 61 6D 65 74 79 70 65 69 64 00 30 00 6E 75 6D 70 6C 61 79 65 72 73 00 31 00 6E 75 6D 74 65 61 6D 73 00 31 00 6D 61 78 70 6C 61 79 65 72 73 00 31 36 00 67 61 6D 65 6D 6F 64 65 00 44 4D 00 74 65 61 6D 70 6C 61 79 00 31 00 66 72 61 67 6C 69 6D 69 74 00 30 00 74 69 6D 65 6C 69 6D 69 74 00 2D 31 00 73 76 72 66 6C 61 67 73 00 32 00 6C 65 76 65 6C 00 32 30 34 30 00 70 6C 61 79 65 72 73 00 43 6F 72 69 6E 00 61 76 67 70 69 6E 67 00 31 30 30 00 6D 75 74 61 74 69 6F 6E 00 30 00 70 61 73 73 77 6F 72 64 65 6E 00 30 00 70 72 6F 67 72 65 73 73 00 30 00 00 00 01 70 6C 61 79 65 72 5F 00 73 63 6F 72 65 5F 00 64 65 61 74 68 73 5F 00 70 69 6E 67 5F 00 74 65 61 6D 5F 00 00 43 6F 72 69 6E 00 30 00 30 00 30 00 30 00 00 00 74 65 61 6D 5F 74 00 73 63 6F 72 65 5F 74 00 00
 *
 * More info on the format can be found here: https://github.com/nitrocaster/GameSpy/blob/master/src/GameSpy/qr2/qr2.c#L1669
 */
class Heartbeat {

	public static function handle(StartGSMS $parent) {
		$packet = GSMSFormat::formatData($parent->data);
		if (!$packet) return;

		$server = MSServer::where('client_id', bin2hex($packet->client_id))->where('gamename', $packet->server_data['gamename'])->first();

		if (!$server) {
			$server = new MSServer();
			$server->client_id = bin2hex($packet->client_id);
			$server->ip = $packet->server_data['localip0'];
			$server->gamename = $packet->server_data['gamename'];

			$response = MasterServer::SERVER_REPLY_BYTES . MasterServer::CMD_CHALLENGE;

			$response .= openssl_random_pseudo_bytes(20);
			$response .= "\x00";

			$parent->server->sendto($parent->remote_address, $parent->remote_port, $response, $parent->socket);
		}

		$server->hostname = $packet->server_data['hostname'];
		$server->port = $packet->server_data['localport'];
		$server->server_data = $packet->server_data;
		$server->player_data = $packet->player_data;
		$server->team_data = $packet->team_data;
		$server->save();
	}
}