<?php
namespace Home\Controller;
use Think\Controller;
class PageController extends BaseController {
	
	//展示某个项目的单个页面
	public function show() {
		$page_id = I("page_id");
		$page = D("Page")->where(" page_id = '$page_id' ")->find();
		if (!$page) {
			header("location:".U("Home/Item/index") );
			return;
		}
		
		$item_id = $page["item_id"];
		$login_user = $this->checkLogin();
		
		$this->checkItemVisit($login_user['uid'], $item_id);
		
		$item = D("Item")->where("item_id = '$item_id' and status = 1 ")->find();
		if (!$item) {
			header("location:" . U("Home/Item/index") );
			return;
		}
		
		//获取所有父目录id为0的页面
		$pages = D("Page")->where("cat_id = '0' and item_id = '$item_id' and status = 1 ")->order(" `order` asc  ")->select();
		//获取所有目录
		$catalogs = D("Catalog")->where("item_id = '$item_id' and status = 1 ")->order(" `order` asc  ")->select();
		if ($catalogs) {
			foreach ($catalogs as $key => &$catalog) {
				$temp = D("Page")->where("cat_id = '$catalog[cat_id]' and status = 1 ")->order(" `order` asc  ")->select();
				$catalog['pages'] = $temp ? $temp : array();
			}
		}
		
		$item_url = get_domain() . __APP__ . '?m=Home&c=Item&a=show&item_id=' . $item_id;
		$page_url = get_domain() . __APP__ . '?m=Home&c=Page&a=show&page_id=' . $page_id;
		
		$ItemPermn = $this->checkItemPermn($login_user['uid'], $item_id);
		$ItemCreator = $this->checkItemCreator($login_user['uid'], $item_id);
		
		$this->assign("webtitle", $item['item_name'] . ': ' . $page['page_title']);
		$this->assign("ItemPermn", $ItemPermn);
		$this->assign("ItemCreator", $ItemCreator);
		$this->assign("share_url", $item_url);
		$this->assign("page_url", $page_url);
		$this->assign("catalogs", $catalogs);
		$this->assign("pages", $pages);
		$this->assign("item", $item);
		$this->assign("login_user", $login_user);
		
		import("Vendor.Parsedown.Parsedown");
		$Parsedown = new \Parsedown();
		//重新转义回来。因为Parsedown会把代码块里的代码也解析称html实体
		//$page['page_content'] = htmlspecialchars_decode($Parsedown->text($page['page_content']));
		$page['page_content'] = $Parsedown->text(htmlspecialchars_decode($page['page_content']));
		//$page['page_content'] = $this->fixMobileHtmlContent($page['page_content'], 750, 0);
		$this->assign("singlepage", $page);
		
		//如果在搜索结果中展示则更换纯净模板
		$in_search = I("in_search");
		if ($in_search) {
			$this->display('show_in_search');
		} else {
			$this->display();
		}
	}
	
	//返回单个页面的源markdown代码
	public function md() {
		$page_id = I("page_id");
		$page = D("Page")->where(" page_id = '$page_id' ")->find();
		echo $page['page_content'];
	}
	
	//编辑页面
	public function edit() {
		$page_id = I("page_id");
		$item_id = I("item_id");
		$page_history_id = I("page_history_id");
		
		if ($page_id > 0) {
			if ($page_history_id) {
				$page = D("PageHistory")->where(" page_history_id = '$page_history_id' ")->find();
			} else {
				$page = D("Page")->where(" page_id = '$page_id' ")->find();
			}
			$item_id = $page['item_id'] ? $page['item_id'] : 0;
			$this->assign("page", $page);
		}
		
		if (!$item_id) {
			$this->message("项目非法", U("Home/Item/index"));
			return;
		}
		
		$login_user = $this->checkLogin();
		if (!$this->checkItemPermn($login_user['uid'], $item_id)) {
			$this->message("你无权限");
			return;
		}
		
		$this->assign("item_id", $item_id);
		
		$this->display();
	}
	
	//保存
	public function save() {
		$login_user = $this->checkLogin();
		$page_id = I("page_id") ? I("page_id") : 0;
		$page_title = I("page_title") ? I("page_title") : '默认页面';
		$page_content = I("page_content");
		$cat_id = I("cat_id") ? I("cat_id") : 0;
		$item_id = I("item_id") ? I("item_id") : 0;
		$order = I("order") ? I("order") : 99;
		
		$login_user = $this->checkLogin();
		if (!$this->checkItemPermn($login_user['uid'], $item_id)) {
			$this->message("你无权限");
			return;
		}
		
		$data = array();
		$data['page_title'] = $page_title;
		$data['page_content'] = $page_content;
		$data['order'] = $order;
		$data['item_id'] = $item_id;
		$data['cat_id'] = $cat_id;
		$data['addtime'] = time();
		$data['author_uid'] = $login_user['uid'];
		$data['author_username'] = $login_user['username'];
		
		if ($page_id > 0) {
			//在保存前先把当前页面的版本存档
			$page = D("Page")->where(" page_id = '$page_id' ")->find();
			$insert_history = array(
				'page_id' => $page['page_id'],
				'item_id' => $page['item_id'],
				'cat_id' => $page['cat_id'],
				'page_title' => $page['page_title'],
				'page_content' => $page['page_content'],
				'order' => $page['order'],
				'addtime' => $page['addtime'],
				'author_uid' => $page['author_uid'],
				'author_username' => $page['author_username']
			);
			D("PageHistory")->add($insert_history);
			
			$ret = D("Page")->where(" page_id = '$page_id' ")->save($data);
			$return = D("Page")->where(" page_id = '$page_id' ")->find();
			
		} else {
			$page_id = D("Page")->add($data);
			$return = D("Page")->where(" page_id = '$page_id' ")->find();
		}
		if (!$return) {
			$return['error_code'] = 10103;
			$return['error_message'] = 'request fail';
		}
		$this->sendResult($return);
	}
	
