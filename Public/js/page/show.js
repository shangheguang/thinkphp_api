$(function(){
	
	//自动根据url把当前菜单激活
	var page_id = GetQueryString('page_id');
	if(page_id !=null && page_id.toString().length>0){
		//var str = 'page_id='+page_id;
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
			}
		})
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
		}else{
			$(this).addClass("active");
		}
		
		//先把所有菜单的激活状态取消
		$(".doc-left li").each(function(){
			if ($(this).children('.child-ul').length == 0) {
				$(this).removeClass("active");
			}
		});
		
		//获取对应的page_id
		var page_id = $(this).children("a").attr("data-page_id");
		if (page_id != '' && page_id != null && page_id !='#') {
			writeCookie('page_id', page_id, 24);
			//如果是移动设备的话，则滚动页面
			if( isMobile()){
				mScroll("page-content");
			}
		};
		//return false;
	});
	
	//分享
	$("#share").click(function(){
		$("#share-modal").modal();
		return false;
	});
	
})

