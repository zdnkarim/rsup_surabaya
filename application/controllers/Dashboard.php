<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('logged_in')) {
			redirect('auth');
		}
	}

	public function index()
	{
		$data['title'] = 'Dashboard';
		$data['username'] = $this->session->userdata('username');
		$data['role'] = $this->session->userdata('role');

		$this->load->view('templates/header', $data);
		$this->load->view('dashboard/index', $data);
	}
}
