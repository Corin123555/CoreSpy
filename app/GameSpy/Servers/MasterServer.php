<?php


namespace App\GameSpy\Servers;

use Illuminate\Support\Facades\Log;
use SebastianBergmann\Environment\Console;
use Swoole\Server;

class MasterServer {

	public const SERVER_REPLY_BYTES = "\xFE\xFD";

	public const CMD_QUERY = "\x00";
	public const CMD_CHALLENGE = "\x01";
	public const CMD_ECHO = "\x02";
	public const CMD_HEARTBEAT = "\x03";
	public const CMD_ADDERROR = "\x04";
	public const CMD_ECHO_RESPONSE = "\x05";
	public const CMD_CLIENT_MESSAGE = "\x06";
	public const CMD_CLIENT_MESSAGE_ACK = "\x07";
	public const CMD_KEEPALIVE = "\x08";
	public const CMD_AVAILABLE = "\x09";
	public const CMD_CLIENT_REGISTERED = "\x0A";
}