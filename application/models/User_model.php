<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_model extends CI_Model
{
	public function get_user_by_username($username)
	{
		$this->db->where('username', $username);
		$query = $this->db->get('users');
		return $query->row_array();
	}
}
