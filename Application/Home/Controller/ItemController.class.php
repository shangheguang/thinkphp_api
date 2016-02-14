<?php
namespace Home\Controller;
use Think\Controller;
class ItemController extends BaseController {
	
	//项目列表页
	public function index() {
		$login_user = $this->checkLogin();
		
		//公开项目
		$pub_items = D("Item")->where("item_type = 0 and status = 1")->select();
		
		//有管理权限的项目（创建、项目成员）
		$orsql_item_ids = '';
		$ItemMembers = D("ItemMember")->where("uid = '$login_user[uid]'")->select();
		if ($ItemMembers) {
			$arr_item = array();
			foreach ($ItemMembers as $member) {
				$arr_item[] = $member['item_id'];
			}
			if (is_array($arr_item) && count($arr_item) > 0) {
				$orsql_item_ids = ' or item_id in (' . implode(',', $arr_item) . ') ';
			}
		}
		$own_items = D("Item")->where("uid = '$login_user[uid]' $orsql_item_ids and status = 1")->select();
		
		//有浏览权限的项目
		$view_items = array();
		$ItemViewers = D("ItemViewer")->where("uid = '$login_user[uid]'")->select();
		if ($ItemViewers) {
			$arr_item = array();
			foreach ($ItemViewers as $viewer) {
				$arr_item[] = $viewer['item_id'];
			}
			if (is_array($arr_item) && count($arr_item) > 0) {
				$view_items = D("Item")->where("item_id in (" . implode(',', $arr_item) . ") and status = 1")->limit(10)->select();
			}
		}
		
		$this->assign("webtitle", '我的项目');
		$this->assign("pub_items", $pub_items);
		$this->assign("own_items", $own_items);
		$this->assign("view_items", $view_items);
		$this->assign("login_user", $login_user);
		$this->display();
	}
	
	//新建/修改项目
	public function edit() {
		$item_id = I("item_id");
		$login_user = $this->checkLogin();
		
		$item = D("Item")->where("item_id = '$item_id' ")->find();
		if (!IS_POST) {
			$this->assign("item", $item);
			if ($item) {
				$this->assign("webtitle", '修改项目 - ' . $item['item_name']);
			} else {
				$this->assign("webtitle", '新建项目');
			}
			$this->assign("login_user", $login_user);
			$this->display();
		} else {
			$item_name = I("item_name");
			$item_type = I("item_type");
			$password = I("password");
			$item_description = I("item_description");
			if ($item_id > 0) {
				$data = array(
					"item_name" => $item_name,
					"item_type" => $item_type,
					"password" => $password,
					"item_description" => $item_description
				);
				$ret = D("Item")->where("item_id = '$item_id' ")->save($data);
				if ($ret !== false) {
					//如果修改了密码则清空密码查看表记录(含清空密码)
					if ($password != $item['password']) {
						D("ItemPwder")->where(" item_id = '$item_id' ")->delete();
					}
					$this->message("操作成功！", U('Home/Item/show', array('item_id' => $item_id)));
				} else {
					$this->message("操作失败！", U('Home/Item/index'));
				}
			} else {
				$insert = array(
					"uid" => $login_user['uid'],
					"username" => $login_user['username'],
					"item_name" => $item_name,
					"item_type" => $item_type,
					"password" => $password,
					"item_description" => $item_description,
					"addtime" => time()
				);
				$item_id = D("Item")->add($insert);
				if ($item_id) {
					$this->message("操作成功！", U('Home/Item/show', array('item_id' => $item_id)));
				} else {
					$this->message("操作失败！", U('Home/Item/index'));
				}
			}
		}
	}
	
