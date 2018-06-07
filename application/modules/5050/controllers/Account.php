<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . 'libraries/REST_Controller.php';

/**
 * @property Maccount   maccount
 * @property Cvskey     cvskey
 * @property MY_Encrypt encrypt
 */
Class Account extends REST_Controller {

    const keyerror = 1;
    const errmsg   = "Dữ liệu không đồng bộ, vui lòng kiểm tra lại thông tin";
    const keyid    = 'xPpRsxTN4esDHLRtAJRvTFS5URF7635p';
    const iv       = 'DcNtxaMP6jrcujEJ';

    public function __construct($config = 'rest') {
        parent::__construct($config);

        $this->load->library('cvskey');
        $this->load->library('encrypt');
        $this->load->model('maccount');

    }


    /**
     * Create account
     *
     * @param string emei
     * @param string name
     * @param string key hashkey
     * @param string refid
     *
     * @method post
     * @url /5050/account/insert
     *
     * @return void json
     */
    public function insert_post() {

        $emei      = $this->post('emei');
        $name      = $this->post('name');
        $clientkey = $this->post('key');
        $refid     = $this->post('refid');

        $check = $this->cvskey->Check2('PvCSchHCxTpaqBqC0', $clientkey);
        if (!$check) {
            $result['err']    = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }

        $result['err']   = 0;
        $result['value'] = $this->maccount->insert($emei, $name, $refid, self::keyid, self::iv);
        $this->response($result);
    }

    /**
     * Check id key exists
     *
     * @param string idkey
     * @param string emei
     * @param string key hashkey
     *
     * @method post
     * @url /5050/account/checkidkeyexists
     *
     * @return void json
     * todo: se quay tro lai sau
     */
    function checkidkeyexists_post() {

        $clientkey = $this->post('key');

        // Số id được lưu trong db sau khi được mã hóa
        $idkey = $this->post('idkey');

        // Số imei được lưu trong db
        $imei = $this->post('emei');

        $szid = $this->encrypt->sha256decrypt($idkey, self::keyid, self::iv);

        // Check thiết bị
        $checkkey = $this->cvskey->Check2($szid, $clientkey);
        // Kiểm tra không hợp lệ trả ra lỗi
        if (!$checkkey) {
            $result['err']    = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }

        // Lấy Id của user
        $id      = $this->cvskey->getid($szid);
        $checkid = $this->maccount->CheckExistsID($id);
        if ($checkid == 1) {
            $result['err']   = 0;
            $result['value'] = 1;
            $result['idkey'] = '';
            $this->response($result);
        } else {
            if ($imei != 'ios') {
                // Kiểm tra dựa vào imei
                $checkimei = $this->maccount->CheckExists($imei);
                if ($checkimei > 0) {
                    $result['err']   = 0;
                    $result['value'] = 1;
                    $result['idkey'] = $this->cvskey->genidkey($checkimei, self::keyid, self::iv);
                    $this->response($result);
                }
            } else {
                // ??? $imei == 'ios' trường hợp này là khi nào ?
                $result['err']   = 0;
                $result['value'] = 0;
                $this->response($result);
            }
        }
    }

    /**
     * Get info account
     *
     * @param string idkey
     * @param string key
     *
     * @method post
     * @url /5050/account/info
     *
     * @return void json
     * todo: se quay tro lai sau
     */
    public function info_post() {
        $keyid     = $this->post('idkey');
        $clientkey = $this->post('key');
        $szid      = $this->encrypt->sha256decrypt($keyid, self::keyid, self::iv);
        $id        = $this->cvskey->getid($szid);

        $check = $this->cvskey->Check2($szid, $clientkey);

        if (!$check) {
            $result['err']    = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }
        $result['err']   = $id;
        $result['value'] = $this->maccount->info($keyid);
        if (count($result['value']) > 0) {
            $result['value'][0]['id'] = $result['value'][0]['id'] . ' ';
            $date                     = date('Ymd');
            $skinex                   = is_null($result['value'][0]['skinex']) ? $date : $result['value'][0]['skinex'];
            if ($date > $skinex) {
                $result['value'][0]['skin'] = 0;
            }
        }
        $this->response($result);
    }
}
