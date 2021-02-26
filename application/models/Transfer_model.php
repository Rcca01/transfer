<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Transfer_model extends CI_Model
{
    private function _start_model($model) {
        $CI =& get_instance();
        $CI->load->model($model);
        return $CI;
    }

    public function send_transaction($payer,$payee,$value)
    {
        $CI = $this->_start_model('user_model');
        if($CI->user_model->user_exist([$payer, $payee])) {
            if ($CI->user_model->_users_is($payer, Constants::LOJISTA)){
                return $this->_send_response('Lojista não pode realizar transação de transferência');
            } else if($payer == $payee) { 
                return $this->_send_response('Você não pode enviar dinheiro para você mesmo');
            } else {
                $transaction = $this->_register_transaction($payer,$payee,$value);
                if($transaction) {
                    return $this->_send_response('Transação realizada', $transaction, TRUE);
                } else {
                    return $this->_send_response('Falha ao realizar transação');
                }
            }
        } else {
            return $this->_send_response('Usuário não encontrado');
        }
    }

    private function _register_transaction($payer,$payee,$value) {
        $transaction = array(
            'payer' => $payer, 
            'payee' => $payee, 
            'value' => number_format(str_replace(",",".",str_replace(".","",$value)), 2, '.', ''), 
            'key_transaction' => sha1(date("Y-m-d H:i:s")),
            'fk_status_transaction' => Constants::PENDENTE
        );
        if($this->db->insert('transactions', $transaction)) {
            return $transaction['key_transaction'];
        } else {
            return FALSE;
        }
    }

    public function status_transaction($key) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://run.mocky.io/v3/8fafdd68-a090-496f-8c9a-3442cf30dae6",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET"
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return $this->_send_response('Falha', $Err, FALSE);
        } else {
            return $this->_send_response('Validação realizada', json_decode($response, true), TRUE);
        }
    }

    public function update_status_transaction($key, $status)
    {
        $this->db->set('date_validation', date("Y-m-d H:i:s"));
        if($statu == Constants::AUTORIZADO_STRING)
            $this->db->set('fk_status_transaction', Constants::AUTORIZADO);
        if($statu == Constants::NEGADO_STRING)
            $this->db->set('fk_status_transaction', Constants::NEGADO);
        $this->db->where('key_transaction', $key);
        if($this->db->update('transactions')) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function _send_response(
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
