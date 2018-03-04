<?php

class Mitem extends CI_Model {

    const angelratio  = 10;
    const ladderratio = 1;

    function __construct() {
        parent::__construct();
        $this->db5050 = $this->load->database('db5050', true);
    }

    function angel_check($id) {
        $sql   = "SELECT angel FROM tbluser WHERE id=$id LIMIT 1";
        $query = $this->db5050->query($sql);
        if ($query->num_rows() == 0) {
            return 0;
        }
        $rec = $query->result_array();

        return $rec[0]['angel'];
    }

    function angel_have($id) {
        $sql   = "SELECT angelactive FROM tbluser WHERE id=$id LIMIT 1";
        $query = $this->db5050->query($sql);
        if ($query->num_rows() == 0) {
            return 0;
        }
        $rec = $query->result_array();

        return $rec[0]['angelactive'];
    }

    function angel_use($id) {
        if ($this->angel_check($id) <= 0) {
            return ['err' => 1, 'angelactive' => 0];
        }
        if ($this->angel_have($id) > 0) {
            return ['err' => 2, 'angelactive' => 0];
        }
        $angelactive = rand(1, 2);
        $sql         = "UPDATE tbluser SET angel=angel-1,angelactive=$angelactive,angeltoday=angeltoday+1 WHERE id=$id";
        $query       = $this->db5050->query($sql);

        return ['err' => 0, 'angelactive' => $angelactive];
    }

    function angel2ladder($uid, $angelratio, $ladder) {
        $angelrequire = $angelratio * $ladder;
        if ($this->angel_check($uid) < $angelrequire) {
            return 2;
        }
        $sql   = "UPDATE tbluser SET angel=angel-$angelrequire, ladder=ladder+$ladder WHERE id=$uid";
        $query = $this->db5050->query($sql);
        $sql2  = "INSERT INTO tblladderchange(uid,angel,ladder,changedate) VALUES($uid,$angelrequire,$ladder,now())";
        $query = $this->db5050->query($sql2);

        return 1;
    }

    function angeltrade($uid, $tid, $qty) {
        if ($this->angel_check($uid) < $qty) {
            return 2;
        }
        $sql    = "UPDATE tbluser SET angel=angel-$qty WHERE id=$uid";
        $query  = $this->db5050->query($sql);
        $sql2   = "UPDATE tbluser SET angel=angel+$qty WHERE id=$tid";
        $query2 = $this->db5050->query($sql2);
        $sql3   = "INSERT INTO tbltradehistory(uid,tid,iid,qty,tradedate) VALUES($uid,$tid,1,$qty,now())";
        $query2 = $this->db5050->query($sql3);

        return 1;
    }


    function ladder_check($id) {
        $sql   = "SELECT ladder FROM tbluser WHERE id=$id LIMIT 1";
        $query = $this->db5050->query($sql);
        if ($query->num_rows() == 0) {
            return 0;
        }
        $rec = $query->result_array();

        return $rec[0]['ladder'];
    }

    function ladder_use($id) {
        if ($this->ladder_check($id) <= 0) {
            return 1;
        }
        $sql   = "UPDATE tbluser SET ladder=ladder-1,laddertoday=laddertoday+1,currentfloor=currentfloor+1 WHERE id=$id";
        $query = $this->db5050->query($sql);
        // $this->WriteItemLog($emei,'ladder',1)
        $this->WriteItemLog($id, 'ladder', 1);

        return 0;
    }


    function laddertrade($uid, $tid, $qty) {
        if ($this->ladder_check($uid) < $qty) {
            return 2;
        }
        $sql    = "UPDATE tbluser SET ladder=ladder-$qty WHERE id=$uid";
        $query  = $this->db5050->query($sql);
        $sql2   = "UPDATE tbluser SET ladder=ladder+$qty WHERE id=$tid";
        $query2 = $this->db5050->query($sql2);
        $sql3   = "INSERT INTO tbltradehistory(uid,tid,iid,qty,tradedate) VALUES($uid,$tid,2,$qty,now())";
        $query2 = $this->db5050->query($sql3);

        return 1;
    }


    function binoculars_check($id) {
        $sql   = "SELECT binoculars FROM tbluser WHERE id=$id LIMIT 1";
        $query = $this->db5050->query($sql);
        if ($query->num_rows() == 0) {
            return 0;
        }
        $rec = $query->result_array();

        return $rec[0]['binoculars'];
    }


