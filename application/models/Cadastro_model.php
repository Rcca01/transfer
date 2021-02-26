<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Register_model extends CI_Model
{
    public function create_user($dados)
    {
        if($this->db->insert('users', $dados)){
            $response['status'] = TRUE;
            $response['data'] = '';
            $response['feedback'] = 'Cadastro realizado';
        } else {
            $response['status'] = FALSE;
            $response['data'] = '';
            $response['feedback'] = 'Falha ao tentar cadastrar';
        }
        return $response;
    }
}
