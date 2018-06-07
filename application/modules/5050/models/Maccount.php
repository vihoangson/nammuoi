<?php

class Maccount extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->db5050 = $this->load->database('db5050', true);
    }

    /**
     * @param $emei
     *
     * @return int
     */
    function CheckExists($emei) {
        // if ($emei=='ios')
        // return 0;
        $sql   = "SELECT id FROM tbluser where emei='$emei' LIMIT 1";
        $query = $this->db5050->query($sql);
        $rec   = $query->result_array();
        if (count($rec) > 0) {
            $result = $rec[0]['id'];
        } else {
            $result = 0;
        }

        return $result;
    }

    /**
     * @param $id
     *
     * @return int 0 | 1
     */
    function CheckExistsID($id) {
        $id    = is_numeric($id) ? $id : 0;
        $sql   = "SELECT 1 FROM tbluser where id=$id LIMIT 1";
        $query = $this->db5050->query($sql);
        $rec   = $query->result_array();
        if (count($rec) > 0) {
            $result = 1;
        } else {
            $result = 0;
        }

        return $result;
    }

    /**
     * @param $emei
     * @param $name
     * @param $refid
     * @param $keyid
     * @param $iv
     *
     * @return string
     */
    function insert($emei, $name, $refid, $keyid, $iv) {
        date_default_timezone_set("Asia/Saigon");
        if ($name == '' || $name == null) {
            return '';
        }
        // if ($this->CheckExists($emei)==1)
        // return '';
        $accountinfo = [
            'emei'         => $emei,
            'nickname'     => $name,
            'life'         => 1,
            'lastlogin'    => date('Y-m-d H:i:s'),
            'currentfloor' => 0,
            'angel'        => 100,
            'ladder'       => 10,
            'refid'        => $refid,
            'createdate'   => date('Y-m-d H:i:s')
        ];
        $this->db5050->insert('tbluser', $accountinfo);
        $id    = $this->db5050->insert_id();
        $idkey = $this->cvskey->genidkey($id, $keyid, $iv);

        return $idkey;
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    function info($id) {
        $sql   = "SELECT 
				id,
				emei,
				nickname,
				life,
				lastlogin,
				currentfloor,
				angel,
				ladder,
				binoculars,
				newaward,
				angelactive,
				skin,
				DATE_FORMAT(skinexpired,'%Y%m%d') as skinex
			FROM tbluser WHERE id='$id' LIMIT 1";
        $query = $this->db5050->query($sql);
        $rec   = $query->result_array();

        return $rec;
    }
}

?>
