<?php

namespace ShortUrl;

interface IService {

	/**
	 * @param string $url
	 *
	 * @return null|string
	 * @throws \Exception
	 */
	public function generateToken($url);

	/**
	 * @param string $token
	 *
	 * @return null|string
	 * @throws \Exception
	 */
	public function getUrl($token);
}