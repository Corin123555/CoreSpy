<?php


namespace App\GameSpy\Common\Utils;


class GSMSFormat {

	public $client_id;
	public $server_data;
	public $player_data;
	public $team_data;

	/**
	 * Handles/formats the GS Master Server heartbeat packet.
	 * The packet is pretty basic, it begins with 4 bytes or the "instance key".
	 * Each section is then separated by 00 00 00 or 00 00 02.
	 * Not quite sure where the 02 comes in, I think it's the amount of teams?
	 *
	 * The packet has 3 sections, the server data, the player data and the team data.
	 *
	 * Finally the packet is terminated by 2 nt bytes
	 *
	 * @param resource $data
	 * @return null|GSMSFormat
	 */
	public static function formatData($data): ?GSMSFormat {
		$client_id = fread($data, 4);
		$data = stream_get_contents($data);

		$data = explode("\x00\x00\x00", $data);
		if (count($data) == 2) {
			$data[2] = explode("\x00\x00\x02", $data[1])[1];
			$data[1] = explode("\x00\x00\x02", $data[1])[0];
		} elseif (count($data) == 1 || count($data) == 0 /*never know*/) {
			return null;
		}

		$data[1] = substr($data[1], 1); // Byte 1 is the player count, we don't care about this because we work it out based on the array elements
		$data[2] = rtrim($data[2], "\x00\x00"); // Remove the end of packet marker

		$format = new GSMSFormat();
		$format->client_id = $client_id;
		$format->server_data = self::formatServerData($data[0]);
		$format->player_data = self::formatAdditionalData($data[1]);
		$format->team_data = self::formatAdditionalData($data[2]);

		return $format;
	}

	/**
	 * "Server data" section of the format; this contains the settings and details of the server.
	 * The format is pretty basic, just key:value pairs separated by a nt byte.
	 *
	 * @param string $data
	 * @return array
	 */
	private static function formatServerData($data) {
		$data = explode("\x00", $data);

		$result = [];
		while (count($data)) {
			list($key, $value) = array_splice($data, 0, 2);
			$result[$key] = $value;
		}

		return $result;
	}

	/**
	 * "Additional data" section of the format; this is for the team data and the player data
	 * The team section is a format of [header list] separated by 2 nt bytes and then the actual data
	 * The elements are then separated by a single nt byte, so for example: 74 65 61 6D 5F 74 00 73 63 6F 72 65 5F 74
	 * These are two headers.
	 *
	 * Then after that is 00 00 to signify the data section, which like the above is separated by a nt byte.
	 * Looping through that data we can throw it all into an array
	 *
	 * The player data section is the exact same except the first byte in the section, which is actually the player count.
	 * We don't actually care about that here so we snip it off in the above functions.
	 *
	 * @param string $data
	 * @return array
	 */
	private static function formatAdditionalData($data) {
		$data = explode("\x00\x00", $data, 2);
		if (count($data) == 1) return null;
		list($headers, $data) = $data;

		$headers = explode("\x00", $headers);

		$data = explode("\x00", $data);
		if (empty($data)) return null;
		$output = [];
		$compiled = [];
		$i = 0;

		foreach ($data as $item) {
			$compiled[$headers[$i]] = $item;
			if ($i == (count($headers) -1)) { // Data should only be as long as the headers
				$output[] = $compiled;
				$compiled = [];
				$i = 0;
			} else {
				$i++;
			}
		}

		return $output;
	}
}