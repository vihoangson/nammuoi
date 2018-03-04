<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require APPPATH . '/libraries/REST_Controller.php';

class Item extends REST_Controller {

    const keyerror = 1;
    // const errmsg = "Dữ liệu không đồng bộ, vui lòng cập nhật phiên bản mới";
	const errmsg = "Dữ liệu không đồng bộ, vui lòng kiểm tra lại thông tin";
    const opentime = 10;
    const closetime = 23;
    const closetimecode = 2;
    // const closetimemsg = 'Đang tổng kết trao thưởng, vui lòng quay lại sau 9h';
	const closetimemsg = 'Đang nâng cấp server, vui lòng theo dõi thông tin trên fanpage';
	const angelratio = 10;
	const ladderratio = 1;
	const keyid = 'xPpRsxTN4esDHLRtAJRvTFS5URF7635p';
	const iv = 'DcNtxaMP6jrcujEJ';
	
    function __construct() {
        parent::__construct();
        $this->load->model('5050/mitem');
        $this->load->library('cvskey');
		$this->load->library('encrypt');
    }
	
	
	function binoculars_post() {
		// $emei = $this->post('emei');
        $clientkey = $this->post('key');
		$idkey = $this->post('idkey');
		$szid = $this->encrypt->sha256decrypt($idkey,self::keyid,self::iv);
		$id = $this->cvskey->getid($szid);
		$check = $this->cvskey->Check2($szid,$clientkey);
        if (!$check) {
            $result['err'] = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }
		$result['err'] = 0;
		$result['value'] = $this->mitem->binoculars_use($id);
		if (count($result['value'])==0) {
			$result['err'] = 3;
			$result['errmsg'] = "Bạn không có ống nhòm";
		}
		$this->response($result);
	}
	
	
	function binoculars_get() {
		$result['err'] = 0;
		$result['value'] = $this->mitem->binoculars_usefree();
		$this->response($result);
	}
	
	
	function angel_post() {
		$err[0] = 'Sử dụng thiên sứ thành công';
		$err[1] = 'Bạn không có thiên sứ';
		$err[2] = 'Bạn đang có thiên sứ bảo hộ';
		// $emei = $this->post('emei');
        $clientkey = $this->post('key');
		$idkey = $this->post('idkey');
        $szid = $this->encrypt->sha256decrypt($idkey,self::keyid,self::iv);
		$id = $this->cvskey->getid($szid);
		$check = $this->cvskey->Check2($szid,$clientkey);
        if (!$check) {
            $result['err'] = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }
		$angelflag = $this->mitem->angel_use($id);
		if ($angelflag['err']!=0) {
			$result['err'] = 3;
			$result['errmsg'] = $err[$angelflag['err']];
		}
		else {
			$result['err'] = 0;
			$result['value'] = $angelflag['angelactive'];
		}
		$this->response($result);
	}
	
	
	function angel2ladder_post() {
		$this->load->model('5050/maccount');
		// $emei = $this->post('emei');
        $clientkey = $this->post('key');
		$qty = is_numeric($this->post('qty')) ? $this->post('qty') : 0;
		$idkey = $this->post('idkey');
		$szid = $this->encrypt->sha256decrypt($idkey,self::keyid,self::iv);
		$id = $this->cvskey->getid($szid);
		$check = $this->cvskey->Check2($szid,$clientkey);
        if (!$check) {
            $result['err'] = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }
		if ($qty<=0) {
			$result['err'] = 3;
			$result['errmsg'] = 'Số lượng cầu thang không hợp lệ';
			$this->response($result);
		}
		$accountinfo = $this->maccount->info($id);
		$status = $this->mitem->angel2ladder($accountinfo[0]['id'],self::angelratio,$qty);
		if ($status==1) {
			$result['err'] = 0;
			$result['errmsg'] = 'Chuyển đổi thành công';
		}
		else {
			$result['err'] = 3;
			$result['errmsg'] = 'Bạn không đủ '.($qty*self::angelratio).' thiên sứ';
		}
		$this->response($result);
	}
	
	
	function ladder_post() {
		$err[0] = 'Sử dụng cầu thang thành công';
		$err[1] = 'Bạn không có cầu thang';
		// $emei = $this->post('emei');
        $clientkey = $this->post('key');
		$idkey = $this->post('idkey');
		$szid = $this->encrypt->sha256decrypt($idkey,self::keyid,self::iv);
		$id = $this->cvskey->getid($szid);
		$check = $this->cvskey->Check2($szid,$clientkey);
        if (!$check) {
            $result['err'] = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }
		if (!$this->CheckTime()) {
            $result['err'] = self::closetimecode;
            $result['errmsg'] = self::closetimemsg;
            $this->response($result);
        }
		$ladderflag = $this->mitem->ladder_use($id);
		if ($ladderflag>0) {
			$result['err'] = 3;
			$result['errmsg'] = $err[$ladderflag];
		}
		else {
			$result['err'] = 0;
		}
		$this->response($result);
	}
	
	
	function angeltrade_post() {
		$this->load->model('5050/maccount');
		$err[1] = 'Chuyển thiên sứ thành công';
		$err[2] = 'Bạn không đủ thiên sứ';
		// $emei = $this->post('emei');
        $clientkey = $this->post('key');
		$rid = is_numeric($this->post('rid')) ? $this->post('rid') : 0;
		$qty = is_numeric($this->post('qty')) ? $this->post('qty') : 0;
		$idkey = $this->post('idkey');
        $szid = $this->encrypt->sha256decrypt($idkey,self::keyid,self::iv);
		$id = $this->cvskey->getid($szid);
		$check = $this->cvskey->Check2($szid,$clientkey);
		if (!$check) {
            $result['err'] = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }
		if ($qty<=0) {
			$result['err'] = 3;
            $result['errmsg'] = 'Số lượng thiên sứ không hợp lệ';
            $this->response($result);
		}
		if ($rid<=0) {
			$result['err'] = 3;
            $result['errmsg'] = 'ID người nhận không hợp lệ';
            $this->response($result);
		}
		$accountinfo = $this->maccount->info($id);
		if (count($accountinfo)==0) {
			$result['err'] = 3;
            $result['errmsg'] = 'Không tìm thấy tài khoản';
            $this->response($result);
		}
		if ($accountinfo[0]['id']==$rid) {
			$result['err'] = 3;
            $result['errmsg'] = 'Giao dịch không được chấp nhận';
            $this->response($result);
		}
		$checkid = $this->maccount->CheckExistsID($rid);
		if ($checkid!=1) {
			$result['err'] = 3;
            $result['errmsg'] = 'Không tìm thấy người nhận';
            $this->response($result);
		}
		$status = $this->mitem->angeltrade($accountinfo[0]['id'],$rid,$qty);
		if ($status!=1) {
			$result['err'] = 3;
            $result['errmsg'] = $err[$status];
		}
		else {
			$result['err'] = 0;
            $result['errmsg'] = $err[$status];
		}
		$this->response($result);
	}
	
	
	function laddertrade_post() {
		$this->load->model('5050/maccount');
		$err[1] = 'Chuyển cầu thang thành công';
		$err[2] = 'Bạn không đủ cầu thang';
		// $emei = $this->post('emei');
        $clientkey = $this->post('key');
		$rid = is_numeric($this->post('rid')) ? $this->post('rid') : 0;
		$qty = is_numeric($this->post('qty')) ? $this->post('qty') : 0;
		$idkey = $this->post('idkey');
        $szid = $this->encrypt->sha256decrypt($idkey,self::keyid,self::iv);
		$id = $this->cvskey->getid($szid);
		$check = $this->cvskey->Check2($szid,$clientkey);
		if (!$check) {
            $result['err'] = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }
		if ($qty<=0) {
			$result['err'] = 3;
            $result['errmsg'] = 'Số lượng cầu thang không hợp lệ';
            $this->response($result);
		}
		if ($rid<=0) {
			$result['err'] = 3;
            $result['errmsg'] = 'ID người nhận không hợp lệ';
            $this->response($result);
		}
		$accountinfo = $this->maccount->info($id);
		if (count($accountinfo)==0) {
			$result['err'] = 3;
            $result['errmsg'] = 'Không tìm thấy tài khoản';
            $this->response($result);
		}
		if ($accountinfo[0]['id']==$rid) {
			$result['err'] = 3;
            $result['errmsg'] = 'Giao dịch không được chấp nhận';
            $this->response($result);
		}
		$checkid = $this->maccount->CheckExistsID($rid);
		if ($checkid!=1) {
			$result['err'] = 3;
            $result['errmsg'] = 'Không tìm thấy người nhận';
            $this->response($result);
		}
		$status = $this->mitem->laddertrade($accountinfo[0]['id'],$rid,$qty);
		if ($status!=1) {
			$result['err'] = 3;
            $result['errmsg'] = $err[$status];
		}
		else {
			$result['err'] = 0;
            $result['errmsg'] = $err[$status];
		}
		$this->response($result);
	}
	
	
	function giftcode_post() {
		$err[0] = '';
		$err[1] = '';
		// $emei = $this->post('emei');
        $clientkey = $this->post('key');
		$code = $this->post('giftcode');
		$idkey = $this->post('idkey');
        $szid = $this->encrypt->sha256decrypt($idkey,self::keyid,self::iv);
		$id = $this->cvskey->getid($szid);
		$check = $this->cvskey->Check2($szid,$clientkey);
        if (!$check) {
            $result['err'] = self::keyerror;
            $result['errmsg'] = self::errmsg;
            $this->response($result);
        }
		if ($code=='' || $code==null) {
			$result['err'] = 3;
            $result['errmsg'] = 'Giftcode khônng hợp lệ';
            $this->response($result);
		}
		$status = $this->mitem->giftcode($id,$code);
		$this->response($status);
	
	}
	
	private function CheckTime() {
        date_default_timezone_set ("Asia/Saigon");
        $hour = date('H');
        if (($hour >= self::closetime) || ($hour < self::opentime))
            return false;
        else 
            return true;
    }

}
