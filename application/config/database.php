<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
|
| LOCAL (XAMPP): 127.0.0.1 / root / empty password / minicrm
| Use 127.0.0.1 (not localhost) to avoid broken mysqli socket paths on macOS/XAMPP.
|
| HOSTINGER: edit the production block below OR set env vars:
|   MINO_DB_HOST, MINO_DB_USER, MINO_DB_PASS, MINO_DB_NAME
|
*/
$active_group = 'default';
$query_builder = TRUE;

$host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : 'localhost';
$is_local = (
	$host === 'localhost'
	|| $host === '127.0.0.1'
	|| strpos($host, 'localhost:') === 0
	|| strpos($host, '127.0.0.1:') === 0
);

$db_host = getenv('MINO_DB_HOST') ?: FALSE;
$db_user = getenv('MINO_DB_USER') ?: FALSE;
$db_pass = getenv('MINO_DB_PASS');
$db_name = getenv('MINO_DB_NAME') ?: FALSE;

$shared = array(
	'dsn'	=> '',
	'dbdriver' => 'mysqli',
	'dbprefix' => '',
	'pconnect' => FALSE,
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8mb4',
	'dbcollat' => 'utf8mb4_unicode_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE,
);

if ($db_host && $db_user && $db_name !== FALSE)
{
	// Force TCP when host is localhost to avoid socket "Not a directory" errors
	if ($db_host === 'localhost')
	{
		$db_host = '127.0.0.1';
	}

	$db['default'] = array_merge($shared, array(
		'hostname' => $db_host,
		'username' => $db_user,
		'password' => ($db_pass === FALSE) ? '' : $db_pass,
		'database' => $db_name,
		'db_debug' => (ENVIRONMENT !== 'production'),
	));
}
elseif ($is_local)
{
	$db['default'] = array_merge($shared, array(
		'hostname' => '127.0.0.1',
		'username' => 'root',
		'password' => '',
		'database' => 'minicrm',
		'db_debug' => (ENVIRONMENT !== 'production'),
		'port'     => 3306,
	));
}
else
{
	/*
	| Hostinger fallback — KEEP your live credentials here on the server.
	| Do not commit real passwords to git. After deploy, edit this block on Hostinger
	| (or use env vars above).
	*/
	$db['default'] = array_merge($shared, array(
		'hostname' => 'localhost',
		'username' => 'u293323553_minicrm',
		'password' => 'CHANGE_ME_HOSTINGER_DB_PASSWORD',
		'database' => 'u293323553_minicrm',
		'db_debug' => FALSE,
	));
}
