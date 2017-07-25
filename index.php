<?php

use ShortUrl\Service as ShortUrlService;

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');

spl_autoload_register(function ($className) {
	include 'src/' . str_replace('\\', '/', $className) . '.php';
});

$conf = include 'conf.php';

$mysqli = new mysqli($conf['mysql_host'], $conf['mysql_user'], $conf['mysql_pswd'], $conf['mysql_db']);
$mysqli->query("set names utf8");

$shortUrlService = new ShortUrlService($mysqli);
$controller = new Controller($shortUrlService, $conf);

if (!array_key_exists($key = 'action', $_REQUEST)
	|| !is_string($action = $_REQUEST[$key])) {
	$method = 'mainAction';
} elseif (!is_callable([$controller, $method = $action.'Action'])) {
	$method = 'getAction';
}
$controller->$method($_REQUEST);
