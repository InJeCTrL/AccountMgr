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
        <h3>条件筛选</h3>
        <div class="row">
            <div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">身份证号码</span>
                    <input id="UID" class="form-control" type="text">
                </div>
            </div>
            <div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">电话号码</span>
                    <input id="TEL" class="form-control" type="text">
                </div>
            </div>
            <div class="form-group col-lg-4">
                <div class="input-group">
                	<span class="input-group-addon">姓名</span>
                    <input id="Name" class="form-control" type="text">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">用户身份</span>
                    <select id="UserType" class="form-control">
                    </select>
                </div>
            </div>
            <div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">在线状态</span>
                    <select id="Online" class="form-control">
                    	<option value="">任意</option>
                    	<option value="1">在线</option>
                    	<option value="0">离线</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">管辖范围</span>
                    <select id="Area" class="form-control">
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
        	<div class="form-group col-lg-6">
        		<button id="doquery" class="btn btn-success btn-block">查询</button>
        	</div>
        	<div class="form-group col-lg-6">
        		<button class="btn btn-primary btn-block">新增用户</button>
        	</div>
        </div>
		<div class="table-responsive">
		    <table class="table table-striped ">
		        <thead>
		            <tr>
		            	<th class="col-lg-1"><input type="checkbox" id="chkall" /></th>
		                <th class="col-lg-3">姓名</th>
		                <th class="col-lg-3">身份</th>
		                <th class="col-lg-2">状态</th>
		                <th class="col-lg-3">操作</th>
		            </tr>
		        </thead>
		        <tbody id="userlist" name="userlist">
		        </tbody>
		    </table>
		</div>
		<div class="row">
        	<div class="form-group col-lg-3">
        		<div class="input-group">
                    <span class="input-group-addon">批量操作：</span>
                    <button id="multidel" class="btn btn-danger btn-block">删除</button>
                    <button id="multisignout" class="btn btn-warning btn-block">强制离线</button>
               </div>
        	</div>
        </div>
		<ul id="pagelimit" name="pagelimit" class="pagination" style="float: right;">
		</ul>
		<ul class="nav nav-pills pagination" style="float: left;">
			<li class="active">
				 <a href="#"><span id="UserCount" name="UserCount" class="badge pull-right"></span>查得用户数量：</a>
			</li>
		</ul>
	</body>
	<script>
		var search_UID = '', search_TEL = '', search_Name = '',
			search_type = '', search_online = '', search_area = '',
			page = 1;
		// 强制离线指定ID的用户账号
		function forcesignout(userid)
		{
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
				SetUserManageShow();
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
		}
		// 删除指定ID的用户账号
		function deleteUser(userid)
		{
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
				SetUserManageShow();
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
		}
		
		// 获取用户管理并显示
		function SetUserManageShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/GetUserList.php',
	         		type : "post",
	         		data : {Page:page, UID:search_UID, TEL:search_TEL, Name:search_Name, Type:search_type, Online:search_online, Area:search_area},
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
					page = $(this).children()[0].innerText;
					SetUserManageShow();
				});
			});
			// 为每个强制离线按钮绑定事件
			$('.signout').each(function(){
				$(this).bind('click', function(){
					var userid = $(this).parent().attr('id');
					forcesignout(userid);
				});
			});
			// 为每个删除按钮添加事件
			$('.del').each(function(){
				$(this).bind('click', function(){
					if (confirm('确认删除账号？'))
					{
						var userid = $(this).parent().attr('id');
						deleteUser(userid);
					}
				});
			});
		}
		// 全选
		$('#chkall').bind('click', function(){
			$('.chksel').each(function(){
				$(this).prop('checked', $('#chkall').is(':checked'));
				$(this).attr('checked', $('#chkall').is(':checked'));
			});
		});
		// 批量删除
		$('#multidel').bind('click', function(){
			if (confirm('确认批量删除选中的用户账号？'))
			{
				$('.chksel').each(function(){
					// 获取选中行的ID并提交删除
					if ($(this).is(':checked') == true)
					{
						var userid = $(this).parent().parent().children().eq(4).children().eq(0).attr('id');
						deleteUser(userid);
					}
				});
			}
		});
		// 批量强制离线
		$('#multisignout').bind('click', function(){
			if (confirm('确认批量强制离线选中的用户账号？'))
			{
				$('.chksel').each(function(){
					// 获取选中行的ID并强制离线
					if ($(this).is(':checked') == true)
					{
						var userid = $(this).parent().parent().children().eq(4).children().eq(0).attr('id');
						forcesignout(userid);
					}
				});
			}
		});
		// 查询按钮
		$('#doquery').bind('click', function(){
			// 查询赋值
			search_UID = $('#UID').val();
			search_TEL = $('#TEL').val();
			search_Name = $('#Name').val();
			search_type = $('#UserType').val();
			search_online = $('#Online').val();
			search_area = $('#Area').val();
			SetUserManageShow();
		});
		$(document).ready(function(){
			SetUserManageShow();
			var ret_type = $.ajax
			(
				{
	        		url : './BasicInfo/GetUserTypeList.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		$('#UserType').html(JSON.parse(ret_type));
    		var ret_area = $.ajax
			(
				{
	        		url : './BasicInfo/GetAreaList.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		$('#Area').html(JSON.parse(ret_area));
		});
	</script>
</html>