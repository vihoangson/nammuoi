<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cvskey {

    // const privatekey = "cauv0ngd1gjt";
    const md5secretkey = "dZdNYcNXBd3Tunwr";
    const idsecretkey  = 'PvCSchHCxTpaqBqC';

    /**
     * @param $emei
     * @param $clientkey
     *
     * @return bool
     */
    public function Check($emei, $clientkey) {
        return true;
        // $date = getdate();
        // $key = md5($emei.self::md5secretkey.$date['yday']);
        // if ($key==$clientkey)
        // return true;
        // else
        // return false;
    }

    /**
     * @param $szcode
     * @param $clientkey
     *
     * @return bool
     */
    public function Check2($szcode, $clientkey) {
        return true;
        // DEBUG MOD no need check login
        if(defined("DEBUGMOD_CHECK_LOGIN") && CHECK_LOGIN == FALSE){
            return CHECK_LOGIN;
        }

        date_default_timezone_set("Asia/Saigon");
        $id          = $this->getid($szcode);
        $date        = getdate();
        $idsecretkey = $this->getidsecretkey($szcode);
        if ($idsecretkey != self::idsecretkey) {
            return false;
        }
        $key = md5($id . self::md5secretkey . $date['yday']);

        if ($key == $clientkey) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param $szcode
     *
     * @return bool|string
     */
    public function getidsecretkey($szcode) {
        $idkey = substr($szcode, 0, strlen(self::idsecretkey));

        return $idkey;
    }

    /**
     * encode $szcode to id
     *
     * @param $szcode
     *
     * @return int|string
     */
    public function getid($szcode) {
        if (strlen($szcode) <= strlen(self::idsecretkey)) {
            return 0;
        }
        $idkey  = $this->getidsecretkey($szcode);
        $id     = substr($szcode, strlen($idkey), strlen($szcode));
        $result = is_numeric($id) ? $id : 0;

        return $result;
    }

    /**
     * @param $id
     * @param $keyid
     * @param $iv
     *
     * @return mixed
     */
    public function genidkey($id, $keyid, $iv) {
        $ci =& get_instance();
        $ci->load->library('encrypt');
        $key = $ci->encrypt->sha256encrypt(self::idsecretkey . $id, $keyid, $iv);

        return $key;
    }
}

