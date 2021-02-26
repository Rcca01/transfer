<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login_model extends CI_Model
{
    public function validate_access($email, $senha)
    {
        if(!$this->_exist_user($email)) {
            return $this->_send_response('Usuário não encontrado.');
        } else {
            if(!$this->_password_valid($email, $senha)) {
                return $this->_send_response('Senha não confere');
            } else {
                return TRUE;
            }
        }
    }

    private function _exist_user($email)
    {
        $this->db->select('Count(pk) as user');
        $this->db->from('users');
        $this->db->where('users.email', $email);
        return ($this->db->get()->row()->user > 0);
    }

    private function _password_valid($email, $senha)
    {
        $this->db->select('Count(pk) as user');
        $this->db->from('users');
        $this->db->where('users.email', $email);
        $this->db->where('users.password', sha1($senha));
        return ($this->db->get()->row()->user > 0);
    }

    private function _send_response(
        $feedback = 'Falha login',
        $data = NULL,
        $status = FALSE
    ) {
        $response['status'] = $status;
        $response['data'] = $data;
        $response['feedback'] = $feedback;
        return $response;
    }
}