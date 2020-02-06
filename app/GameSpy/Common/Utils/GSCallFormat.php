<?php


namespace App\GameSpy\Common\Utils;


/**
 * Class GSCallFormat
 *
 * This entire process is a mess and could be improved so much.
 *
 * @package App\GameSpy\Common\Utils
 */
class GSCallFormat {

	/**
	 * @var string $call
	 */
	public $call;

	/**
	 * @var array $data
	 */
	public $data;

	public function parseIncoming(string $string) {
		$string = ltrim($string, '\\');
		$string = rtrim($string, '\\final\\');

		$array = explode('\\', $string);
		$this->call = $array[0];

		unset($array[0], $array[1]); // Removes the call and the empty array element

		$result = [];
		while (count($array)) {
			list($key, $value) = array_splice($array, 0, 2);
			$result[$key] = $value;
		}

		$this->data = $result;
	}

	public function buildOutgoing(array $data, bool $is_final) {
		$resp = "";
		foreach ($data as $key => $value) {
			if (is_bool($value)) $value = (int)$value;
			$resp .= "\\{$key}\\{$value}";
		}
		if ($is_final) $resp .= '\\final\\';
		return $resp;
	}

	/**
	 * https://github.com/nitrocaster/GameSpy/blob/master/src/GameSpy/GP/gpiUtility.c#L51
	 *
	 * Uses error codes from App\GameSpy\Common\ErrorCodes
	 *
	 * @param array $error_code
	 * @param bool $is_fatal
	 * @return string
	 */
	public function buildError(array $error_code, bool $is_fatal = true) {
		$array = [
			'error' => null,
			'err' => $error_code['code'],
			'errmsg' => $error_code['text'],
			'fatal' => $is_fatal
		];
		return $this->buildOutgoing($array, true);
	}

}