<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Config extends REST_Controller {

    public function __construct() {
        parent::__construct();
    }

    /**
     * @url: /5050/config/itemscreen
     */
    public function itemscreen_get() {
        $result['btnconfig']  = 15;
        $result['angelratio'] = "10 thiên sứ = 1 cầu thang";
        $this->response($result);
    }

    /**
     * @url: /5050/config/playscreen
     */
    public function playscreen_get() {
        // $result['btnconfig'] = 13;
        $result['btnconfig'] = 15;
        $this->response($result);
    }

    /**
     * @url: /5050/config/version
     */
    public function version_get() {
        $result['ver']    = 7;//4;
        $result['errmsg'] = 'Đã có phiên bản mới, vui lòng cập nhật phiên bản ' . $result['ver'];
        $this->response($result);
    }

    /**
     * @url: /5050/config/startscreen
     */
    public function startscreen_get() {
        $result['ver']         = 6;
        $result['errmsg']      = 'Đã có phiên bản mới, vui lòng cập nhật phiên bản ' . $result['ver'];
        $result['progress']    = ceil(25 * 74 / 100);
        $result['progressimg'] = 'award.png';
        $result['progressurl'] = 'http://tool.cauvongso.vn/images/5050/award.png';
        $result['dailynews']   = 0;
        $result['newssize']    = 40;
        $result['newscontent'] = "Chuẩn bị xóa các tài khoản không đăng nhập từ trước 15-06-2015.";
        // $result['newscontent'] = "Cập nhật hoàn tất, mời các bạn tiếp tục cuộc đua.";
        //$result['newslink'] = 'https://facebook.com/cvs5050';
        $this->response($result);
    }

}
