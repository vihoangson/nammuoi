<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

Class Account extends REST_Controller {
    const keyerror = 1;
    const errmsg = "Dữ liệu không đồng bộ, vui lòng kiểm tra lại thông tin";
    const keyid = 'xPpRsxTN4esDHLRtAJRvTFS5URF7635p';
    const iv = 'DcNtxaMP6jrcujEJ';

    /**
     * @url /5050/account/test
     */
    public function test_get(){
        $this->load->library('cvskey');
        $this->load->library('encrypt');
        $this->load->model('5050/maccount');

        $emei='1';
        $name='1';
        $refid='1';
        $result['value'] = $this->maccount->insert($emei,$name,$refid,self::keyid,self::iv);

        $this->response([2,3,4,5]);

    }

    /**
     * @url /5050/account/insert
     */
    public function insert_post() {
        $this->load->library('cvskey');
        $this->load->library('encrypt');


        $emei = $this->post('emei');
        $name = $this->post('name');
        $clientkey = $this->post('key');
        $refid = $this->post('refid');
        $check = $this->cvskey->Check2('PvCSchHCxTpaqBqC0',$clientkey);
        if (!$check) {
            $result['err'] = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }
        $result['err'] = 0;
        $result['value'] = $this->maccount->insert($emei,$name,$refid,self::keyid,self::iv);
        $this->response($result);
    }
}
