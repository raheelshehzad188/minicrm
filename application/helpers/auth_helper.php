<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Auth helper functions
 */

if ( ! function_exists('logged_in'))
{
	function logged_in()
	{
		$CI =& get_instance();
		return isset($CI->auth_lib) ? $CI->auth_lib->logged_in() : FALSE;
	}
}

if ( ! function_exists('current_user_id'))
{
	function current_user_id()
	{
		$CI =& get_instance();
		return isset($CI->auth_lib) ? $CI->auth_lib->user_id() : 0;
	}
}

if ( ! function_exists('current_user_name'))
{
	function current_user_name()
	{
		$CI =& get_instance();
		return isset($CI->auth_lib) ? $CI->auth_lib->user_name() : '';
	}
}

if ( ! function_exists('current_user_email'))
{
	function current_user_email()
	{
		$CI =& get_instance();
		return (string) $CI->session->userdata('user_email');
	}
}

if ( ! function_exists('current_role_name'))
{
	function current_role_name()
	{
		$CI =& get_instance();
		return (string) $CI->session->userdata('role_name');
	}
}

if ( ! function_exists('can'))
{
	function can($permission)
	{
		$CI =& get_instance();
		return isset($CI->permission_lib) ? $CI->permission_lib->can($permission) : FALSE;
	}
}

if ( ! function_exists('cannot'))
{
	function cannot($permission)
	{
		return ! can($permission);
	}
}

if ( ! function_exists('is_owner'))
{
	function is_owner()
	{
		$CI =& get_instance();
		return isset($CI->permission_lib) ? $CI->permission_lib->is_owner() : FALSE;
	}
}

if ( ! function_exists('is_admin'))
{
	function is_admin()
	{
		$CI =& get_instance();
		return isset($CI->permission_lib) ? $CI->permission_lib->is_admin() : FALSE;
	}
}

if ( ! function_exists('user_initials'))
{
	function user_initials($name = NULL)
	{
		$name = $name !== NULL ? $name : current_user_name();
		$parts = preg_split('/\s+/', trim($name));
		if ( ! $parts || ! $parts[0])
		{
			return 'U';
		}
		$initials = strtoupper(substr($parts[0], 0, 1));
		if (isset($parts[1]))
		{
			$initials .= strtoupper(substr($parts[1], 0, 1));
		}
		return $initials;
	}
}

if ( ! function_exists('csrf_field'))
{
	function csrf_field()
	{
		$CI =& get_instance();
		$name = $CI->security->get_csrf_token_name();
		$hash = $CI->security->get_csrf_hash();
		return '<input type="hidden" name="' . html_escape($name) . '" value="' . html_escape($hash) . '">';
	}
}
