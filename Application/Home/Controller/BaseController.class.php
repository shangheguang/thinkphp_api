<?php
namespace Home\Controller;
use Think\Controller;
class BaseController extends Controller {

	//消息提示框
	public function message($msg , $redirect = '') {
		$this->assign("msg" , $msg);
		$this->assign("redirect" , $redirect);
		$this->display("Common/message");
	}

	//检测是否登录
	public function checkLogin() {
		if ( ! session("login_user")) {
			$cookie_token = cookie('cookie_token');
			if ($cookie_token) {
				$ret = D("User")->where("cookie_token = '$cookie_token' ")->find();
				if ($ret && $ret['cookie_token_expire'] > time() ) {
					//$login_user = $ret ;
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
					return $login_user ;
				}
			}
			header("location:" . U('Home/User/login'));
			exit();
		}else{
			return session("login_user") ;
		}
	}

	//检测群组权限
	public function checkGroup($login_user, $group_id = 1) {
		if (!$login_user || $login_user && $login_user['groupid'] != $group_id ) {
			$this->message("你无权限");
			exit();
		}
		return $login_user ;
	}

	//返回json数据
	public function sendResult($array) {
		if (isset($array['error_code'])) {
			$result['error_code'] = $array['error_code'] ;
			$result['error_message'] = $array['error_message'] ;
		} else {
			$result['error_code'] = 0 ;
			$result['data'] = $array ;
		}
		echo json_encode($result);
	}

	//判断某用户是否为项目创建者
	protected function checkItemCreator($uid , $item_id) {
		if (!$uid) {
			return false;
		}
// 		if (session("creat_item_".$item_id)) {
// 			return true;
// 		}
		$item = D("Item")->where("item_id = '$item_id' ")->find();
		if ($item['uid'] && $item['uid'] == $uid) {
			//session("creat_item_".$item_id , 1 );
			return true;
		}
		return false;
	}

	//判断某用户是否有项目管理权限（项目成员和项目创建者）
	protected function checkItemPermn($uid , $item_id) {
		if (!$uid) {
			return false;
		}
// 		if (session("mamage_item_".$item_id)) {
// 			return true;
// 		}
		$item = D("Item")->where("item_id = '$item_id' ")->find();
		if ($item['uid'] && $item['uid'] == $uid) {
			//session("mamage_item_".$item_id , 1 );
			return true;
		}
		$ItemMember = D("ItemMember")->where("item_id = '$item_id' and uid = '$uid' ")->find();
		if ($ItemMember) {
			//session("mamage_item_".$item_id , 1 );
			return true;
		}
		return false;
	}

	//判断某用户是否有项目访问权限（公开项目的话所有人可访问，私有项目则项目成员、项目创建者和访问密码输入者可访问）
	protected function checkItemVisit($uid , $item_id) {
// 		if (session("visit_item_".$item_id)) {
// 			return true;
// 		}
		if ($this->checkItemPermn($uid , $item_id)) {
			//添加浏览记录
			$this->addViewLog($uid , $item_id);
			return true;
		} else {
			$item = D("Item")->where("item_id = '$item_id' ")->find();
			//如果是私人项目，但未设置密码，则禁止访问
			if (isset($item['item_type']) && $item['item_type'] == 1 && $item['password'] == '') {
				$this->message("项目错误或你无权限", U("Home/Item/index"));
				exit();
			}
			//如果设置了密码
			if (isset($item['password']) && $item['password'] != '') {
				$itempwder = D("ItemPwder")->where(" uid = '$uid' and item_id = '$item_id' ")->find();
				if ($itempwder) {
					//添加浏览记录
					$this->addViewLog($uid , $item_id);
					return true;
				} else {
					//跳转到输入访问密码框
					header("location:" . U("Home/Item/pwd", array('item_id' => $item_id)) );
					exit();
				}
			} else {
				//添加浏览记录
				$this->addViewLog($uid , $item_id);
				//session("visit_item_" . $item_id , 1 );
				return true;
			}
		}
	}
	
	//加入项目浏览记录
	protected function addViewLog($uid , $item_id){
		$data = array();
		$data['uid'] = $uid;
		$data['item_id'] = $item_id;
		$data['addtime'] = time();
		D("ItemViewer")->add($data, array(), true);
	}

}
