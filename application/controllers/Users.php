<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
	private $currentUser;

	public function __construct()
	{
		parent::__construct();
		if (!$this->session->userdata('logged_in')) {
			redirect('auth');
		}
	}

	protected function requireLogin()
	{
		if (!$this->session->userdata('logged_in')) {
			return false;
		}

		$this->currentUser = [
			'userId' => $this->session->userdata('id'),
			'username' => $this->session->userdata('username'),
			'role' => $this->session->userdata('role'),
		];

		if ($this->currentUser['role'] == 'user') {
			return false;
		}

		return true;
	}

	protected function requireAdmin()
	{
		if (!$this->requireLogin()) {
			return false;
		}

		if ($this->currentUser['role'] != 'admin') {
			return false;
		}

		return true;
	}

	public function index()
	{
		if (!$this->requireLogin()) return redirect('login');

		$data['title'] = 'Manage Users';
		$data['username'] = $this->currentUser['username'];
		$data['role'] = $this->currentUser['role'];
		$data['userId'] = $this->currentUser['userId'];

		if ($data['role'] == 'admin') {
			$data['title'] = 'Manage Users';
		} else {
			$data['title'] = 'User Dashboard';
		}

		$this->load->view('templates/header', $data);
		$this->load->view('users/index', $data);
	}

	public function create()
	{
		if (!$this->requireAdmin()) return redirect('users');
		$data['title'] = 'Create User';
		$data['username'] = $this->currentUser['username'];
		$data['role'] = $this->currentUser['role'];
		$data['userId'] = $this->currentUser['userId'];

		$this->load->view('templates/header', $data);
		$this->load->view('users/create', $data);
	}

	public function update($id)
	{
		if (!$this->requireAdmin()) return redirect('users');
		$data['title'] = 'Update User';
		$data['username'] = $this->currentUser['username'];
		$data['role'] = $this->currentUser['role'];
		$data['userId'] = $this->currentUser['userId'];

		$data['id'] = $id;

		$this->load->view('templates/header', $data);
		$this->load->view('users/update', $data);
	}
}