	//搜索
	public function search() {
		$item_id = I("item_id");
		$keyword = I("keyword");
		if (!$item_id || !$keyword) {
			$this->message("关键词不能为空！", U('Home/Item/show', array('item_id' => $item_id)));
			return;
		}
		
		$login_user = $this->checkLogin();
		$this->checkItemVisit($login_user['uid'], $item_id);
		
		$item = D("Item")->where("item_id = '$item_id' and status = 1 ")->find();
		if (!$item) {
			header("location:" . U("Home/Item/index") );
			return;
		}
		
		$first_page_id = 0;
		$where_keyword = " and ( page_title like '%{$keyword}%' or page_content like '%{$keyword}%' ) ";
		$pages = D("Page")->where(" cat_id = '0' and item_id = '$item_id' and status = 1 $where_keyword ")->order(" `order` asc ")->select();
		if (isset($pages[0])) {
			$first_page_id = $pages[0]['page_id'];
		}
		//获取所有目录
		$searched_catalogs = array();
		$catalogs = D("Catalog")->where(" item_id = '$item_id' and status = 1 ")->order(" `order` asc ")->select();
		if ($catalogs) {
			foreach ($catalogs as $key => &$catalog) {
				$catalog['pages'] = array();
				$temp = D("Page")->where("cat_id = '$catalog[cat_id]' and status = 1 $where_keyword ")->order(" `order` asc  ")->select();
				if ($temp) {
					$catalog['pages'] = $temp;
					if ($key == 0 && isset($temp[0]) && !$first_page_id) {
						$first_page_id = $temp[0]['page_id'];
					}
					$searched_catalogs[] = $catalog;
				}
			}
		}
		
		$ItemPermn = $this->checkItemPermn($login_user['uid'], $item_id);
		$ItemCreator = $this->checkItemCreator($login_user['uid'], $item_id);
		
		$this->assign("webtitle", '搜索 ' . $keyword . ' 结果');
		$this->assign("keyword", $keyword);
		$this->assign("ItemPermn", $ItemPermn);
		$this->assign("ItemCreator", $ItemCreator);
		$this->assign("catalogs", $catalogs);
		$this->assign("searched_catalogs", $searched_catalogs);
		$this->assign("pages", $pages);
		$this->assign("item", $item);
		$this->assign("login_user", $login_user);
		$this->assign("first_page_id", $first_page_id);
		$this->display();
	}
	
	//展示单个项目
	public function show() {
		$item_id = I("item_id");
		
		$login_user = $this->checkLogin();
		$this->checkItemVisit($login_user['uid'], $item_id);
		
		$item = D("Item")->where("item_id = '$item_id' and status = 1 ")->find();
		if (!$item) {
			header("location:" . U("Home/Item/index") );
			return;
		}
		
		$first_page_id = 0;
		//获取所有父目录id为0的页面
		$pages = D("Page")->where("cat_id = '0' and item_id = '$item_id' and status = 1 ")->order(" `order` asc  ")->select();
		if (isset($pages[0])) {
			$first_page_id = $pages[0]['page_id'];
		}
		//获取所有目录
		$catalogs = D("Catalog")->where("item_id = '$item_id' and status = 1 ")->order(" `order` asc  ")->select();
		if ($catalogs) {
			foreach ($catalogs as $key => &$catalog) {
				$temp = D("Page")->where("cat_id = '$catalog[cat_id]' and status = 1 ")->order(" `order` asc  ")->select();
				if (!$first_page_id && $key == 0 && isset($temp[0])) {
					$first_page_id = $temp[0]['page_id'];
				}
				$catalog['pages'] = $temp ? $temp : array();
			}
		}
		
		if ($first_page_id > 0) {
			header("location:" . U("Home/Page/show", array("page_id" => $first_page_id)) );
			return;
		} else {
			$item_url = get_domain() . __APP__ . '?m=Home&c=Item&a=show&item_id=' . $item_id;
			
			$ItemPermn = $this->checkItemPermn($login_user['uid'], $item_id);
			$ItemCreator = $this->checkItemCreator($login_user['uid'], $item_id);
			
			$this->assign("webtitle", $item['item_name']);
			$this->assign("ItemPermn", $ItemPermn);
			$this->assign("ItemCreator", $ItemCreator);
			$this->assign("share_url", $item_url);
			$this->assign("catalogs", $catalogs);
			$this->assign("pages", $pages);
			$this->assign("item", $item);
			$this->assign("login_user", $login_user);
			$this->display();
		}
	}
	
