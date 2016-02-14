//写入cookies
function writeCookie(name, value, hours){
	var expire = "";
	if (hours != null) {
		expire = new Date((new Date()).getTime() + hours * 3600 * 1000);
		expire = "; expires=" + expire.toGMTString();
	}
	document.cookie = DocConfig.domain + '_' + name + "=" + escape(value) + expire + ";path=/";
}

//读取cookies
function readCookie(name){
	var cookieValue = "";
	var search = DocConfig.domain + '_' + name + "=";
	if(document.cookie.length > 0){
		offset = document.cookie.indexOf(search);
		if(offset != -1){
			offset += search.length;
			end = document.cookie.indexOf(";", offset);
			if(end == -1){
				end = document.cookie.length;
			}
			cookieValue = unescape(document.cookie.substring(offset, end))
		}
	}
	return cookieValue;
}

//删除cookies
function delCookie(name){
	var expire = new Date();
	expire.setTime(expire.getTime() - 1);
	var cookieValue = readCookie(name);
	if(cookieValue != null){
		document.cookie = DocConfig.domain + '_' + name + "=" + cookieValue + ";expires=" + expire.toGMTString();
	}
}

//js获取url参数
function GetQueryString(name){
	var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
	var r = window.location.search.substr(1).match(reg);
	if(r!=null)return	unescape(r[2]); return null;
}

//是否是移动端
function isMobile(){
	return navigator.userAgent.match(/iPhone|iPad|iPod|Android|android|BlackBerry|IEMobile/i) ? true : false; 
}

//滑动至id
function mScroll(id){
	$("html,body").stop(true);
	$("html,body").animate({scrollTop: $("#"+id).offset().top}, 2000);
}