$(function(){
	var item_id = $("#item_id").val();
	
	$('#edit-cat').modal({
		"backdrop":'static'
	});
	
	getCatList();
	function getCatList(){
		$.get(
			"../index.php?m=Home&c=Catalog&a=catList",
			{ "item_id": item_id },
			function(data){
				$("#show-cat").html('');
				if (data.error_code == 0) {
					json = data.data;
					for (var i = 0; i < json.length; i++) {
						cat_html ='<a class="badge badge-info single-cat " href="../index.php?m=Home&c=Catalog&a=edit&cat_id='+json[i].cat_id+'&item_id='+json[i].item_id+'">'+json[i].cat_name+'&nbsp;<i class="icon-edit"></i></a>';
						$("#show-cat").append(cat_html);
					};
				};
				
			},
			"json"
		);
	}
	//保存目录
	$("#save-cat").click(function(){
		var cat_name = $("#cat_name").val();
		var order = $("#order").val();
		var cat_id = $("#cat_id").val();
		$.post(
			"../index.php?m=Home&c=Catalog&a=save",
			{"cat_name": cat_name , "order": order , "item_id": item_id , "cat_id": cat_id	},
			function(data){
				if (data.error_code == 0) {
					$("#delete-cat").hide();
					$("#cat_name").val('');
					$("#order").val('');
					$("#cat_id").val('');
					alert("保存成功！");
				}else{
					alert("保存失败：" + data.error_message);
				}
				getCatList();
			},
			"json"
		);
		return false;
	});
	//删除目录
	$("#delete-cat").click(function(){
		var cat_id = $("#cat_id").val();
		if(confirm('该操作不可恢复，确认删除吗？')){
			if (cat_id > 0 ) {
				$.post(
					"../index.php?m=Home&c=Catalog&a=delete",
					{ "cat_id": cat_id },
					function(data){
						if (data.error_code == 0) {
							alert("删除成功！");
							window.location.href="../index.php?m=Home&c=Catalog&a=edit&item_id="+item_id;
						}else{
							alert("删除失败：" + data.error_message);
							window.location.href="../index.php?m=Home&c=Catalog&a=edit&item_id="+item_id+"&cat_id="+cat_id;
						}
					},
					"json"
				);
			}else{
				alert("请先选择文件夹");
			}
		}
		return false;
	})
	
	$(".exist-cat").click(function(){
		window.location.href="../index.php?m=Home&c=Item&a=show&item_id="+item_id;
		return false;
	});
});
