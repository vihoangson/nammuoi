<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Play extends REST_Controller {

    const keyerror = 1;
    // const errmsg = "Dữ liệu không đồng bộ, vui lòng cập nhật phiên bản mới";
    const errmsg        = "Dữ liệu không đồng bộ, vui lòng kiểm tra lại thông tin";
    const opentime      = 10;
    const closetime     = 23;
    const closetimecode = 2;
    // const closetimemsg = 'Đang tổng kết trao thưởng, vui lòng quay lại sau 9h';
    const closetimemsg = 'Đang nâng cấp server, vui lòng theo dõi thông tin trên fanpage';
    const keyid        = 'xPpRsxTN4esDHLRtAJRvTFS5URF7635p';
    const iv           = 'DcNtxaMP6jrcujEJ';

    public function __construct() {
        parent::__construct();
        $this->load->model('5050/mplay');
        $this->load->library('cvskey');
        $this->load->library('encrypt');
    }

    /**
     * @param string key
     * @param string idkey
     *
     * @method post
     * @url /5050/play/checklife
     */
    public function checklife() {
        // $emei = $this->post('emei');
        $clientkey = $this->post('key');
        $idkey     = $this->post('idkey');
        $szid      = $this->encrypt->sha256decrypt($idkey, self::keyid, self::iv);
        $id        = $this->cvskey->getid($szid);
        $check     = $this->cvskey->Check2($szid, $clientkey);
        if (!$check) {
            $result['err']    = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }
        $result['err']   = 0;
        $result['value'] = $this->mplay->CheckLife($id);
        $this->response($result);
    }

    /**
     * @method post
     * @url /5050/play/dice
     */
    public function dice_post() {
        // $emei = $this->post('emei');
        $clientkey = $this->post('key');
        $idkey     = $this->post('idkey');
        $szid      = $this->encrypt->sha256decrypt($idkey, self::keyid, self::iv);
        $id        = $this->cvskey->getid($szid);
        $check     = $this->cvskey->Check2($szid, $clientkey);
        if (!$check) {
            $result['err']    = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }
        if (!$this->CheckTime()) {
            $result['err']    = self::closetimecode;
            $result['errmsg'] = self::closetimemsg;
            $this->response($result);
        }
        $result['err']   = 0;
        $result['value'] = $this->mplay->dice($id);
        if ($result['value'] == -1) {
            $result['errmsg'] = 'Bạn đã hết lượt chơi, hãy chờ tổng kết trao thưởng.';
        }
        $this->response($result);
    }

    private function CheckTime() {
        date_default_timezone_set("Asia/Saigon");
        $hour = date('H');
        if (($hour >= self::closetime) || ($hour < self::opentime)) {
            return false;
        } else {
            return true;
        }
    }

}
