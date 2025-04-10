<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Articles extends CI_Controller
{
	private $currentUser;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('article_model');
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

	protected function requireEditor()
	{
		if (!$this->requireLogin()) {
			return false;
		}

		if ($this->currentUser['role'] == 'user') {
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

		$keyword = $this->input->get('search') ? $this->input->get('search') : null;
		$page = $this->input->get('page') ? (int)$this->input->get('page') : 1;
		$limit = $this->input->get('limit') ? (int)$this->input->get('limit') : 5;
		$offset = ($page - 1) * $limit;
		$result = $this->article_model->get_articles($keyword, $limit, $offset);

		$this->output
			->set_content_type('application/json')
			->set_status_header(200)
			->set_output(json_encode([
				'status' => true,
				'data' => $result['articles'],
				'pagination' => [
					'total' => $result['total'],
					'page' => $page,
					'limit' => $limit,
					'total_pages' => ceil($result['total'] / $limit)
				]
			]));
	}

	public function store()
	{
		if (!$this->requireEditor()) return;

		$jsonData = json_decode($this->input->raw_input_stream, true);

		$_POST = $jsonData;

		$this->form_validation->set_rules('title', 'Title', 'required|trim|min_length[8]|max_length[20]');
		$this->form_validation->set_rules('content', 'Content', 'required|trim|min_length[20]|max_length[200]');
		$this->form_validation->set_rules('category', 'Category', 'required|trim|min_length[3]|max_length[20]');
		$this->form_validation->set_rules('userId', 'User ID', 'required|integer');

		if ($this->form_validation->run() == false) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(400)
				->set_output(json_encode([
					'status' => false,
					'message' => validation_errors(),
				]));
			return;
		}

		$data = [
			'title' => $jsonData['title'],
			'content' => $jsonData['content'],
			'category' => $jsonData['category'],
			'user_id' => $jsonData['userId']
		];

		$this->article_model->create_article($data);

		$this->output
			->set_content_type('application/json')
			->set_status_header(201)
			->set_output(json_encode([
				'status' => true,
				'message' => 'Article created successfully',
			]));
	}

	public function show($id)
	{
		if (!$this->requireEditor()) return;

		$article = $this->article_model->get_article_by_id($id);

		if ($article) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => true,
					'data' => $article,
				]));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_status_header(404)
				->set_output(json_encode([
					'status' => false,
					'message' => 'Article not found.',
				]));
		}
	}

	public function update($id)
	{
		if (!$this->requireEditor()) return;

		$jsonData = json_decode($this->input->raw_input_stream, true);

		$this->form_validation->set_data($jsonData);

		$this->form_validation->set_rules('title', 'Title', 'required|trim|min_length[8]|max_length[20]');
		$this->form_validation->set_rules('content', 'Content', 'required|trim|min_length[20]|max_length[200]');
		$this->form_validation->set_rules('category', 'Category', 'required|trim|min_length[3]|max_length[20]');

		if ($this->form_validation->run() == false) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(400)
				->set_output(json_encode([
					'status' => false,
					'message' => 'There was a problem with your input.',
					'errors' => validation_errors(),
				]));
			return;
		}

		$data = [
			'title' => $jsonData['title'],
			'content' => $jsonData['content'],
			'category' => $jsonData['category'],
		];

		$this->article_model->update_article($id, $data);

		$this->output
			->set_content_type('application/json')
			->set_status_header(201)
			->set_output(json_encode([
				'status' => true,
				'message' => 'Article created successfully',
			]));
	}

	public function delete($id)
	{
		if (!$this->requireEditor()) return;

		$article = $this->article_model->get_article_by_id($id);

		if ($article) {
			$this->article_model->delete_article($id);
			$this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => true,
					'message' => 'Article deleted successfully.',
				]));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_status_header(404)
				->set_output(json_encode([
					'status' => false,
					'message' => 'Article not found.',
				]));
		}
	}
}
