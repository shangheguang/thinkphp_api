$(function(){
	
	//自动根据url把当前菜单激活
	var page_id = readCookie('page_id');
	if(!page_id || (page_id && $(".doc-left li a[data-page_id='" + page_id + "']").length == 0)){
		page_id = $(".doc-left li a[data-page_id]:first").attr("data-page_id");
	}
	if(page_id !=null && page_id.toString().length>0){
		var str = 'page_id='+page_id;
		$(".doc-left li").each(function(){
			//获取对应的page_id
			var click_page_id = $(this).children("a").attr("data-page_id");
			if (click_page_id != '' && page_id == click_page_id) {
				//激活菜单
				$(this).addClass("active");
				//如果该菜单是子菜单，则还需要把父菜单打开才行
				if ($(this).parent('.child-ul')) {
					$(this).parent('.child-ul').show();
					$(this).parent('.child-ul').parent('li').children("a").children('i').attr("class","icon-chevron-down");
				}
				change_page(page_id);
			}
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
				mScroll("page_content");
			}
		};
		return false;
	});
	
	//切换页面；
	function change_page(page_id){
		if(!page_id) return;
		var item_id = $("#item_id").val();
		var base_url = $("#base_url").val();
		$("#page_content").attr("src", base_url+"?m=Home&c=Page&a=show&page_id="+page_id+"&in_search=1");
	}
	
	var ifr = document.getElementById('page_content')
	ifr.onload = function() {
		var iDoc = ifr.contentDocument || ifr.document;
		var height = calcPageHeight(iDoc);
		console.log(height);
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

