<?php


namespace App\GameSpy\Common\Utils;



class GSBase64 {
	public static function decode($data) {
		$data = str_replace('=', '_', $data);
		$data = str_replace('+', '[', $data);
		$data = str_replace('/', ']', $data);

		return base64_decode($data);
	}

	public static function encode($data) {
		$data = str_replace('_', '=', $data);
		$data = str_replace('[', '+', $data);
		$data = str_replace(']', '/', $data);

		return base64_encode($data);
	}
}