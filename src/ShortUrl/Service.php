<?php

namespace ShortUrl;

use AbstractService;

class Service extends AbstractService implements IService
{
	const APPEND_ALLOWED_ATTEMPT_LIMIT = 2; // 0 is no limit

	/**
	 * Replacement for base64-coded strings
	 *
	 * @var array
	 */
	protected $base64Replace = [
		'+' => '-',
		'/' => '_',
	];

	/**
	 * @var string
	 */
	protected $excludeTokenPattern = 'src|js';

	/**
	 * @param \mysqli $mysqli
	 */
	public function __construct(\mysqli $mysqli) {
		parent::__construct($mysqli);
	}

	/**
	 * @param string $url
	 *
	 * @return null|string
	 * @throws InternalException
	 * @throws InvalidUrlException
	 */
	public function generateToken($url) {

		// check url
		$components = parse_url($url);
		if (false === $components
			|| 0 != count(array_diff(['scheme', 'host'], array_keys($components)))) {
			throw new InvalidUrlException('Invalid URL: ' . $url);
		}

		// try to get/append url
		$attemptNumber = 0;
		while (is_null($token = $this->getToken($url))) {
			if (self::APPEND_ALLOWED_ATTEMPT_LIMIT == ++$attemptNumber) {
				throw new InternalException('Append attempt limit exceeded');
			}
			$this->appendUrl($url);
		}

		return $token;
	}

	/**
	 * @param string $token
	 *
	 * @return null
	 * @throws InvalidTokenException
	 */
	public function getUrl($token) {
		$result = $this->mysqli->query("select url from short_url where id = " . $this->decodeToken($token));
		if ($result instanceof \mysqli_result
			&& ($row = $result->fetch_row())) {
			$url = $row[0];
		} else {
			$url = null;
		}

		return $url;
	}

	/**
	 * For check token format:
	 * MySQL uint 4b (0 - 4294967295)
	 * base64 -> 6 chars, fixed
	 *
	 * @param string $token
	 *
	 * @return string
	 * @throws InvalidTokenException
	 */
	protected function decodeToken($token) {

		// if token matched exclude set
		if (preg_match('/^(' . $this->excludeTokenPattern . ')=$/', $token)) {
			$token = substr($token, 0, -1);
		}

		// extent base64 by replacement chars
		$replace = array_flip($this->base64Replace);
		$pattern = array_reduce($replace, function ($string, $value) {
			return $string . preg_quote($value, '/');
		}, '');
		if (!preg_match('/^[\da-z' . $pattern . ']{6}$/i', $token)) {
			throw new InvalidTokenException('Invalid token: ' . $token);
		}

		return unpack('Lid', base64_decode(strtr($token, $replace)))['id'];
	}

	/**
	 * @param integer $id
	 *
	 * @return string
	 */
	protected function encodeToken($id) {
		$token = rtrim(strtr(base64_encode(pack('L', $id)), $this->base64Replace), '=');

		if (preg_match('/^(' . $this->excludeTokenPattern . ')$/', $token)) {
			$token .= '=';
		}

		return $token;
	}

	/**
	 * @param string $url
	 *
	 * @return boolean
	 */
	protected function appendUrl($url) {
		$this->mysqli->query("insert ignore short_url (hash, url) values (0x" . $this->getUrlHash($url) . ", '" . $this->mysqli->escape_string($url) . "')");

		return 0 != $this->mysqli->affected_rows;
	}

	/**
	 * @param string $url
	 *
	 * @return string|null
	 */
	protected function getToken($url) {
		$result = $this->mysqli->query("select id from short_url where hash = 0x" . $this->getUrlHash($url));
		if ($result instanceof \mysqli_result
			&& ($row = $result->fetch_row())) {
			$token = $this->encodeToken((int) $row[0]);
		} else {
			$token = null;
		}

		return $token;
	}

	/**
	 * @param string $url
	 *
	 * @return string
	 */
	protected function getUrlHash($url) {
		return md5($url);
	}
}