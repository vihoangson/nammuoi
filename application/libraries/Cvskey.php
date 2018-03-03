<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Cvskey {

    // const privatekey = "cauv0ngd1gjt";
    const md5secretkey = "dZdNYcNXBd3Tunwr";
    const idsecretkey  = 'PvCSchHCxTpaqBqC';

    public function Check($emei, $clientkey) {
        return true;
        // $date = getdate();
        // $key = md5($emei.self::md5secretkey.$date['yday']);
        // if ($key==$clientkey)
        // return true;
        // else
        // return false;
    }

    public function Check2($szcode, $clientkey) {
        // return true;
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

    public function getidsecretkey($szcode) {
        $idkey = substr($szcode, 0, strlen(self::idsecretkey));

        return $idkey;
    }

    public function getid($szcode) {
        if (strlen($szcode) <= strlen(self::idsecretkey)) {
            return 0;
        }
        $idkey  = $this->getidsecretkey($szcode);
        $id     = substr($szcode, strlen($idkey), strlen($szcode));
        $result = is_numeric($id) ? $id : 0;

        return $result;
    }

    public function genidkey($id, $keyid, $iv) {
        $ci =& get_instance();
        $ci->load->library('encrypt');
        $key = $ci->encrypt->sha256encrypt(self::idsecretkey . $id, $keyid, $iv);

        return $key;
    }
}

