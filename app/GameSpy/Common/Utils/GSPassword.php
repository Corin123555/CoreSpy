<?php


namespace App\GameSpy\Common\Utils;

/**
 * Class GSPassword
 *
 * Based off http://aluigi.altervista.org/papers/gspassenc.zip
 * Not working correctly for some reason.
 *
 * Input: e4uEk1iom8MLaw__
 * Output: mypass▒or▒
 * Expected output: mypassword
 *
 * As you can see there's invalid characters where there should be valid ones?
 *
 * @package App\GameSpy\Common\Utils
 */
class GSPassword {

	public static function decode($data) {
		$data = GSBase64::decode($data);
		$data = array_values(unpack('C*', $data));
		self::cipher($data);
		return pack('C*', ...$data);
	}

	public static function encode($data) {
		$data = array_values(unpack('C*', $data));
		self::cipher($data);
		$data = GSBase64::encode(pack('C*', ...$data));
		return $data;
	}


	private static function cipher(&$data) {
		$a = 0;
		$num = 0x79707367;

		if(!$num) {
			$num = 1;
		} else {
			$num &= 0x7fffffff;
		}

		for ($i = 0; $i < count($data); ++$i) {
			$d = 0xff;
			$c = 0;
			$d -= $c;
			if ($d) {
				$num = self::gslame($num);
				$a = $num % 0xFF;
				$a += $c;
			} else {
				$a = $c;
			}
			$data[$i] ^= $a;
		}
	}

	private static function gslame(int $num): int {
		$c = ($num >> 16) & 0xffff;
		$a = $num & 0xffff;
		$c *= 0x41a7;
		$a *= 0x41a7;
		$a += (($c & 0x7fff) << 16);
		if ($a < 0) {
			$a &= 0x7fffffff;
			$a++;
		}
		$a += ($c >> 15);
		if ($a < 0) {
			$a &= 0x7fffffff;
			$a++;
		}
		return ($a);
	}

}