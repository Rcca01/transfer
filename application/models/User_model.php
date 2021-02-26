<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    public function get_date_user($email)
    {
        $this->db->select('pk, name, email, cpf_cnpj, fk_type, fk_status');
        $this->db->from('users');
        $this->db->where('email', $email);
        return $this->db->get()->row_array();
    }

    public function _users_is($user, $type) {
        $this->db->select('Count(pk) as user');
        $this->db->from('users');
        $this->db->where('pk', $user);
        $this->db->where('fk_type', $type);
        return ($this->db->get()->row()->user > 0);
    }

    public function user_exist($users)
    {
        for ($i=0; $i < count($users); $i++) { 
            $this->db->select('Count(pk) as user');
            $this->db->from('users');
            $this->db->where('pk', $users[$i]);
            if($this->db->get()->row()->user == 0) {
                return FALSE;
            }
        }
        return TRUE;
    }
}
