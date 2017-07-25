<?php

abstract class AbstractService
{
	/**
	 * @var mysqli
	 */
	protected $mysqli;

	/**
	 * @param mysqli $mysqli
	 */
	public function __construct(mysqli $mysqli) {
		$this->mysqli = $mysqli;
	}
}