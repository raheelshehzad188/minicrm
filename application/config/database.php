<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| Local XAMPP uses root / empty password / minicrm.
| Hostinger: set credentials below OR via environment variables:
|   MINO_DB_HOST, MINO_DB_USER, MINO_DB_PASS, MINO_DB_NAME
*/
$active_group = 'default';
$query_builder = TRUE;

$host = isset($_SERVER['HTTP_HOST']) ? strtolower($_SERVER['HTTP_HOST']) : 'localhost';
$is_local = (strpos($host, 'localhost') !== FALSE || $host === '127.0.0.1');

if ($is_local)
{
	$db['default'] = array(
		'dsn'	=> '',
		'hostname' => 'localhost',
		'username' => 'root',
		'password' => '',
		'database' => 'minicrm',
		'dbdriver' => 'mysqli',
		'dbprefix' => '',
		'pconnect' => FALSE,
		'db_debug' => (ENVIRONMENT !== 'production'),
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8mb4',
		'dbcollat' => 'utf8mb4_unicode_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array(),
		'save_queries' => TRUE
	);
}
else
{
	// Hostinger / production — prefer env vars; fallback to common Hostinger names
	$db['default'] = array(
		'dsn'	=> '',
		'hostname' => getenv('MINO_DB_HOST') ?: 'localhost',
		'username' => getenv('MINO_DB_USER') ?: 'u293323553_minicrm',
		'password' => getenv('MINO_DB_PASS') ?: '', // SET THIS on server if empty
		'database' => getenv('MINO_DB_NAME') ?: 'u293323553_minicrm',
		'dbdriver' => 'mysqli',
		'dbprefix' => '',
		'pconnect' => FALSE,
		'db_debug' => (ENVIRONMENT !== 'production'),
		'cache_on' => FALSE,
		'cachedir' => '',
		'char_set' => 'utf8mb4',
		'dbcollat' => 'utf8mb4_unicode_ci',
		'swap_pre' => '',
		'encrypt' => FALSE,
		'compress' => FALSE,
		'stricton' => FALSE,
		'failover' => array(),
		'save_queries' => TRUE
	);
}
