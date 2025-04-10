<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
	public function get_users($limit = 10, $offset = 0)
	{
		$this->db->select('id, username, role');
		$this->db->limit($limit, $offset);
		$query = $this->db->get('users');

		$this->db->select('COUNT(*) as total');
		$total_query = $this->db->get('users');
		$total = $total_query->row()->total;

		return [
			'users' => $query->result_array(),
			'total' => $total
		];
	}

	public function create_user($data)
	{
		$this->db->insert('users', $data);
		return $this->db->insert_id();
	}

	public function get_user_by_username($username)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		return $query->row_array();
	}

	public function get_user_by_id($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('users');
		return $query->row_array();
	}

	public function update_user($id, $data)
	{
		$this->db->where('id', $id);
		return $this->db->update('users', $data);
	}

	public function delete_user($id)
	{
		$this->db->where('id', $id);
		return $this->db->delete('users');
	}
}
