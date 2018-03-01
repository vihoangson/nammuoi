<?php

Class Account extends REST_Controller {

    function insert_post() {
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
