<?php
class Maward extends CI_Model {
    function __construct() {
        parent::__construct();
	$this->db5050 = $this->load->database('db5050',TRUE);
    }
    
    function Get7Next() {
        $sql = "SELECT 
                    releasedate,awardname,color,count(*) as qty FROM tblaward 
                WHERE 
                    (releasedate IS NOT NULL) and 
                    (releasedate > now()) and 
                    (releasedate <= Date_add(now(),INTERVAL 7 DAY)) and
                    (owner is null or owner = 0)
                GROUP BY awardname,releasedate,color
                ORDER BY releasedate ASC, awardname DESC";
         $query = $this->db5050->query($sql);
         $result = $query->result_array();
         return $result;
    }
    
    function Received($id) {
		$id = is_numeric($id) ? $id : 0;
        $sql = "UPDATE tbluser SET newaward=0 WHERE id=$id";
        $query = $this->db5050->query($sql);
        $sql = "SELECT 
                    awardname,IFNULL(awardserial,'') as awardserial,awardcode,receivedate as releasedate
                FROM tblawardnotice
                WHERE uid=$id
                ORDER BY receivedate DESC";
        $query = $this->db5050->query($sql);
        $result = $query->result_array();
        return $result;
    }
    
    function TodayAward() {
        $sql = "SELECT 
                    awardname,count(1) as qty FROM tblaward 
                WHERE 
                    (releasedate IS NOT NULL) and 
                    releasedate >= CURRENT_DATE AND 
                    releasedate <  CURRENT_DATE + INTERVAL 1 DAY
                GROUP BY awardname";
        $query = $this->db5050->query($sql);
        $result = $query->result_array();
        return $result;
    }
    
    function NewAward($id) {
		$id = is_numeric($id) ? $id : 0;
        $sql = "SELECT newaward FROM tbluser where id=$id LIMIT 1";
        $query = $this->db5050->query($sql);
        $rec = $query->result_array();
        $result = 0;
        if (count($rec)>0)
            $result = (int)$rec[0]['newaward'];
        return $result;
    }
}
?>
