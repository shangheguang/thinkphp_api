<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends BaseController {
	
	public function index() {
		$login_user = $this->checkLogin();
		if ($login_user) {
			header("location:" . U("Home/Item/index") );
			exit();
		}
		
		$this->assign("login_user", $login_user);
		$this->display();
	}
}