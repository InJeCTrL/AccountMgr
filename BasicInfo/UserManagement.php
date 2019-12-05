<?php
	session_start();
	// 禁止重复找回密码
	unset($_SESSION['enResetPwd']);
	include "../conn/DBMgr.php";
	$conn = Connect();
	// 根据用户ID获取用户信息
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		$UserInfo = GetUser($conn, $_SESSION['UserID']);
		$_SESSION['UID'] = $UserInfo['@UID'];
		$_SESSION['TEL'] = $UserInfo['@TEL'];
		$_SESSION['UserName'] = $UserInfo['@UserName'];
		$_SESSION['Type'] = $UserInfo['@strType'];
		$_SESSION['Online'] = $UserInfo['@Online'];
	}
	DisConnect($conn);
	// 未登录
	if (!isset($_SESSION['Online']) || $_SESSION['Online'] == 0)
	{	
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
	</head>
	<body>
		<ol class="breadcrumb">
		    <li class="active">
		    	基本信息
		    </li>
		    <li class="active">
		    	<a style="text-decoration: none;" href="#">用户管理</a>
		    </li>
		</ol>
		<div class="table-responsive">
		    <table class="table table-striped ">
		        <thead>
		            <tr>
		                <th>姓名</th>
		                <th>身份</th>
		                <th>状态</th>
		                <th>操作</th>
		            </tr>
		        </thead>
		        <tbody id="userlist" name="userlist">
		        </tbody>
		    </table>
		</div>
		<ul id="pagelimit" name="pagelimit" class="pagination" style="float: right;">
		</ul>
		<ul class="nav nav-pills pagination" style="float: left;">
			<li class="active">
				 <a href="#"><span id="UserCount" name="UserCount" class="badge pull-right"></span>正式用户数量：</a>
			</li>
		</ul>
	</body>
	<script>
		// 获取用户管理并显示
		function SetUserManageShow(page)
		{
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/GetUserList.php',
	         		type : "post",
	         		data : {Page:page},
	        		async : false,
    			}
    		).responseText;
    		// 获取传回的json，根据json设置用户列表与翻页显示
			if (ret != '')
			{
				var obj_ret = JSON.parse(ret);
				$('#userlist').html(obj_ret['Res']);
				$('#UserCount').text(obj_ret['UserCount']);
				$('#pagelimit').html(obj_ret['PageLimit']);
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
			// 为每个翻页按钮绑定事件
			$('.pagination li').each(function(){
				// 单击按钮时获取按钮字符串并传入翻页页面
				$(this).bind('click', function(){
					var page_index = $(this).children()[0].innerText;
					SetUserManageShow(page_index);
				});
			});
			// 为每个强制离线按钮绑定事件
			$('.signout').each(function(){
				$(this).bind('click', function(){
					var userid = $(this).parent().attr('id');
					var ret = $.ajax
					(
						{
			        		url : './BasicInfo/forceSignout.php',
			         		type : "post",
			         		data : {UserID:userid},
			        		async : false,
		    			}
		    		).responseText;
		    		// 注销用户
					if (ret != '')
					{
						alert(ret);
						SetUserManageShow(page);
					}
					// 返回为空则认为用户下线，刷新页面
					else
					{
						window.location.reload();
					}
				});
			});
			// 为每个删除按钮添加事件
			$('.del').each(function(){
				$(this).bind('click', function(){
					if (confirm('确认删除账号？'))
					{
						var userid = $(this).parent().attr('id');
						var ret = $.ajax
						(
							{
				        		url : './BasicInfo/deleteUser.php',
				         		type : "post",
				         		data : {UserID:userid, force:0},
				        		async : false,
			    			}
			    	).responseText;
			    		// 删除用户
						if (ret != '')
						{
							// 没有有效删除
							if (ret === '0')
							{
								if (confirm('无法删除账号，可能该用户在其它数据表有记录！是否强制删除用户？（包括其它表的响应记录）'))
								{
									var ret = $.ajax
									(
										{
							        		url : './BasicInfo/deleteUser.php',
							         		type : "post",
							         		data : {UserID:userid, force:1},
							        		async : false,
						    			}
						    		).responseText;
								}
							}
							else
							{
								alert('用户已删除！');
							}
							SetUserManageShow(page);
						}
						// 返回为空则认为用户下线，刷新页面
						else
						{
							window.location.reload();
						}
					}
				});
			});
		}
		$(document).ready(SetUserManageShow(1));
	</script>
</html>