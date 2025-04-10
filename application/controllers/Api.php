<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api extends CI_Controller
{
	public function test()
	{
		$this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode([
				'status' => true,
				'message' => 'API is working.',
			]));
	}

	public function login()
	{
		$jsonData = json_decode($this->input->raw_input_stream, true);

		$_POST = $jsonData;

		$this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[3]|max_length[20]');
		$this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[6]');
		$this->form_validation->set_rules('recaptcha', 'reCAPTCHA', 'required|trim');

		if ($this->form_validation->run() == FALSE) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(400) // Bad Request
				->set_output(json_encode([
					'status' => false,
					'message' => 'There was a problem with your input.',
					'errors' => $this->form_validation->error_array()
				]));
			return;
		}

		$recaptchaSecret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
		$recaptchaResponse = $jsonData['recaptcha'];

		$verifyResponse = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptchaSecret&response=$recaptchaResponse");
		$responseData = json_decode($verifyResponse);

		if (!$responseData->success) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(400) // Bad Request
				->set_output(json_encode([
					'status' => false,
					'message' => 'reCAPTCHA verification failed.',
				]));
			return;
		}

		$this->load->model('user_model');
		$user = $this->user_model->get_user_by_username($jsonData['username']);
		$user = $user ? (object) $user : null;

		if (!$user || !password_verify($jsonData['password'], $user->password)) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(401) // Unauthorized
				->set_output(json_encode([
					'status' => false,
					'message' => 'Invalid username or password.',
				]));
			return;
		}

		$userData = [
			'id' => $user->id,
			'username' => $user->username,
			'role' => $user->role,
		];

		$this->session->set_userdata($userData);
		$this->session->set_userdata('logged_in', true);

		$this->output
			->set_content_type('application/json')
			->set_status_header(200) // OK
			->set_output(json_encode([
				'status' => true,
				'message' => 'Login successful.',
				'data' => $userData,
			]));
	}

	public function logout()
	{
		// Check if request method is DELETE
		$requestMethod = $_SERVER['REQUEST_METHOD'];

		if ($requestMethod !== 'DELETE') {
			// Return a 405 Method Not Allowed status code if not DELETE
			$this->output
				->set_content_type('application/json')
				->set_status_header(405) // Method Not Allowed
				->set_header('Allow: DELETE')
				->set_output(json_encode([
					'status' => false,
					'message' => 'Method Not Allowed',
				]));
			return;
		}

		// Check if user is actually logged in
		if (!$this->session->userdata('logged_in')) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(401) // Unauthorized
				->set_output(json_encode([
					'status' => false,
					'message' => 'You are not logged in.',
				]));
			return;
		}

		// Clear all user session data
		$this->session->unset_userdata('logged_in');
		$this->session->unset_userdata('id');
		$this->session->unset_userdata('username');
		$this->session->unset_userdata('role');

		// Return success response
		$this->output
			->set_content_type('application/json')
			->set_status_header(200) // OK
			->set_output(json_encode([
				'status' => true,
				'message' => 'Logout successful.',
			]));
	}
}
