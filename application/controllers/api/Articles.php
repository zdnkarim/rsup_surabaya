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
			'userId' => $this->session->userdata('id'),
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
					'message' => 'There was a problem with your input.',
					'errors' => $this->form_validation->error_array(),
				]));
			return;
		}

		$data = [
			'title' => $this->input->post('title'),
			'content' => $this->input->post('content'),
			'category' => $this->input->post('category'),
			'user_id' => $this->input->post('userId')
		];

		if (!empty($_FILES['image']['name'])) {
			$this->load->library('upload');

			$config['upload_path'] = './uploads/images/';
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['max_size'] = 2048;
			$config['encrypt_name'] = true;

			if (!is_dir($config['upload_path'])) {
				mkdir($config['upload_path'], 0777, true);
			}

			$this->upload->initialize($config);

			if (!$this->upload->do_upload('image')) {
				$this->output
					->set_content_type('application/json')
					->set_status_header(400)
					->set_output(json_encode([
						'status' => false,
						'message' => $this->upload->display_errors(),
					]));
				return;
			} else {
				$uploadData = $this->upload->data();
				$imagePath = 'uploads/images/' . $uploadData['file_name'];
				$data['image_path'] = $imagePath;
			}
		}

		if (!empty($_FILES['file']['name'])) {
			$this->load->library('upload');

			$config['upload_path'] = './uploads/files/';
			$config['allowed_types'] = 'pdf';
			$config['max_size'] = 5120;
			$config['encrypt_name'] = true;

			if (!is_dir($config['upload_path'])) {
				mkdir($config['upload_path'], 0777, true);
			}

			$this->upload->initialize($config);

			if (!$this->upload->do_upload('file')) {
				$this->output
					->set_content_type('application/json')
					->set_status_header(400)
					->set_output(json_encode([
						'status' => false,
						'message' => $this->upload->display_errors(),
					]));
				return;
			} else {
				$uploadData = $this->upload->data();
				$filePath = 'uploads/files/' . $uploadData['file_name'];
				$data['pdf_path'] = $filePath; // Using pdf_path as per database structure
			}
		}

		$articles = $this->article_model->create_article($data);

		if ($articles) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(201)
				->set_output(json_encode([
					'status' => true,
					'message' => 'Article created successfully',
				]));
		} else {
			if (isset($data['image_path']) && file_exists('./' . $data['image_path'])) {
				unlink('./' . $data['image_path']);
			}
			if (isset($data['pdf_path']) && file_exists('./' . $data['pdf_path'])) {
				unlink('./' . $data['pdf_path']);
			}

			$this->output
				->set_content_type('application/json')
				->set_status_header(400)
				->set_output(json_encode([
					'status' => false,
					'message' => 'There was a problem creating the article.',
				]));
		}
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
					'errors' => $this->form_validation->error_array(),
				]));
			return;
		}

		$article = $this->article_model->get_article_by_id($id);
		if (!$article) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(404)
				->set_output(json_encode([
					'status' => false,
					'message' => 'Article not found.',
				]));
			return;
		}

		$data = [
			'title' => $this->input->post('title'),
			'content' => $this->input->post('content'),
			'category' => $this->input->post('category'),
		];

		if (!empty($_FILES['image']['name'])) {
			$this->load->library('upload');

			$config['upload_path'] = './uploads/images/';
			$config['allowed_types'] = 'gif|jpg|jpeg|png';
			$config['max_size'] = 2048;
			$config['encrypt_name'] = TRUE;

			if (!is_dir($config['upload_path'])) {
				mkdir($config['upload_path'], 0777, TRUE);
			}

			$this->upload->initialize($config);

			if (!$this->upload->do_upload('image')) {
				$this->output
					->set_content_type('application/json')
					->set_status_header(400)
					->set_output(json_encode([
						'status' => false,
						'message' => 'There was a problem uploading your image.',
						'errors' => $this->upload->display_errors(),
					]));
				return;
			} else {
				if (!empty($article['image_path'] && file_exists($article['image_path']))) {
					unlink($article['image_path']);
				}

				$uploadData = $this->upload->data();
				$imagePath = './uploads/images/' . $uploadData['file_name'];
				$data['image_path'] = $imagePath;
			}
		}

		if (!empty($_FILES['file']['name'])) {
			$this->load->library('upload');

			$config['upload_path'] = './uploads/files/';
			$config['allowed_types'] = 'pdf';
			$config['max_size'] = 5120;
			$config['encrypt_name'] = TRUE;

			if (!is_dir($config['upload_path'])) {
				mkdir($config['upload_path'], 0777, TRUE);
			}

			$this->upload->initialize($config);

			if (!$this->upload->do_upload('file')) {
				$this->output
					->set_content_type('application/json')
					->set_status_header(400)
					->set_output(json_encode([
						'status' => false,
						'message' => 'There was a problem uploading your file.',
						'errors' => $this->upload->display_errors(),
					]));
				return;
			} else {
				if (!empty($article['pdf_path']) && file_exists('./' . $article['pdf_path'])) {
					unlink('./' . $article['pdf_path']);
				}

				$uploadData = $this->upload->data();
				$filePath = 'uploads/files/' . $uploadData['file_name'];
				$data['pdf_path'] = $filePath;
			}
		}

		$success = $this->article_model->update_article($id, $data);

		if ($success) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(200)
				->set_output(json_encode([
					'status' => true,
					'message' => 'Article updated successfully.',
				]));
		} else {
			$this->output
				->set_content_type('application/json')
				->set_status_header(500)
				->set_output(json_encode([
					'status' => false,
					'message' => 'There was a problem updating your article.',
				]));
		}
	}

	public function delete($id)
	{
		if (!$this->requireEditor()) return;

		$article = $this->article_model->get_article_by_id($id);

		if ($this->currentUser['role'] == 'editor' && $article['user_id'] != $this->currentUser['userId']) {
			$this->output
				->set_content_type('application/json')
				->set_status_header(403)
				->set_output(json_encode([
					'status' => false,
					'message' => 'You do not have permission to delete this article.',
				]));
			return;
		}

		if ($article) {

			if (!empty($article['image_path']) && file_exists('./' . $article['image_path'])) {
				unlink('./' . $article['image_path']);
			}
			if (!empty($article['pdf_path']) && file_exists('./' . $article['pdf_path'])) {
				unlink('./' . $article['pdf_path']);
			}

			$success = $this->article_model->delete_article($id);

			if ($success) {
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
					->set_status_header(500)
					->set_output(json_encode([
						'status' => false,
						'message' => 'Failed to delete the article.',
					]));
			}
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
