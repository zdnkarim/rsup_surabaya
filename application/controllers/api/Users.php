<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{
	private $currentUser;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('user_model');
	}

	protected function requireLogin()
	{
		if (!$this->session->userdata('logged_in')) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(401)
				->set_output(json_encode([
					'status' => false,
					'message' => 'You are not logged in.',
				]));
			return false;
		}

		$this->currentUser = [
			'role' => $this->session->userdata('role')
		];

		return true;
	}

	protected function requireAdmin()
	{
		if (!$this->requireLogin()) {
			return false;
		}

		if ($this->currentUser['role'] != 'admin') {
			$this->output
				->set_content_type('application/json')
				->set_status_header(403)
				->set_output(json_encode([
					'status' => false,
					'message' => 'You do not have permission to access this resource.',
				]));
			return false;
		}

		return true;
	}

	public function index()
	{
		if (!$this->requireLogin()) return;


		$page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
		$limit = $this->input->get('limit') ? (int)$this->input->get('limit') : 10;
		$offset = ($page - 1) * $limit;

		$result = $this->user_model->get_users($limit, $offset);

		if ($result['users']) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => true,
					'data' => $result['users'],
					'pagination' => [
						'total' => $result['total'],
						'page' => $page,
						'limit' => $limit,
						'total_pages' => ceil($result['total'] / $limit)
					]
				]));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_status_header(404)
				->set_output(json_encode([
					'status' => false,
					'message' => 'No users found.',
				]));
		}
	}

	public function store()
	{
		if (!$this->requireAdmin()) return;

		$jsonData = json_decode($this->input->raw_input_stream, true);

		$_POST = $jsonData;

		$this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]|max_length[20]|is_unique[users.username]');
		$this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[6]');
		$this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'required|trim|matches[password]');
		$this->form_validation->set_rules('role', 'Role', 'required|trim|in_list[admin,editor,user]');

		if ($this->form_validation->run() == FALSE) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(400)
				->set_output(json_encode([
					'status' => false,
					'message' => 'There was a problem with your input.',
					'errors' => $this->form_validation->error_array()
				]));
			return;
		}

		$userData = [
			'username' => $jsonData['username'],
			'password' => password_hash($jsonData['password'], PASSWORD_BCRYPT),
			'role' => $jsonData['role'],
		];


		$this->user_model->create_user($userData);

		$this->output
			->set_content_type('application/json')
			->set_status_header(201)
			->set_output(json_encode([
				'status' => true,
				'message' => 'User created successfully.',
			]));
	}

	public function show($id)
	{
		if (!$this->requireAdmin()) return;

		$user = $this->user_model->get_user_by_id($id);

		if ($user) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => true,
					'data' => $user,
				]));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_status_header(404)
				->set_output(json_encode([
					'status' => false,
					'message' => 'User not found.',
				]));
		}
	}

	public function update($id)
	{
		if (!$this->requireAdmin()) return;

		$jsonData = json_decode($this->input->raw_input_stream, true);

		$this->form_validation->set_data($jsonData);
		$this->form_validation->set_rules('role', 'Role', 'required|trim|in_list[admin,editor,user]');

		$existingUser = $this->user_model->get_user_by_id($id);
		if ($existingUser['username'] != $jsonData['username']) {
			$this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]|max_length[20]|is_unique[users.username]');
		}

		if ($jsonData['password'] != null) {
			$this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[6]');
			$this->form_validation->set_rules('confirmPassword', 'Confirm Password', 'required|trim|matches[password]');
		}

		if ($this->form_validation->run() == FALSE) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(400)
				->set_output(json_encode([
					'status' => false,
					'message' => 'There was a problem with your input.',
					'errors' => $this->form_validation->error_array()
				]));
			return;
		}

		$userData = [
			'username' => $jsonData['username'],
			'role' => $jsonData['role'],
		];
		if ($jsonData['password'] != null) {
			$userData['password'] = password_hash($jsonData['password'], PASSWORD_BCRYPT);
		}

		$this->user_model->update_user($id, $userData);
		$this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode([
				'status' => true,
				'message' => 'User updated successfully.',
			]));
	}

	public function delete($id)
	{
		if (!$this->requireAdmin()) return;

		$user = $this->user_model->get_user_by_id($id);

		if ($user) {
			$this->user_model->delete_user($id);
			$this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => true,
					'message' => 'User deleted successfully.',
				]));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_status_header(404)
				->set_output(json_encode([
					'status' => false,
					'message' => 'User not found.',
				]));
		}
	}
}
