<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('rest');
        $this->load->library('constants');
    }

    public function create()
    {
        $this->form_validation->set_rules('name', 'Nome completo', 'trim|required');
        $this->form_validation->set_rules('email', 'E-mail', 'trim|required|valid_email|is_unique[users.email]');
        $this->form_validation->set_rules('type', 'Tipo', 'trim|required|in_list['.Constants::LOJISTA.','.Constants::COMUM.']',
        array('in_list' => 'O campo Tipo deve ser: '.Constants::LOJISTA.' -> LOJISTA ou '.Constants::COMUM.' -> COMUM'));
        if($this->input->post('type') == Constants::COMUM):
            $this->form_validation->set_rules('cpf', 'CPF', 'trim|required|callback_somenteNumeros|callback_checkCPF|is_unique[users.cpf_cnpj]');
        elseif($this->input->post('type') == Constants::LOJISTA):
            $this->form_validation->set_rules('cnpj', 'CNPJ', 'trim|required|callback_somenteNumeros|callback_checkCNPJ|is_unique[users.cpf_cnpj]');
        endif;
        $this->form_validation->set_rules('password', 'Senha', 'trim|required|min_length[8]');
        $this->form_validation->set_rules('passconf', 'Confirmar senha', 'trim|required|min_length[8]|matches[password]');
        if($this->form_validation->run() == TRUE) {
            $data = $this->_parse_date_register();
            $this->load->model('Register_model');
            $response = $this->register_model->create_user($data);
        } else {
            $response['status'] = FALSE;
            $response['data'] = $this->form_validation->error_array();
            $response['feedback'] = 'Dados incorretos';
        }
        $this->rest->response(Rest::HTTP_OK, $response);
    }

    private function _parse_date_register()
    {
        return array (
            'name' => $this->input->post('name'),
            'email' => $this->input->post('email'),
            'cpf_cnpj' => $this->_get_cpf_or_cnpj_to_type(),
            'password' => sha1($this->input->post('password')),
            'fk_type' => $this->input->post('type'),
            'fk_status' => Constants::ATIVO
        );
    }

    private function _get_cpf_or_cnpj_to_type(){
        switch ($this->input->post('type')) {
            case Constants::LOJISTA:
                return $this->input->post('cnpj');
                break;
            case Constants::COMUM:
                return $this->input->post('cpf');
                break;
            default:
                $this->rest->response(Rest::HTTP_FORBIDDEN, $this->rest->response403);
                break;
        }
    }

    public function checkCPF($cpf)
	{
		$this->form_validation->set_message('checkCPF', 'O CPF informado não é válido.');

		if ($cpf == ''):
			return TRUE;
		endif;

		$cpf = preg_replace('/[^0-9]/','',$cpf);
		if (strlen($cpf) != 11 OR preg_match('/^([0-9])\1+$/', $cpf)):
			return FALSE;
		endif;
		$digit = substr($cpf, 0, 9);
		for ($j=10; $j <= 11; $j++){
			$sum = 0;
			for ($i=0; $i< $j-1; $i++):
				$sum += ($j-$i) * ((int) $digit[$i]);
			endfor;
			$summod11 = $sum % 11;
			$digit[$j-1] = $summod11 < 2 ? 0 : 11 - $summod11;
		}
		return $digit[9] == ((int)$cpf[9]) && $digit[10] == ((int)$cpf[10]);
	}

    function checkCNPJ($cnpj) {

        $this->form_validation->set_message('checkCNPJ', 'O CNPJ informado não é válido.');

        if ($cnpj == ''):
			return TRUE;
		endif;
    
        // Remover caracteres especias
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    
        // Verifica se o numero de digitos informados
        if (strlen($cnpj) != 14)
            return false;
    
          // Verifica se todos os digitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj))
            return false;
    
        $b = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
    
        for ($i = 0, $n = 0; $i < 12; $n += $cnpj[$i] * $b[++$i]);
    
        if ($cnpj[12] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }
    
        for ($i = 0, $n = 0; $i <= 12; $n += $cnpj[$i] * $b[$i++]);
    
        if ($cnpj[13] != ((($n %= 11) < 2) ? 0 : 11 - $n)) {
            return false;
        }
    
        return true;
    }

    function somenteNumeros($string)
    {
        return preg_replace('/[^0-9]/', "", $string);
    }
}
