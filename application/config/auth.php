<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Mino CRM — Authentication configuration
 * Loaded via: $this->config->load('auth', TRUE);
 * Access via: $this->config->item('key', 'auth');
 */

$config['max_login_attempts']      = 5;
$config['lockout_minutes']         = 15;
$config['attempt_window_minutes']  = 15;
$config['remember_days']           = 30;
$config['remember_cookie']         = 'mino_remember';
$config['reset_token_hours']       = 1;
$config['password_min_length']     = 8;
$config['login_redirect']          = 'dashboard';
$config['logout_redirect']         = 'auth/login';
