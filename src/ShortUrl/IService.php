<?php

namespace ShortUrl;

interface IService {

	/**
	 * @param string $url
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function generateToken($url);

	/**
	 * @param string $token
	 *
	 * @return string
	 * @throws \Exception
	 */
	public function getUrl($token);
}