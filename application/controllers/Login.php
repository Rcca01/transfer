<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('rest');
    }

    public function acessar()
    {
        $this->form_validation->set_rules('email', 'E-mail', 'required|trim|valid_email');
        $this->form_validation->set_rules('password', 'Senha', 'required|trim');
        if($this->form_validation->run() == TRUE) {
            $this->load->model('login_model');
            $response = $this->login_model->validate_access(
                $this->input->post('email'),
                $this->input->post('password')
            );
            if($response === TRUE) {
                $this->load->model('user_model');
                $user = $this->user_model->get_date_user($this->input->post('email'));
                $response = $this->_create_session($user);
            }
        } else {
            $response['status'] = FALSE;
            $response['data'] = $this->form_validation->error_array();
            $response['feedback'] = 'Login não realizado';
        }
        $this->rest->response(Rest::HTTP_OK, $response);
    }

    private function _create_session($user) {

        $this->load->library('session');

        $this->session->set_userdata([
            'pk_usuario' => $user['pk'],
            'email' => $user['email'],
            'pk_tipo' => $user['fk_type'],
            'token' => $this->_create_token_session($user['pk']),
            'logged_in' => true,
        ]);

        $response['status'] = TRUE;
        $response['data']['token'] = $this->session->userdata('token');
        $response['feedback'] = 'Login realizado';

        return $response;
    }

    private function _create_token_session($pk) {
        $this->load->library('Jwt');
        $info_token['pk'] = $pk;
        $info_token['exp'] = (time() + $this->config->item('jwtExpTime'));
        return $this->jwt->encode($info_token, $this->config->item('jwtSecretKey'));
    }
}
