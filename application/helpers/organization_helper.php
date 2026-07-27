<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Organization helper functions
 */

if ( ! function_exists('current_org_id'))
{
	function current_org_id()
	{
		$CI =& get_instance();
		return isset($CI->organization_lib) ? $CI->organization_lib->id() : (int) $CI->session->userdata('organization_id');
	}
}

if ( ! function_exists('current_org_name'))
{
	function current_org_name()
	{
		$CI =& get_instance();
		return isset($CI->organization_lib) ? $CI->organization_lib->name() : (string) $CI->session->userdata('org_name');
	}
}

if ( ! function_exists('current_org_slug'))
{
	function current_org_slug()
	{
		$CI =& get_instance();
		return isset($CI->organization_lib) ? $CI->organization_lib->slug() : (string) $CI->session->userdata('org_slug');
	}
}

if ( ! function_exists('org_scope'))
{
	/**
	 * Apply organization_id scope to the active query builder
	 */
	function org_scope($table_alias = NULL)
	{
		$CI =& get_instance();
		return $CI->organization_lib->scope($table_alias);
	}
}

if ( ! function_exists('org_owns'))
{
	function org_owns($row, $column = 'organization_id')
	{
		$CI =& get_instance();
		return $CI->organization_lib->owns($row, $column);
	}
}
