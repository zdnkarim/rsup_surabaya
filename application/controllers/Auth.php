<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
	public function index()
	{
		if ($this->session->userdata('logged_in')) {
			return redirect('welcome');
		}

		return redirect('login');
	}
	public function login()
	{
		$this->load->view('auth/login');
	}
}
