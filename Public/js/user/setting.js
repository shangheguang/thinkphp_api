$(function(){
	
	$('#setting-user').modal({
		"backdrop":'static'
	});
	
	//返回
	$(".exist-user").click(function(){
		window.location.href="../index.php?m=Home&c=Item&a=index";
		return false;
	});
	
});