    function binoculars_use($id) {
        if ($this->binoculars_check($id) <= 0) {
            return [];
        }
        $sql2                 = "SELECT currentfloor,count(1) as qty FROM tbluser WHERE currentfloor>0 GROUP BY currentfloor ORDER BY currentfloor DESC LIMIT 3";
        $query                = $this->db5050->query($sql2);
        $result               = $query->result_array();
        $resultedit[0]['msg'] = 'Top 3 hien tai:';
        $resultedit[1]['msg'] = '';
        if (count($result) > 0) {
            $sql   = "UPDATE tbluser SET binoculars=binoculars-1 WHERE id=$id";
            $query = $this->db5050->query($sql);
            for ($i = 0; $i < count($result); $i++) {
                $resultedit[$i + 2]['msg'] = '* Tang' . ' ' . $result[$i]['currentfloor'] . ': ' . $result[$i]['qty'] . ' nguoi *';
            }
        } else {
            $resultedit[2]['msg'] = 'Chua co thong tin';
        }

        return $resultedit;
    }


    function giftcode_getinfo($code) {
        $sql   = "SELECT 
					id,
					code,
					codetype,
					value,
					duration,
					DATE_FORMAT(expireddate,'%Y%m%d') as exdate,
					status 
				FROM tblgiftcode WHERE code='$code' LIMIT 1";
        $query = $this->db5050->query($sql);
        $rec   = $query->result_array();
        if (count($rec) > 0) {
            return $rec[0];
        } else {
            return [];
        }
    }


    function giftcode($id, $code) {
        $codeinfo = $this->giftcode_getinfo($code);

        if (count($codeinfo) == 0) {
            return ['err' => 3, 'errmsg' => 'Giftcode không đúng'];
        }

        $date = date("Ymd");

        if ($date > $codeinfo['exdate']) {
            return ['err' => 3, 'errmsg' => 'Giftcode đã hết hạn'];
        }

        if ($codeinfo['status'] == 0) {
            return ['err' => 3, 'errmsg' => 'Giftcode chưa kích hoạt'];
        }

        if ($codeinfo['status'] == 2) {
            return ['err' => 3, 'errmsg' => 'Giftcode sử dụng rồi'];
        }

        $result = [];
        switch ($codeinfo['codetype']) {
            case 1:
                $status = $this->giftcode_use_type1($id, $codeinfo);
                if ($status == 0) {
                    $result = ['err' => 3, 'errmsg' => 'Vui lòng thử lại vào ngày mai'];
                } else {
                    $result = ['err' => 0, 'errmsg' => 'Kích hoạt thành công, may mắn +5%'];
                }
            break;
            case 2:
                $status = $this->giftcode_use_type2($id, $codeinfo);
                if ($status == 0) {
                    $result = ['err' => 3, 'errmsg' => 'Vui lòng thử lại vào ngày mai'];
                } else {
                    $result = ['err' => 0, 'errmsg' => "Kích hoạt thành công\n+$status Thiên Sứ"];
                }
            break;
            default:
                return ['err' => 3, 'errmsg' => 'Lỗi hệ thống, vui lòng báo hỗ trợ'];
        }

        return $result;
    }


    function giftcode_use_type1($id, $codeinfo) {
        $cid      = $codeinfo['id'];
        $skin     = $codeinfo['value'];
        $duration = $codeinfo['duration'] - 1;
        if ($duration < 0) {
            $sql = "UPDATE tbluser SET skin=$skin,skinexpired=null WHERE id=$id";
        } else {
            $sql = "UPDATE tbluser SET skin=$skin,skinexpired=DATE_ADD(now(),INTERVAL $duration DAY) WHERE id=$id";
        }
        $query  = $this->db5050->query($sql);
        $status = $this->db5050->affected_rows();
        if ($status != 0) {
            $sql2  = "UPDATE tblgiftcode SET owner='$id',status=2,usedate=now() WHERE id=$cid";
            $query = $this->db5050->query($sql2);
        }

        return $status;
    }


    function giftcode_use_type2($id, $codeinfo) {
        $cid    = $codeinfo['id'];
        $angel  = $codeinfo['value'];
        $sql    = "UPDATE tbluser SET angel=angel+$angel WHERE id=$id";
        $query  = $this->db5050->query($sql);
        $status = $this->db5050->affected_rows();
        if ($status != 0) {
            $sql2  = "UPDATE tblgiftcode SET owner='$id',status=2,usedate=now() WHERE id=$cid";
            $query = $this->db5050->query($sql2);

            return $angel;
        } else {
            return 0;
        }
    }


    function binoculars_usefree() {
        $sql    = "SELECT currentfloor,count(1) as qty FROM tbluser WHERE currentfloor>0 GROUP BY currentfloor ORDER BY currentfloor DESC LIMIT 3";
        $query  = $this->db5050->query($sql);
        $result = $query->result_array();

        return $result;
    }


    private function WriteItemLog($id, $action, $result) {
        date_default_timezone_set("Asia/Saigon");
        $filename = $_SERVER['DOCUMENT_ROOT'] . '/log/' . date('Ymd') . '.log';
        $file     = fopen($filename, 'a');
        fwrite($file, date("H:i:s") . '	' . $_SERVER['REMOTE_ADDR'] . ' ' . $id . '	' . $action . '	' . $result . "\n");
        fclose($file);
    }
}

?>
