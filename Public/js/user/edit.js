$(function(){
	
	$('#edit-user').modal({
		"backdrop":'static'
	});
	
	getUserList();
	function getUserList(){
		$.get(
			"../index.php?m=Home&c=User&a=userList",
			function(data){
				$("#show-user").html('');
				if (data.error_code == 0) {
					json = data.data;
					for (var i = 0; i < json.length; i++) {
						html ='<a class="badge badge-info single-user " href="../index.php?m=Home&c=User&a=edit&user_id='+json[i].uid+'">'+json[i].username+'&nbsp;<i class="icon-edit"></i></a>';
						$("#show-user").append(html);
					};
				};
				
			},
			"json"
		);
	}
	//保存用户
	$("#save-user").click(function(){
		var user_id = $("#user_id").val();
		var username = $("#username").val();
		var password = $("#password").val();
		var confirm_password = $("#confirm_password").val();
		if(username == ''){
			alert('用户名不能为空');
			return false;
		}
		if(password != '' && password != confirm_password){
			alert('两次密码输入不一致');
			return false;
		}
		$.post(
			"../index.php?m=Home&c=User&a=savedit",
			{"user_id": user_id, "username": username, "password": password, "confirm_password": confirm_password},
			function(data){
				if (data.error_code == 0) {
					$("#user_id").val('');
					$("#username").val('');
					$("#password").val('');
					$("#confirm_password").val('');
					alert("保存成功！");
					
				}else{
					alert("保存失败：" + data.error_message);
				}
				//getUserList();
				window.location.href="../index.php?m=Home&c=User&a=edit";
			},
			"json"
		);
		return false;
	});
	//删除用户
	$("#delete-user").click(function(){
		var user_id = $("#user_id").val();
		if(confirm('该操作不可恢复，确认删除吗？')){
			if (user_id > 0 ) {
				$.post(
					"../index.php?m=Home&c=Catalog&a=delete",
					{ "user_id": user_id },
					function(data){
						if (data.error_code == 0) {
							alert("删除成功！");
							window.location.href="../index.php?m=Home&c=User&a=edit";
						}else{
							alert("删除失败！");
							window.location.href="../index.php?m=Home&c=User&a=edit&user_id="+user_id;
						}
					},
					"json"
				);
			}else{
				alert("请先选择用户");
			}
		}
		return false;
	})
	//返回
	$(".exist-user").click(function(){
		window.location.href="../index.php?m=Home&c=Item&a=index";
		return false;
	});
	//新建用户
	$(".new-user").click(function(){
		window.location.href="../index.php?m=Home&c=User&a=edit";
		return false;
	});
	
});
