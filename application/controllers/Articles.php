<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Articles extends CI_Controller
{
	private $currentUser;

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

		return true;
	}

	protected function requireEditor()
	{
		if (!$this->requireLogin()) {
			return false;
		}

		if ($this->currentUser['role'] == 'user') {
			return false;
		}

		return true;
	}

	public function index()
	{
		if (!$this->requireLogin()) return redirect('login');

		$data['title'] = 'Article';
		$data['username'] = $this->currentUser['username'];
		$data['role'] = $this->currentUser['role'];
		$data['userId'] = $this->currentUser['userId'];

		$this->load->view('templates/header', $data);
		$this->load->view('articles/index', $data);
	}

	public function create()
	{
		if (!$this->requireEditor()) return redirect('articles');
		$data['title'] = 'Create Articles';
		$data['username'] = $this->currentUser['username'];
		$data['role'] = $this->currentUser['role'];
		$data['userId'] = $this->currentUser['userId'];

		$this->load->view('templates/header', $data);
		$this->load->view('articles/create', $data);
	}

	public function update($id)
	{
		if (!$this->requireEditor()) return redirect('articles');
		$data['title'] = 'Update Article';
		$data['username'] = $this->currentUser['username'];
		$data['role'] = $this->currentUser['role'];
		$data['userId'] = $this->currentUser['userId'];

		$data['id'] = $id;

		$this->load->view('templates/header', $data);
		$this->load->view('articles/update', $data);
	}
}
