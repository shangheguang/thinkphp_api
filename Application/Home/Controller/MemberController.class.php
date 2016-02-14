<?php
namespace Home\Controller;
use Think\Controller;
class MemberController extends BaseController {
	
	//编辑页面
	public function edit() {
		$item_id = I("item_id");
		$login_user = $this->checkLogin();
		if (!$this->checkItemCreator($login_user['uid'], $item_id)) {
			$this->message("你无权限");
			return;
		}
		$this->assign("webtitle", '成员管理');
		$this->assign("login_user", $login_user);
		$this->assign("item_id", $item_id);
		
		$this->display();
	}
	
	//保存
	public function save() {
		$item_id = I("item_id");
		$login_user = $this->checkLogin();
		if (!$this->checkItemCreator($login_user['uid'], $item_id)) {
			$this->message("你无权限");
			return;
		}
		
		$user_id = I("user_id");
		$username = I("username");
		if ($user_id > 0) {
			$member = D("User")->where(" uid = '$user_id' ")->find();
		} else {
			$member = D("User")->where(" username = '$username' ")->find();
		}
		
		if (!$member) {
			$return['error_code'] = 10201;
			$return['error_message'] = '不存在此用户！';
			$this->sendResult($return);
			return;
		}
		
		$ret = D("ItemMember")->where(" uid = '{$member['uid']}' and item_id = '$item_id' ")->find();
		if ($ret) {
			$return['error_code'] = 10202;
			$return['error_message'] = '已是成员，请不要重复加入！';
			$this->sendResult($return);
			return;
		}
		
		$data['uid'] = $member['uid'];
		$data['item_id'] = $item_id;
		$data['addtime'] = time();
		
		$return = D("ItemMember")->add($data);
		if (!$return) {
			$return['error_code'] = 10103;
			$return['error_message'] = 'request fail';
		}
		
		$this->sendResult($return);
	}
	
	//获取成员列表
	public function getList() {
		$item_id = I("item_id");
		$login_user = $this->checkLogin();
		if (!$this->checkItemCreator($login_user['uid'], $item_id)) {
			$this->message("你无权限");
			return;
		}
		if ($item_id > 0) {
			$ret = D("ItemMember")->where(" item_id = '$item_id' ")->order(" 'order', addtime asc ")->select();
		}
		if ($ret) {
			foreach ($ret as $key => $value) {
				$user = D("User")->where(" uid = '{$value['uid']}' ")->find();
				$ret[$key]['username'] = (isset($user['username']) && $user['username'] != '') ? $user['username'] : '';
			}
			$this->sendResult($ret);
		} else {
			$return['error_code'] = 10103;
			$return['error_message'] = 'request fail';
			$this->sendResult($return);
		}
	}
	
	//删除成员
	public function delete() {
		$user_id = I("user_id");
		$item_id = I("item_id");
		$login_user = $this->checkLogin();
		if (!$this->checkItemCreator($login_user['uid'], $item_id)) {
			$this->message("你无权限");
			return;
		}
		if ($item_id && $user_id) {
			$ret = D("ItemMember")->where(" item_id = '$item_id' and uid = '$user_id' ")->limit(1)->delete();
		}
		if ($ret) {
			$this->sendResult($ret);
		} else {
			$return['error_code'] = 10103;
			$return['error_message'] = 'request fail';
			$this->sendResult($return);
		}
	}
}