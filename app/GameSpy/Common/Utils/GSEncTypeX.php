<?php


namespace App\GameSpy\Common\Utils;

/**
 * EncTypeX cipher, only working on decoding for now, once I can get this working I'll work on encoding
 * From what I'm aware it's used quite extensively and, unfortunately, for server search; so there's no way around it.
 *
 * Based off http://aluigi.altervista.org/papers/enctypex_decoder.c and ideas taken from a few other projects.
 * Except I suck at C so I can't do the conversion completely.
 * ... as a result this doesn't work and is my current blocker with the project.
 *
 * array_values(unpack('C*', $data)) can be used to convert the binary to char to be used with the decoder (the array_values will shift the array to start at 0 instead of 1)
 *
 * Test values:
 * $key = "e4Rd9J";
 * $validate = "&Te,C*3F";
 * $hex = "e60000e741cbf38138f0f5f383a0f7841ed99cd39e3b20f20c64469e0d1fe0e3fd868ae4c7d49b398a671a0c5c98879d25c273481e430f16edec8e46161c1031ab9da513dbdc2dbd1f2dc160c25b940a2d314ec3a1fa937d1ca207fe495ff720fc19872dc4346f6b3bb0b938d9a05f5037c31f9d25222a7ea63eaff0ac1f8cceba0f9303328469393d3a6b442b7fa52a3d180c6078e9664c4b489421c9";
 *
 */
class enctypex_data_t {
	public $encxkey;
	public $offset;
	public $start;

	public function __construct() {
		$this->encxkey = GSEncTypeX::byte(260);
	}
}

class GSEncTypeX {
	public static function byte($len) {
		return array_fill(0, $len, "\x00");
	}

	public static function enctypex_decoder(array &$key, array &$validate, array &$data, int &$datalen, ?enctypex_data_t &$enctypex_data = null) {
		$encxkeyb = self::byte(260);

		$encxkey = $enctypex_data ? $enctypex_data->encxkey : $encxkeyb;

		if (!$enctypex_data || ($enctypex_data && !$enctypex_data->start)) {
			$data = self::enctypex_init($encxkey, $key, $validate, $data, $datalen, $enctypex_data);
			if (!$data) return (null);
		}
		if (!$enctypex_data) {
			self::enctypex_func6($encxkey, $data, $datalen);
			return ($data);
		} else if ($enctypex_data && $enctypex_data->start) {
			$enctypex_data->offset += self::enctypex_func6($encxkey, array_slice($data, $enctypex_data->offset), ($datalen - $enctypex_data->offset));
			return (array_slice($data, $enctypex_data->start));
		}
		return null;
	}

	public static function enctypex_encoder(array &$key, array &$validate, array &$data, int &$datalen, ?enctypex_data_t &$enctypex_data = null) {
		$encxkeyb = self::byte(260);

		$encxkey = $enctypex_data ? $enctypex_data->encxkey : $encxkeyb;

		if (!$enctypex_data || ($enctypex_data && !$enctypex_data->start)) {
			$data = self::enctypex_init($encxkey, $key, $validate, $data, $datalen, $enctypex_data);
			if (!$data) return (null);
		}
		if (!$enctypex_data) {
			self::enctypex_func6e($encxkey, $data, $datalen);
			return ($data);
		} else if ($enctypex_data && $enctypex_data->start) {
			$enctypex_data->offset += self::enctypex_func6e($encxkey, array_slice($data, $enctypex_data->offset), ($datalen - $enctypex_data->offset));
			return (array_slice($data, $enctypex_data->start));
		}
		return null;
	}


	public static function enctypex_init(&$encxkey, &$key, &$validate, &$data, &$datalen, ?enctypex_data_t &$enctypex_data) {
		$encxvalidate = self::byte(7);

		if ($datalen < 1) return null;

		$a = ($data[0] ^ 0xec) + 2;
		if ($datalen < $a) return null;

		$b = $data[$a - 1] ^ 0xea;
		if ($datalen < ($a + $b)) return null;

		$encxvalidate = array_replace($encxvalidate, array_slice($validate, 0, 8));

		$data = self::enctypex_funcx($encxkey, $key, $encxvalidate, array_slice($data, $a), $b);

		return array_slice($data, $a);
	}

