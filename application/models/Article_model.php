<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Article_model extends CI_Model
{
	public function get_articles($keyword, $limit = 5, $offset = 0)
	{
		$this->db->select('datas.id, title, content, category,users.id as user_id, users.username, datas.created_at');
		$this->db->join('users', 'users.id = datas.user_id');
		$this->db->where("(title LIKE '%$keyword%' OR category LIKE '%$keyword%')");
		$this->db->limit($limit, $offset);
		$query = $this->db->get('datas');

		$this->db->select('COUNT(*) as total');
		$this->db->where("(title LIKE '%$keyword%' OR category LIKE '%$keyword%')");
		$total_query = $this->db->get('datas');
		$total = $total_query->row()->total;

		if ($total == 0) {
			return [
				'articles' => [],
				'total' => $total
			];
		}

		return [
			'articles' => $query->result_array(),
			'total' => $total
		];
	}

	public function create_article($data)
	{
		$this->db->insert('datas', $data);
		return $this->db->insert_id();
	}

	public function get_article_by_id($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('datas');
		return $query->row_array();
	}

	public function update_article($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('datas', $data);
	}

	public function delete_article($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete('datas');
	}
}
