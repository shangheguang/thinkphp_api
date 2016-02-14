$(function(){
	var item_id = $("#item_id").val();
	
	$('#edit-cat').modal({
		"backdrop":'static'
	});
	
	getList();
	function getList(){
		$.get(
			"../index.php?m=Home&c=Member&a=getList",
			{ "item_id": item_id },
			function(data){
				$("#show-cat").html('');
				if (data.error_code == 0) {
					json = data.data;
					for (var i = 0; i < json.length; i++) {
						cat_html ='<a class="badge badge-important single-cat" data-uid="'+json[i].uid+'" >'+json[i].username+'&nbsp;x</a>';
						$("#show-cat").append(cat_html);
					};
				};
			},
			"json"
		);
	}
	
	//保存
	$("#save-cat").click(function(){
		var user_id = $("#user_id").val(),
			username = $("#username").val();
		if(user_id == '' && username == ''){
			alert('请至少填写一项');
			return false;
		}
		$.post(
			"../index.php?m=Home&c=Member&a=save",
			{"user_id": user_id, "username": username, "item_id": item_id},
			function(data){
				if (data.error_code == 0) {
					$("#user_id").val('');
					$("#username").val('');
					alert("保存成功！");
				}else{
					alert("保存失败：" + data.error_message);
				}
				getList();
			},
			"json"
		);
		return false;
	});
	
	//删除
	$('#show-cat').delegate('.single-cat','click', function(){
		if(confirm('该操作不可恢复，确认删除吗？')){
			var user_id = $(this).attr("data-uid");
			if (user_id) {
				$.post(
					"../index.php?m=Home&c=Member&a=delete",
					{ "user_id": user_id, "item_id" :item_id },
					function(data){
						if (data.error_code == 0) {
							alert("删除成功！");
							getList();
						}else{
							alert("删除失败：" + data.error_message);
						}
					},
					"json"
				);
			}
		}
		return false;
	});
	
	$(".exist-cat").click(function(){
		window.location.href="../index.php?m=Home&c=Item&a=show&item_id="+item_id;
		return false;
	});
	
});

