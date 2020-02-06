<?php


namespace App\GameSpy\Common\Utils;

/**
 * Class GSXor
 *
 * Used for GS auth services
 *
 * @package App\GameSpy\Common\Utils
 */
class GSXor {
	private const XOR_KEY_0 = 'gamespy';
	private const XOR_KEY_1 = 'GameSpy3D';
	private const XOR_KEY_2 = 'Industries';
	private const XOR_KEY_3 = 'ProjectAphex';

	public const XOR_TYPE_0 = 0;
	public const XOR_TYPE_1 = 1;
	public const XOR_TYPE_2 = 2;
	public const XOR_TYPE_3 = 3;

	public static function XorString(string $string, int $xor_type) {
		$key = null;
		$index = 0;
		$string = str_replace('\final\\', '', $string);
		$data = str_split($string);
		$string_length = mb_strlen($string);

		switch ($xor_type) {
			case self::XOR_TYPE_0:
				$key = self::XOR_KEY_0;
				break;
			case self::XOR_TYPE_1:
				$key = self::XOR_KEY_1;
				break;
			case self::XOR_TYPE_2:
				$key = self::XOR_KEY_2;
				break;
			case self::XOR_TYPE_3:
				$key = self::XOR_KEY_3;
				break;
		}

		$key_length = mb_strlen($key);

		for ($i = 0; $string_length > 0; $string_length--) {
			if ($i >= $key_length)
				$i = 0;

			$data[$index++] ^= $key[$i++];
		}

		$data = implode($data) . '\final\\';
		return $data;
	}
}