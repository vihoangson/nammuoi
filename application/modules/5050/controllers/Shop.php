<?php defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Shop extends REST_Controller {

    const keyerror = 1;

    function __construct() {
        parent::__construct();
    }

    /**
     * Shop show
     *
     * @param string idkey
     * @param string key
     *
     * @method get
     * @url /5050/shop/show
     *
     * @return void json
     * todo: se quay tro lai sau
     */
    public function show_get() {
        $idKey     = $this->get('key');
        $date      = getdate();
        $dateIndex = $date['yday'] + 1;
        $keyStr    = 'testCVS' . $dateIndex . $idKey;
        $checkkey  = md5($keyStr);

        $result['key'] = $keyStr;
        $result['md5'] = $checkkey;
        $this->response($result);
    }

    /**
     * Shop reset
     *
     * @param string idkey
     * @param string key
     *
     * @method post
     * @url /5050/shop/reset
     *
     * @return void json
     * todo: se quay tro lai sau
     */
    public function reset_post() {
        $clientkey = $this->post('key');
        $idkey     = $this->post('idkey');

        // if (!$this->CheckTime()) {
        // $result['err'] = self::closetimecode;
        // $result['errmsg'] = self::closetimemsg;
        // $this->response($result);
        // }
        $errcode           = $idkey;
        $result['errCode'] = $errcode;
        $result['errMsg']  = $clientkey;
        if ($this->CheckKey($idkey, $clientkey) == false) {
            $errcode = 1;
        }
        switch ($errcode) {
            case 0:
                $result['errMsg'] = 'Hoi sinh thanh cong';
            break;
            case 1:
                $result['errMsg'] = 'Key khong hop le';
            break;
            case 2:
                $result['errMsg'] = 'Tai khoan khong ton tai';
            break;
            case 3:
                $result['errMsg'] = 'Dang trong thoi gian tong ket trao thuong';
            break;
            case 4:
                $result['errMsg'] = 'Tai khoan nay da het luot reset trong ngay';
            break;
            default:
                $result['errMsg'] = 'Loi khong xac dinh';
        }
        $this->response($result);
    }

    private function CheckKey($idKey, $clientKey) {
        $date      = getdate();
        $dateIndex = $date['yday'] + 1;
        $keyStr    = 'testCVS' . $dateIndex . $idKey;
        $checkkey  = md5($keyStr);

        if ($clientKey == $checkkey) {
            return true;
        } else {
            return false;
        }
    }
}

?>