	public static function enctypex_funcx(&$encxkey, &$key, &$encxvalidate, &$data, $datalen) {
		$keylen = count($key);
		for ($i = 0; $i < $datalen; $i++) {
			$encxvalidate[($key[$i % $keylen] * $i) & 7] ^= $encxvalidate[$i & 7] ^ $data[$i];
		}
		self::enctypex_func4($encxkey, $encxvalidate, 8);
		return $data;
	}

	public static function enctypex_func4(&$encxkey, &$id, $idlen) {
		$n1 = $n2 = 0;

		if ($idlen < 1) return;

		for ($i = 0; $i < 256; $i++) $encxkey[$i] = $i;

		for ($i = 255; $i >= 0; $i--) {
			$t1 = self::enctypex_func5($encxkey, $i, $id, $idlen, $n1, $n2);
			$t2 = $encxkey[$i];
			$encxkey[$i] = $encxkey[$t1];
			$encxkey[$t1] = $t2;
		}

		$encxkey[256] = $encxkey[1];
		$encxkey[257] = $encxkey[3];
		$encxkey[258] = $encxkey[5];
		$encxkey[259] = $encxkey[7];
		$encxkey[260] = $encxkey[$n1 & 0xff];
	}

	public static function enctypex_func5(&$encxkey, $cnt, &$id, $idlen, &$n1, &$n2) {
		$mask = 1;

		if (!$cnt) return (0);
		if ($cnt > 1) {
			do {
				$mask = ($mask << 1) + 1;
			} while ($mask < $cnt);
		}

		$i = 0;
		do {
			$n1 = $encxkey[$n1 & 0xff] + $id[$n2];
			$n2++;
			if ($n2 >= $idlen) {
				$n2 = 0;
				$n1 += $idlen;
			}
			$tmp = $n1 & $mask;
			if (++$i > 11) $tmp %= $cnt;
		} while ($tmp > $cnt);
		return ($tmp);
	}

	public static function enctypex_func6(array &$encxkey, array &$data, $len) {
		for ($i = 0; $i < $len; $i++) {
			$data[$i] = self::enctypex_func7($encxkey, $data[$i]);
		}
		return ($len);
	}

	public static function enctypex_func7(array $encxkey, $d) {
		$a = $b = $c = null;

		$a = $encxkey[256];
		$b = $encxkey[257];
		$c = $encxkey[$a];
		$encxkey[256] = $a + 1;
		$encxkey[257] = $b + $c;
		$a = $encxkey[260];
		$b = $encxkey[257];
		$b = $encxkey[$b];
		$c = $encxkey[$a];
		$encxkey[$a] = $b;
		$a = $encxkey[259];
		$b = $encxkey[257];
		$a = $encxkey[$a];
		$encxkey[$b] = $a;
		$a = $encxkey[256];
		$b = $encxkey[259];
		$a = $encxkey[$a];
		$encxkey[$b] = $a;
		$a = $encxkey[256];
		$encxkey[$a] = $c;
		$b = $encxkey[258];
		$a = $encxkey[$c];
		$c = $encxkey[259];
		$b += $a;
		$encxkey[258] = $b;
		$a = $b;
		$c = $encxkey[$c];
		$b = $encxkey[257];
		$b = $encxkey[$b];
		$a = $encxkey[$a];
		$c += $b;
		$b = $encxkey[260];
		$b = $encxkey[$b];
		$c += $b;
		$b = $encxkey[$c];
		$c = $encxkey[256];
		$c = $encxkey[$c];
		$a += $c;
		$c = $encxkey[$b];
		$b = $encxkey[$a];
		$encxkey[260] = $d;
		$c ^= $b ^ $d;
		$encxkey[259] = $c;
		return ($c);
	}
}
