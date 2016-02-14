<?php
namespace Home\Model;
use Home\Model\BaseModel;
class UserModel extends BaseModel {
	
	//加密key
	private $ukey = 'd6g3j@8d#~!6()m2#v0b_9-k1';
	
	//用户名是否已经存在
	public function isExist($username) {
		return $this->where("username = '$username'")->find();
	}
	
	//注册新用户
	public function register($username, $password) {
		$password = md5(base64_encode(md5($password)) . $this->ukey);
		return $this->add(array(
			'username' => $username,
			'password' => $password,
			'reg_time' => time()
		));
	}
	
	//修改用户密码
	public function updatePwd($uid, $password) {
		$password = md5(base64_encode(md5($password)) . $this->ukey);
		return $this->where("uid ='$uid' ")->save(array(
			'password' => $password
		));
	}
	
	//返回用户信息
	public function userInfo($uid) {
		return $this->where("uid = '$uid'")->find();
	}
	
	//登录验证
	public function checkLogin($username, $password) {
		$password = md5(base64_encode(md5($password)) . $this->ukey);
		$where = array(
			$username,
			$password
		);
		return $this->where("username='%s' and password='%s'", $where)->find();
	}
}