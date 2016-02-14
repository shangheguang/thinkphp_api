$(function(){
	var item_id = $("#item_id").val();
	
	$('#edit-cat').modal({
		"backdrop":'static'
	});
	
	//保存
	$("#save-cat").click(function(){
		if(confirm('该操作不可恢复，确认删除吗？')){
			var password = $("#password").val();
			$.post(
				"../index.php?m=Home&c=Item&a=ajaxDelete",
				{"item_id": item_id , "password": password	},
				function(data){
					if (data.error_code == 0) {
						alert("删除成功！");
						window.location.href="../index.php?m=Home&c=Item&a=index";
					}else{
						alert("删除失败：" + data.error_message);
					}
				},
				"json"
			);
		}
		return false;
	});
	
	$(".exist-cat").click(function(){
		window.location.href="../index.php?m=Home&c=Item&a=show&item_id="+item_id;
		return false;
	});
	
});
