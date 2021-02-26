<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Transfer extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->library('rest');
        $this->load->library('constants');
    }

    public function send()
    {
        $this->form_validation->set_rules('payer', 'Pagador', 'required|trim');
        $this->form_validation->set_rules('payee', 'Beneficiário', 'required|trim');
        $this->form_validation->set_rules('value', 'Valor', 'required|trim');
        if($this->form_validation->run() == TRUE) {
            $this->load->model('transfer_model');
            $response = $this->transfer_model->send_transaction(
                $this->input->post('payer'),
                $this->input->post('payee'),
                $this->input->post('value')
            );
            if($response['status']) {
                $result = $this->transfer_model->status_transaction($response['data']);
                if($result['status']) {
                    $this->transfer_model->update_status_transaction($response['data'], $result['data']['message']);
                }
            }
        } else {
            $response['status'] = FALSE;
            $response['data'] = $this->form_validation->error_array();
            $response['feedback'] = 'Dados incorretos';
        }
        $this->rest->response(Rest::HTTP_OK, $response);
    }
}