	//输入访问密码
	public function pwd() {
		$item_id = I("item_id");
		$login_user = $this->checkLogin();
		
		if (!IS_POST) {
			$this->assign("item_id", $item_id);
			$this->assign("login_user", $login_user);
			$this->display();
		} else {
			$password = I("password");
			$v_code = I("v_code");
			//不区分大小写
			if ($v_code && $v_code == strtolower(session('v_code'))) {
				$item = D("Item")->where("item_id = '$item_id' and status = 1 ")->find();
				if ($item['password'] == $password) {
					
					//加入密码访问记录
					$data = array();
					$data['uid'] = $login_user['uid'];
					$data['item_id'] = $item_id;
					$data['addtime'] = time();
					$return = D("ItemPwder")->add($data, array(), true);
					if (!$return) {
						$this->message("访问密码不正确");
						exit();
					}
					
					//session("visit_item_" . $item_id, 1);
					header("location:" . U("Home/Item/show", array('item_id' => $item_id)) );
					exit();
				} else {
					$this->message("访问密码不正确");
					exit();
				}
			} else {
				$this->message("验证码不正确");
				exit();
			}
		}
	}
	
	//删除项目
	public function delete() {
		$item_id = I("item_id");
		$item = D("Item")->where("item_id = '$item_id' and status = 1 ")->find();
		if (!$item) {
			$this->message("错误的项目", U("Home/Item/index"));
			return;
		}
		
		$login_user = $this->checkLogin();
		if (!$this->checkItemCreator($login_user['uid'], $item_id)) {
			$this->message("你无权限", U("Home/Item/index"));
			return;
		}
		
		if (!IS_POST) {
			$this->assign("webtitle", '删除项目');
			$this->assign("login_user", $login_user);
			$this->assign("item_id", $item_id);
			$this->display();
		} else {
			$password = I("password");
			if (!D("User")->checkLogin($item['username'], $password)) {
				$this->message("密码错误！");
				exit();
			}
			
			$data = array(
				'status' => 0
			);
			D("Page")->where("item_id = '$item_id' ")->save($data);
			D("Catalog")->where("item_id = '$item_id' ")->save($data);
			D("PageHistory")->where("item_id = '$item_id' ")->save($data);
			$return = D("Item")->where("item_id = '$item_id' ")->save($data);
			if ($return !== false) {
				header("location:" . U("Home/Item/index") );
				exit();
			}
			
			$this->message("request fail", U("Home/Item/delete", array('item_id' => $item_id)));
			exit();
		}
	}
	
	//导出word
	public function word() {
		import("Vendor.Parsedown.Parsedown");
		$Parsedown = new \Parsedown();
		$item_id = I("item_id");
		$login_user = $this->checkLogin();
		if (!$this->checkItemPermn($login_user['uid'], $item_id)) {
			$this->message("你无权限");
			return;
		}
		
		$item = D("Item")->where("item_id = '$item_id' ")->find();
		//获取所有父目录id为0的页面
		$pages = D("Page")->where("cat_id = '0' and item_id = '$item_id' ")->order(" `order` asc  ")->select();
		//获取所有目录
		$catalogs = D("Catalog")->where("item_id = '$item_id' ")->order(" `order` asc  ")->select();
		if ($catalogs) {
			foreach ($catalogs as $key => &$catalog) {
				$temp = D("Page")->where("cat_id = '$catalog[cat_id]' ")->order(" `order` asc  ")->select();
				$catalog['pages'] = $temp ? $temp : array();
			}
		}
		
		$data = '';
		$parent = 1;
		
		if ($pages) {
			foreach ($pages as $key => $value) {
				$data .= "<h1>{$parent}、{$value['page_title']}</h1>";
				$data .= '<div style="margin-left:20px;">';
				$data .= htmlspecialchars_decode($Parsedown->text($value['page_content']));
				$data .= '</div>';
				$parent++;
			}
		}
		//var_export($catalogs);
		if ($catalogs) {
			foreach ($catalogs as $key => $value) {
				$data .= "<h1>{$parent}、{$value['cat_name']}</h1>";
				$data .= '<div style="margin-left:20px;">';
				$child = 1;
				if ($value['pages']) {
					foreach ($value['pages'] as $page) {
						$data .= "<h2>{$parent}.{$child}、{$page['page_title']}</h2>";
						$data .= '<div style="margin-left:20px;">';
						$data .= htmlspecialchars_decode($Parsedown->text($page['page_content']));
						$data .= '</div>';
						$child++;
					}
				}
				$data .= '</div>';
				$parent++;
			}
		}
		if (!$data) {
			$this->message("无数据！", U("Home/Item/show", array('item_id' => $item_id)));
			return;
		}
		output_word($data, $item['item_name']);
	}
}