	//删除页面
	public function delete() {
		$page_id = I("page_id") ? I("page_id") : 0;
		$page = D("Page")->where(" page_id = '$page_id' ")->find();
		
		$login_user = $this->checkLogin();
		if (!$this->checkItemPermn($login_user['uid'], $page['item_id'])) {
			$this->message("你无权限");
			return;
		}
		
		if ($page) {
			//$ret = D("Page")->where(" page_id = '$page_id' ")->limit(1)->delete();
			$data = array(
				'status' => 0
			);
			$ret = D("Page")->where(" page_id = '$page_id' ")->save($data);
		}
		if ($ret) {
			$this->message("删除成功！", U("Home/Item/show", array('item_id' => $page['item_id'])) );
		} else {
			$this->message("删除失败！", U("Home/Item/show", array('item_id' => $page['item_id'])) );
		}
	}
	
	//历史版本
	public function history() {
		$page_id = I("page_id") ? I("page_id") : 0;
		$this->assign("page_id", $page_id);
		
		$PageHistory = D("PageHistory")->where("page_id = '$page_id' ")->order(" addtime desc")->limit(10)->select();
		
		if ($PageHistory) {
			foreach ($PageHistory as $key => &$value) {
				$value['addtime'] = date("Y-m-d H:i:s", $value['addtime']);
			}
		}
		$this->assign("PageHistory", $PageHistory);
		$this->display();
	}
	
	//图片上传
	Public function upload(){
		//$file[name] = 'editormd-image-file';
		if(IS_POST) {
			//上传类配置信息
			$config = array(
				'maxSize' => 3145728,
				'exts' => array('jpg', 'jpeg', 'png', 'gif'),
				'rootPath' => './Uploads/',// 设置附件上传根目录
				'savePath' => '',// 设置附件上传（子）目录
				'autoSub' => true,
				'subName' => array('date', 'Ym'),
				//'saveName' => array('uniqid', ''),
				'saveName' => date('YmdHis') . rand(100, 999)
			);
			//初始化上传类
			$upload = new \Think\Upload($config);
			
			// 上传文件
			$info = $upload->upload();
			if(!$info) {// 上传错误提示错误信息
				$this->error($upload->getError());
			}else{// 上传成功
				//$this->success('上传成功！');
				/*
				 * key 	附件上传的表单名称
					 savepath 	上传文件的保存路径
					 name 	上传文件的原始名称
					 savename 	上传文件的保存名称
					 size 	上传文件的大小
					 type 	上传文件的MIME类型
					 ext 	上传文件的后缀类型
				 */
				
				/*
				 {
				 success : 0 | 1,           // 0 表示上传失败，1 表示上传成功
				 message : "提示的信息，上传成功或上传失败及错误信息等。",
				 url     : "图片地址"        // 上传成功时才返回
				 }
				 */
				
				if (isset($info['editormd-image-file']) && $info['editormd-image-file']) {
					$upfile = $info['editormd-image-file'];
					
					// 保存表单数据 包括附件数据
					$return['success'] = 1;
					$return['message'] = "上传成功";
					$return['url'] = '/Uploads/' . $upfile['savepath'] . $upfile['savename'];
					echo json_encode($return);
					exit();
				}
			}
			
		}
		
		//其它情况返回失败
		$return['success'] = 0;
		$return['message'] = "上传失败";
		$return['url'] = '';
		echo json_encode($return);
		exit();
	}
	
	//修正获取移动适配版html内容
	public function fixMobileHtmlContent($content, $to_width = "100%", $to_height = null) {
		if (empty($content)) {
			return $content;
		}
		preg_match_all('/<img[\s\t\r\n]+[^>]*src\s*=\s*[\'"\s\t\r\n]+([^>\'"]+?)[\'"\s\t\r\n]+[^>]*[\s\t\r\n]*\/>/i', $content, $out_img, PREG_PATTERN_ORDER);
		if (isset($out_img[0]) && is_array($out_img[0]) && count($out_img[0]) > 0) {
			foreach ($out_img[0] as $key => $img) {
				$new_img = isset($out_img[1][$key]) && !empty($out_img[1][$key]) ? '<img class="wiki_ImgHtmlObject" src="' . $out_img[1][$key] . '" width="' . $to_width . '" />' : '';
				$content = str_replace($img, $new_img, $content);
			}
		}
		preg_match_all('/<object[\s\t\r\n]+[^>]*data[\s\t\r\n]*=[\'"\s\t\r\n]+([^>\'"]+?)[\'"\s\t\r\n]+[^>]*[\s\t\r\n]*>[\s\t\r\n\.]*<\/object>/i', $content, $out_video, PREG_PATTERN_ORDER);
		if (isset($out_video[0]) && is_array($out_video[0]) && count($out_video[0]) > 0) {
			foreach ($out_video[0] as $key => $video) {
				$new_video = isset($out_video[1][$key]) && !empty($out_video[1][$key]) ? '<object class="wiki_VideoHtmlObject" name="videoObject" data="' . $out_video[1][$key] . '" width="' . $to_width . '"' . ($to_height ? ' height="' . $to_height . '"' : '') . '" type="text/html" wmode="transparent"></object>' : '';
				$content = str_replace($video, $new_video, $content);
			}
		}
		return $content;
	}
}