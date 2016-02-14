<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends BaseController {
	
	public $ckey = 'f6(h7^n3r+5o6~d!9$4l%$v5f&*)-9!h';
	
	//添加/编辑用户
	public function edit() {
		$user_id = I("user_id");
		
		$User = D("User")->where(" uid = '$user_id' and status = 1 ")->find();
		if ($User) {
			$this->assign("User", $User);
		}
		
		$login_user = $this->checkLogin();
		$login_user = $this->checkGroup($login_user);
		
		$this->assign("webtitle", '添加/编辑用户');
		$this->assign("login_user", $login_user);
		$this->assign("user_id", $user_id);
		$this->assign("username", $User['username']);
		
		$this->display();
	}
	
	//保存用户
	public function savedit() {
		$username = I("username");
		$password = I("password");
		$confirm_password = I("confirm_password");
		$user_id = I("user_id") ? I("user_id") : 0;
		
		if (empty($username)) {
			$return['error_code'] = 10100;
			$return['error_message'] = '用户名不能为空！';
			$this->sendResult($return);
			return;
		}
		
		$login_user = $this->checkLogin();
		$login_user = $this->checkGroup($login_user);
		
		$data = array();
		$data['username'] = $username;
		
		if ($user_id > 0) {
			if ($password != '') {
				if ($password == $confirm_password) {
					$ret = D("User")->updatePwd($user_id, $password);
				} else {
					$return['error_code'] = 10101;
					$return['error_message'] = '两次输入的密码不一致！';
					$this->sendResult($return);
					return;
				}
			}
		} else {
			if ($password != '') {
				if ($password == $confirm_password) {
					if (!D("User")->isExist($username)) {
						$user_id = D("User")->register($username, $password);
					} else {
						$return['error_code'] = 10102;
						$return['error_message'] = '用户名已经存在！';
						$this->sendResult($return);
						return;
					}
				} else {
					$return['error_code'] = 10103;
					$return['error_message'] = '两次输入的密码不一致！';
					$this->sendResult($return);
					return;
				}
			} else {
				$return['error_code'] = 10104;
				$return['error_message'] = '新用户需设置密码';
				$this->sendResult($return);
				return;
			}
		}
		
		$return = D("User")->where(" uid = '$user_id' and status = 1 ")->find();
		if (!$return) {
			$return['error_code'] = 10105;
			$return['error_message'] = 'request fail';
		}
		$this->sendResult($return);
	}
	
	//获取用户列表
	public function userList() {
		$ret = D("User")->where(" groupid = 2 and status = 1 ")->order(" uid desc  ")->select();
		if ($ret) {
			$this->sendResult($ret);
		} else {
			$return['error_code'] = 10103;
			$return['error_message'] = 'request fail';
			$this->sendResult($return);
		}
	}
	
	//删除用户
	public function delete() {
		$user_id = I("user_id") ? I("user_id") : 0;
		$user = D("User")->where(" uid = '$user_id' and status = 1 ")->find();
		
		$login_user = $this->checkLogin();
		$login_user = $this->checkGroup($login_user);
		
		if ($user_id > 0) {
			//$ret = D("User")->where(" uid = '$user_id' ")->limit(1)->delete();
			$data = array(
				'status' => 0
			);
			$ret = D("User")->where(" uid = '$user_id' ")->save($data);
		}
		
		if ($ret) {
			$this->sendResult($ret);
		} else {
			$return['error_code'] = 10103;
			$return['error_message'] = 'request fail';
			$this->sendResult($return);
		}
	}
	
	//注册
	public function regist() {
		$this->message("本站禁止注册！", U('Home/User/login'));
		die;
		/*
		$login_user = $this->checkLogin();
		if ($login_user) {
			//如果有cookie记录，则自动登录
			$cookie_token = cookie('cookie_token');
			if ($cookie_token) {
				$ret = D("User")->where("cookie_token = '$cookie_token' ")->find();
				if ($ret && $ret['cookie_token_expire'] > time()) {
					//$login_user = $ret;
					$login_user = array(
						"uid" => $ret["uid"],
						"username" => $ret["username"],
						"groupid" => $ret["groupid"],
						"name" => $ret["name"],
						"avatar" => $ret["avatar"],
						"avatar_small" => $ret["avatar_small"],
						"email" => $ret["email"],
						"cookie_token" => $ret["cookie_token"],
						"cookie_token_expire" => $ret["cookie_token_expire"],
					);
					session("login_user", $login_user);
					$this->message("您已登录，请勿重复注册！正在跳转...", U('Home/Item/index'));
					exit();
				}
			}
		} else {
			if (!IS_POST) {
				$this->assign("webtitle", '注册');
				$this->display();
			} else {
				$username = I("username");
				$password = I("password");
				$confirm_password = I("confirm_password");
				$v_code = I("v_code");
				//不区分大小写
				if ($v_code && $v_code == strtolower(session('v_code'))) {
					if ($password != '' && $password == $confirm_password) {
						if (!D("User")->isExist($username)) {
							$ret = D("User")->register($username, $password);
							if ($ret) {
								$this->message("注册成功！", U('Home/User/login'));
							} else {
								$this->message("用户名或密码不正确");
							}
						} else {
							$this->message("用户名已经存在！");
						}
					} else {
						$this->message("两次输入的密码不一致！");
					}
				} else {
					$this->message("验证码不正确");
				}
			}
		}
		*/
	}
	
	//登录
	public function login() {
		if (!IS_POST) {
			//如果有cookie记录，则自动登录
			$cookie_token = cookie('cookie_token');
			if ($cookie_token) {
				$ret = D("User")->where("cookie_token = '$cookie_token' ")->find();
				if ($ret && $ret['cookie_token_expire'] > time()) {
					$login_user = $ret;
					session("login_user", $login_user);
					$this->message("自动登录成功！正在跳转...", U('Home/Item/index'));
					exit();
				}
			}
			$this->assign("webtitle", '登录');
			$this->display();
		} else {
			$username = I("username");
			$password = I("password");
			$v_code = I("v_code");
			if ($v_code && $v_code == strtolower(session('v_code'))) { //不区分大小写
				$ret = D("User")->checkLogin($username, $password);
				if ($ret) {
					$login_user = array(
						"uid" => $ret["uid"],
						"username" => $ret["username"],
						"groupid" => $ret["groupid"],
						"name" => $ret["name"],
						"avatar" => $ret["avatar"],
						"avatar_small" => $ret["avatar_small"],
						"email" => $ret["email"],
						"cookie_token" => $ret["cookie_token"],
						"cookie_token_expire" => $ret["cookie_token_expire"],
					);
					session("login_user", $login_user);
					$cookie_token = md5(time() . rand() . $this->ckey);
					$cookie_token_expire = time() + 60 * 60 * 24 * 90;
					cookie('cookie_token', $cookie_token, 60 * 60 * 24 * 90);
					D("User")->where(" uid = '$ret[uid]' ")->save(array(
						"last_login_time" => time(),
						"cookie_token" => $cookie_token,
						"cookie_token_expire" => $cookie_token_expire
					));
					unset($ret['password']);
					
					$this->message("登录成功！", U('Home/Item/index'));
				} else {
					$this->message("用户名或密码不正确");
				}
			} else {
				$this->message("验证码不正确");
			}
		}
	}
	
	//生成验证码
	public function verifycode() {
		//生成验证码图片
		Header("Content-type: image/PNG");
		// 画一张指定宽高的图片
		$im = imagecreate(80, 36);
		// 定义背景颜色
		$back = ImageColorAllocate($im, 245, 245, 245);
		//把背景颜色填充到刚刚画出来的图片中
		imagefill($im, 0, 0, $back);
		$vcodes = "";
		srand((double)microtime() * 1000000);
		//生成5位随机数字或字母(无01无ol防止混淆)
		$code = "23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ";
		for ($i = 0; $i < 5; $i++) {
			// 生成随机颜色
			$font = ImageColorAllocate($im, rand(0, 255), rand(0, 100), rand(0, 255));
			$authnum = $code{rand(0, strlen($code) - 1)};
			$vcodes .= $authnum;
			imagestring($im, 5, 15 + $i * 10, 8, $authnum, $font);
		}
		session("v_code", $vcodes);
		//加入干扰象素
		for ($i = 0; $i < 200; $i++) {
			$randcolor = ImageColorallocate($im, rand(0, 255), rand(0, 255), rand(0, 255));
			imagesetpixel($im, rand() % 80, rand() % 30, $randcolor); // 画像素点函数
		}
		ImagePNG($im);
		ImageDestroy($im);
	}
	
	//设置
	public function setting() {
		$login_user = $this->checkLogin();
		if (!IS_POST) {
			$this->assign("webtitle", '修改个人资料');
			$this->assign("login_user", $login_user);
			$this->display();
		} else {
			$username = $login_user['username'];
			$password = I("password");
			$new_password = I("new_password");
			if ($password != '') {
				$ret = D("User")->checkLogin($username, $password);
				if ($ret) {
					$ret = D("User")->updatePwd($login_user['uid'], $new_password);
					if ($ret !== false) {
						session("login_user", NULL);
						cookie('cookie_token', NULL);
						
						$this->message("修改成功，请重新登录！", U("Home/Item/index"));
						exit();
					} else {
						$this->message("修改失败！");
						exit();
					}
				} else {
					$this->message("原密码不正确");
					exit();
				}
			} else {
				$this->message("未做修改或修改失败！");
				exit();
			}
		}
	}
	
	//退出登录
	public function logout() {
		$login_user = $this->checkLogin();
		session("login_user", NULL);
		cookie('cookie_token', NULL);
		$this->message("退出成功！", U('Home/User/login'));
	}
}