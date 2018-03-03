<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Award extends REST_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('5050/maward');
        $this->load->library('cvskey');
        $this->load->library('encrypt');
    }

    /**
     * get7next
     *
     * @method get
     * @url /5050/award/get7next
     *
     * @return void json
     * todo: se quay tro lai sau
     */
    function get7next_get() {
        $result['err']   = 0;
        $result['value'] = $this->maward->Get7Next();
        $this->response($result);
    }

    /**
     * today
     *
     * @method get
     * @url /5050/award/today
     *
     * @return void json
     * todo: se quay tro lai sau
     */
    function today_get() {
        $result['err']   = 0;
        $result['value'] = $this->maward->TodayAward();
        $result['err']   = 'abc';
        $this->response($result);
    }

}

?>
