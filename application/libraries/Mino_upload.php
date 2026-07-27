<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Upload helper library for logos & avatars
 */
class Mino_upload {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('upload');
	}

	/**
	 * @param string $field Input name
	 * @param string $subdir Relative under uploads/ (e.g. logos, profiles)
	 * @param array  $options
	 * @return array {success, message, path?, filename?}
	 */
	public function image($field, $subdir, $options = array())
	{
		$subdir = trim($subdir, '/');
		$path = FCPATH . 'uploads/' . $subdir . '/';
		if ( ! is_dir($path))
		{
			@mkdir($path, 0777, TRUE);
		}

		$config = array(
			'upload_path'   => $path,
			'allowed_types' => isset($options['allowed_types']) ? $options['allowed_types'] : 'jpg|jpeg|png|gif|webp',
			'max_size'      => isset($options['max_size']) ? $options['max_size'] : 2048,
			'encrypt_name'  => TRUE,
			'file_ext_tolower' => TRUE,
		);

		$this->CI->upload->initialize($config, TRUE);

		if ( ! $this->CI->upload->do_upload($field))
		{
			return array(
				'success' => FALSE,
				'message' => strip_tags($this->CI->upload->display_errors('', '')),
			);
		}

		$data = $this->CI->upload->data();
		$relative = 'uploads/' . $subdir . '/' . $data['file_name'];

		return array(
			'success'  => TRUE,
			'message'  => 'Uploaded',
			'path'     => $relative,
			'filename' => $data['file_name'],
			'full'     => $data['full_path'],
		);
	}

	public function delete_file($relative_path)
	{
		if ( ! $relative_path) return FALSE;
		$full = FCPATH . ltrim($relative_path, '/');
		if (is_file($full) && strpos(realpath($full), realpath(FCPATH . 'uploads')) === 0)
		{
			return @unlink($full);
		}
		return FALSE;
	}
}
