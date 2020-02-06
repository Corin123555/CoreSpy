<?php

namespace App\Console\Commands;

use App\GameSpy\Common\Utils\GSCallFormat;
use App\GameSpy\Servers\GPCM;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Swoole\Server;

class StartGPCM extends Command {
	private const PORT = 29900;

	/**
	 * @var Server $server
	 */
	public $server;

	/**
	 * @var int $fd
	 */
	public $fd;

	/**
	 * @var string $session_id
	 */
	public $session_id;

	/**
	 * @var GSCallFormat $data
	 */
	public $data;

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'gs:startgpcm';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Starts the GameSpy Connection Manager server';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle() {
		$server = new Server("192.168.0.6", self::PORT, SWOOLE_BASE, SWOOLE_SOCK_TCP);
		$server->on('connect', function (Server $server, $fd) {
			$client = $server->getClientInfo($fd);
			$remote_ip = $client['remote_ip'];
			$remote_port = $client['remote_port'];

			$this->session_id = Str::random(10);

			$this->info("GPCM: Connection from {$remote_ip}:{$remote_port} (FD: {$fd})");
		});

		$server->on('receive', function ($server, $fd, $from_id, $data) {
			$this->server = $server;
			$this->fd = $fd;
			$gs_format = new GSCallFormat();
			$gs_format->parseIncoming($data);
			$this->data = $gs_format;
			$this->process();
		});

		$server->on('close', function ($server, $fd) {
			$this->info("GPCM: Connection closed ({$fd})");
		});

		$server->start();
	}


	private function process(): void {
		$this->info("FD {$this->fd} Calling: {$this->data->call}");

		switch ($this->data->call) {
			case 'newuser':
				GPCM\Commands\NewUser::handle($this);;
				break;
			default:
				$this->error("No switch for call {$this->data->call}");
				break;
		}
	}
}
