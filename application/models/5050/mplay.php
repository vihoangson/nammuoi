<?php
class Mplay extends CI_Model {
    function __construct() {
        parent::__construct();
	$this->db5050 = $this->load->database('p5050',TRUE);
    }
    
    function CheckLife($id) {
        $sql = "SELECT life FROM tbluser WHERE id=$id LIMIT 1";
        $query = $this->db5050->query($sql);
        if ($query->num_rows()==0)
            return 0;
        $rec = $query->result_array();
        return $rec[0]['life'];
    }
    
    
    function CheckFloor($id) {
        $sql = "SELECT currentfloor,skin,DATE_FORMAT(skinexpired,'%Y%m%d') as skinex FROM tbluser WHERE id='$id' LIMIT 1";
        $query = $this->db5050->query($sql);
        if ($query->num_rows()==0)
            return 0;
        $rec = $query->result_array();
        return $rec[0];
    }
    
    function dice($id) {
		$this->load->model('5050/mitem');
        if ($this->CheckLife($id)<=0)
            return -1;
        $nResult = $this->getrand($id);
		if ($this->mitem->angel_have($id)>0)
			$nResult = 1;
        if ($nResult==1)
            $sql = "UPDATE tbluser SET currentfloor=currentfloor+1,angelactive=0,lastlogin=now() WHERE id=$id";
        else
            $sql = "UPDATE tbluser SET life=life-1, lastlogin=now(),angel=angel+1 WHERE id=$id";
        $query = $this->db5050->query($sql);
        $this->WriteLog($id,'play',$nResult);
        return $nResult;
    }
    
    private function WriteLog($id,$action,$result) {
        date_default_timezone_set ("Asia/Saigon");
        $filename = $_SERVER['DOCUMENT_ROOT'].'/log/'.date('Ymd').'.log';
        $file = fopen($filename,'a');
        fwrite($file, date("H:i:s").'	'.$_SERVER['REMOTE_ADDR'].' '.$id.'	'.$action.'	'.$result."\n");
        fclose($file);
    }
    
    private function getrand($id) {
        $arrRate = array(98,95,90,85,80,75,70,60);
		// $arrRate = array(100,90,80,70,60);
        $info = $this->CheckFloor($id);
		$floor = 0;
		$skin = 0;
		$skinex = 0;
		$date = date('Ymd');
		if (count($info)>0) {
			$floor = $info['currentfloor'];
			$skin = $info['skin'];
			$skinex = is_null($info['skinex']) ? $date : $info['skinex'];
		}
		if ($date > $skinex)
			$skin = 0;
		$nRate = (isset($arrRate[$floor])) ? $arrRate[$floor] : 50;
		if ($skin==1)
			$nRate = $nRate + 5;
        $nRand = rand(1,100000);
        $nResult = $nRand ;//% 100;
        if ($nResult<$nRate*1000) {
            return 1;
        }
        else {
            return 0;
        }
    }
}
?>
