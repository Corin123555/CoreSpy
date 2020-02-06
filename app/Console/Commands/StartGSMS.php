<?php

namespace App\Console\Commands;

use App\GameSpy\Servers\MasterServer;
use Illuminate\Console\Command;
use Swoole\Server;

/**
 * GameSpy MasterServer
 * Port: 27900
 * Protocol: UDP
 * DNS: %s.ms%d.gamespy.com Where %s is the game name and %d is an int from 1 to 6, EG: area51pc.ms1.gamespy.com
 * DNS: %s.master.gamespy.com Where %s is the game name, EG: area51pc.master.gamespy.com
 * DNS: %s.available.gamespy.com Where %s is the game name, EG: area51pc.available.gamespy.com
 */
class StartGSMS extends Command {

	/**
	 * @var Server $server
	 */
	public $server;

	/**
	 * @var resource $data
	 */
	public $data;

	/**
	 * @var string $remote_address
	 */
	public $remote_address;

	/**
	 * @var int $remote_port
	 */
	public $remote_port;

	/**
	 * @var int $socket
	 */
	public $socket;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gs:startms';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Starts the GameSpy MasterServer';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Start the server.
	 *
	 * @return void
	 */
	public function handle() {
		$server = new Server("192.168.0.6", 27900, SWOOLE_BASE, SWOOLE_SOCK_UDP);
		$server->on('packet', function ($server, $data, $addr) {
			$this->server = $server;

			$resource = fopen('php://memory', 'rw+');
			fwrite($resource, $data);
			fseek($resource, 0);
			$this->data = $resource;

			$this->remote_address = $addr['address'];
			$this->remote_port = $addr['port'];
			$this->socket = $addr['server_socket'];

			$this->info('Connection from ' . $this->remote_address . ':' . $this->remote_port);

			$this->process();
		});
		$server->start();
	}

	private function process(): void {
		$command = fread($this->data, 1);
		$this->info('Calling command: ' . ord($command));

		switch ($command) {
			case MasterServer::CMD_QUERY: // QUERY (0x00)
				$this->stub($command);
				break;
			case MasterServer::CMD_CHALLENGE: // CHALLENGE (0x01)
				MasterServer\Commands\Challenge::handle($this);
				break;
			case MasterServer::CMD_ECHO: // ECHO (0x02) Follow up with ECHO_RESPONSE (0x05) or alternatively ADDERROR (0x04) if the NAT is shit
				$this->stub($command);
				break;
			case MasterServer::CMD_HEARTBEAT: // HEARTBEAT (0x03) Follow up with CHALLENGE if unrecognised client id/game name combo
				MasterServer\Commands\Heartbeat::handle($this);
				break;
			case MasterServer::CMD_CLIENT_MESSAGE: // CLIENT_MESSAGE (0x06) Mixed signals, according to MKWiki the server sends all packets but apparently the client can send part of type 2?
				$this->stub($command);
				break;
			case MasterServer::CMD_KEEPALIVE: // KEEPALIVE (0x08)
				MasterServer\Commands\Keepalive::handle($this);
				break;
			case MasterServer::CMD_AVAILABLE: // AVAILABLE (0x09)
				MasterServer\Commands\Available::handle($this);
				break;
			case MasterServer::CMD_CLIENT_REGISTERED: // CLIENT_REGISTERED (0x0A)
				$this->stub($command);
				break;
			default:
				$this->error('No switch for command ' . ord($command));
				break;
		}

		fclose($this->data);
	}

	public function stub(string $command): void {
		$command = ord($command);
		$this->error('Unhandled call from game with command ' . $command . ', data as follows: ' . bin2hex(stream_get_contents($this->data)));
	}
}
