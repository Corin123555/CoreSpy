<?php


namespace App\GameSpy\Common;

/**
 * Class ErrorCodes
 *
 * https://github.com/nitrocaster/GameSpy/blob/d1deb2d1a951cf77933dda040b8d311cc09815a7/src/GameSpy/GP/gp.h#L235
 *
 * @package App\GameSpy\Common
 */
class ErrorCodes {

	// General
	public const GP_GENERAL = [
		'code' => 0x0000,
		'text' => 'A general error has occurred'
	];
	public const GP_PARSE = [
		'code' => 0x0001,
		'text' => 'There was an error parsing the request'
	];
	public const GP_NOT_LOGGED_IN = [
		'code' => 0x0002,
		'text' => 'You are not logged in'
	];
	public const GP_BAD_SESSKEY = [
		'code' => 0x0003,
		'text' => 'Invalid session key supplied'
	];
	public const GP_DATABASE = [
		'code' => 0x0004,
		'text' => 'There was an error accessing the database'
	];
	public const GP_NETWORK = [
		'code' => 0x0005,
		'text' => 'A network error has occurred'
	];
	public const GP_FORCED_DISCONNECT = [
		'code' => 0x0006,
		'text' => '' // I think this is client
	];
	public const GP_CONNECTION_CLOSED = [
		'code' => 0x0007,
		'text' => '' // Client again?
	];
	public const GP_UDP_LAYER = [
		'code' => 0x0008,
		'text' => '' // And again...?
	];

	// newuser
	public const GP_NEWUSER = [
		'code' => 0x0200,
		'text' => 'A general error occurred when creating your account'
	];
	public const GP_NEWUSER_BAD_NICK = [
		'code' => 0x0201,
		'text' => 'Invalid nickname supplied, please choose another'
	];
	public const GP_NEWUSER_BAD_PASSWORD = [ // Why would this ever even be thrown?? (I think max password length of 40 chars)
		'code' => 0x0202,
		'text' => 'Invalid password supplied, please choose another'
	];
	public const GP_NEWUSER_UNIQUENICK_INVALID = [
		'code' => 0x0203,
		'text' => 'Invalid unique nick supplied, please choose another'
	];
	public const GP_NEWUSER_UNIQUENICK_INUSE = [
		'code' => 0x0204,
		'text' => 'Provided unique nick is in use, please choose another'
	];
}