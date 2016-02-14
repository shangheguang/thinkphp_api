$(function(){
	
	//自动根据url把当前菜单激活
	var page_id = readCookie('page_id');
	if(!page_id || (page_id && $(".doc-left li a[data-page_id='" + page_id + "']").length == 0)){
		page_id = $(".doc-left li a[data-page_id]:first").attr("data-page_id");
	}
	if(page_id !=null && page_id.toString().length>0){
		var str = 'page_id='+page_id;
		$(".doc-left li").each(function(){
			url = $(this).children("a").attr("href");
			//如果链接中包含当前url的信息，两者相匹配
			if (url && url.indexOf(str) >= 0 ) {
				//激活菜单
				$(this).addClass("active");
				//如果该菜单是子菜单，则还需要把父菜单打开才行
				if ($(this).parent('.child-ul')) {
						$(this).parent('.child-ul').show();
						$(this).parent('.child-ul').parent('li').children("a").children('i').attr("class","icon-chevron-down");
				};
				//获取对应的page_id
				page_id = $(this).children("a").attr("data-page_id");
				if (page_id != '' && page_id !='#') {
					change_page(page_id)
				};
			};
		})
	}
	
	function isMobile(){
		return navigator.userAgent.match(/iPhone|iPad|iPod|Android|android|BlackBerry|IEMobile/i) ? true : false; 
	}
	
	//根据屏幕宽度进行响应(应对移动设备的访问)
	if( isMobile()){
		AdaptToMobile();
	}
	
	$(window).resize(function(){
		if( isMobile()){
			AdaptToMobile();
		}else if($(window).width() < 600){
			AdaptToMobile();
		}else{
			window.location.reload();
		}
	});
	
	//js获取url参数
	function GetQueryString(name){
		var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
		var r = window.location.search.substr(1).match(reg);
		if(r!=null)return	unescape(r[2]); return null;
	}
	
	function AdaptToMobile(){
		$(".doc-left").removeClass("span3");
		$(".doc-left").css("width",'100%');
		$(".doc-left").css("height",'initial');
		$(".doc-right").removeClass("span12");
		$(".doc-head .right").hide();
		$(".page-edit-link").html('');
		$(".doc-left-newbar").html('');
		$(".iframe_content").css("padding-left","30px");
		$(".doc-left .nav-list li a i ").css("margin-left" , '10px');
		$(".search-input-append").css("width","100%");
		$(".search-query-input").css("width","70%");
	}
	
	function mScroll(id){
		$("html,body").stop(true);
		$("html,body").animate(
		{scrollTop: $("#"+id).offset().top},
			2000);
	}
	
	function writeCookie(name, value, hours) {
		var expire = "";
		if (hours != null) {
			expire = new Date((new Date()).getTime() + hours * 3600 * 1000);
			expire = "; expires=" + expire.toGMTString();
		}
		document.cookie = DocConfig.domain + '_' + name + "=" + escape(value) + expire + ";path=/";
	}
	
	function readCookie(name) {
		var cookieValue = "";
		var search = DocConfig.domain + '_' + name + "=";
		if (document.cookie.length > 0) {
			offset = document.cookie.indexOf(search);
			if (offset != -1) {
				offset += search.length;
				end = document.cookie.indexOf(";", offset);
				if (end == -1)
					end = document.cookie.length;
				cookieValue = unescape(document.cookie.substring(offset, end))
			}
		}
		return cookieValue;
	}
	
	//点击左侧菜单事件
	$(".doc-left li").click(function(){
		//先把所有菜单的激活状态取消
		$(".doc-left li").each(function(){
			$(this).removeClass("active");
		});
		$(this).addClass("active");
		//先判断是否存在子菜单
		if ($(this).children('.child-ul').length != 0) {
			//如果子菜单是隐藏的，则显示之；如果是显示状态的，则隐藏
			if ($(this).children('.child-ul').css("display") == "none") {
				$(this).children('.child-ul').show();
				$(this).children("a").children('i').attr("class","icon-chevron-down");
			}else{
				$(this).children('.child-ul').hide();
				$(this).children("a").children('i').attr("class","icon-chevron-right");
			}
		};
		//获取对应的page_id
		var page_id = $(this).children("a").attr("data-page_id");
		if (page_id != '' && page_id != null && page_id !='#') {
			writeCookie('page_id', page_id, 24);
			change_page(page_id);
			//如果是移动设备的话，则滚动页面
			if( isMobile()){
				mScroll("page-content");
			}
		};
		return false;
	});
	
	//切换页面；
	function change_page(page_id){
		if(!page_id)return;
		var item_id = $("#item_id").val();
		var base_url = $("#base_url").val();
		$(".page-edit-link").show();
		$("#page-content").attr("src", base_url+"?m=Home&c=Page&a=show&page_id="+page_id+"&in_item=1");
		$("#edit-link").attr("href", base_url+"?m=Home&c=Page&a=edit&page_id="+page_id);
		$("#delete-link").click(function(){
			if(confirm('该操作不可恢复，确认删除吗？')){
				$("#delete-link").attr("href", base_url+"?m=Home&c=Page&a=delete&page_id="+page_id);
				return true;
			}
			return false;
		});
	}
	
	//分享
	$("#share").click(function(){
		$("#share-modal").modal();
		return false;
	});
	
	var ifr = document.getElementById('page-content')
	ifr.onload = function() {
		var iDoc = ifr.contentDocument || ifr.document;
		var height = calcPageHeight(iDoc);
		ifr.style.height = height + 'px';
	}
	
	// 计算页面的实际高度，iframe自适应会用到
	function calcPageHeight(doc) {
		var cHeight = Math.max(doc.body.clientHeight, doc.documentElement.clientHeight);
		var sHeight = Math.max(doc.body.scrollHeight, doc.documentElement.scrollHeight);
		var height = Math.max(cHeight, sHeight);
		return height;
	}
	
})

