<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Email template sender (dev-safe: logs when mail fails)
 */
class Mino_mail {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('email');
	}

	public function send_template($to, $subject, $template, $data = array())
	{
		$body = $this->CI->load->view('emails/' . $template, $data, TRUE);

		$config = array(
			'mailtype' => 'html',
			'charset'  => 'utf-8',
			'newline'  => "\r\n",
		);
		$this->CI->email->initialize($config);
		$this->CI->email->from('noreply@minocrm.local', 'Mino CRM');
		$this->CI->email->to($to);
		$this->CI->email->subject($subject);
		$this->CI->email->message($body);

		$ok = @$this->CI->email->send(FALSE);
		if ( ! $ok)
		{
			log_message('info', 'Email to ' . $to . ' [' . $subject . '] — ' . $this->CI->email->print_debugger(array('headers')));
			// Always succeed in foundation so flows are not blocked without SMTP
			log_message('info', 'Email body logged for ' . $to . ': ' . substr(strip_tags($body), 0, 200));
		}
		$this->CI->email->clear(TRUE);
		return TRUE;
	}
}
