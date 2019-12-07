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
		    	审核注册
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
        	<div class="form-group col-lg-12">
        		<button id="doquery" class="btn btn-success btn-block">查询</button>
        	</div>
        </div>
		<div class="table-responsive">
		    <table class="table table-striped ">
		        <thead>
		            <tr>
		            	<th class="col-lg-1"><input type="checkbox" id="chkall" /></th>
		                <th class="col-lg-3">姓名</th>
		                <th class="col-lg-3">电话号码</th>
		                <th class="col-lg-2">身份证号码</th>
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
                    <button id="multideny" class="btn btn-warning btn-block">拒绝</button>
               </div>
        	</div>
        </div>
		<ul id="pagelimit" name="pagelimit" class="pagination" style="float: right;">
		</ul>
		<ul class="nav nav-pills pagination" style="float: left;">
			<li class="active">
				 <a href="#"><span id="UserCount" name="UserCount" class="badge pull-right"></span>查得待审核用户数量：</a>
			</li>
		</ul>
	</body>
	<script>
		var search_UID = '', search_TEL = '', search_Name = '',
			page = 1;
		// 拒绝指定账号的申请
		function denyUser(userid)
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
    		// 删除用户
			if (ret != '0')
			{
				SetApplyApproveShow();
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
		}
		// 获取用户管理并显示
		function SetApplyApproveShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/GetRegList.php',
	         		type : "post",
	         		data : {Page:page, UID:search_UID, TEL:search_TEL, Name:search_Name},
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
					SetApplyApproveShow();
				});
			});
			// 为每个拒绝按钮添加事件
			$('.deny').each(function(){
				$(this).bind('click', function(){
					var userid = $(this).parent().attr('id');
					denyUser(userid);
				});
			});
			// 为每个同意按钮添加事件
			$('.pass').each(function(){
				$(this).bind('click', function(){
					var userid = $(this).parent().attr('id');
					$('#mainview').load('./BasicInfo/SetRole.php?UserID=' + userid);
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
		$('#multideny').bind('click', function(){
			if (confirm('确认批量拒绝选中的注册申请？'))
			{
				$('.chksel').each(function(){
					// 获取选中行的ID并提交删除
					if ($(this).is(':checked') == true)
					{
						var userid = $(this).parent().parent().children().eq(4).children().eq(0).attr('id');
						denyUser(userid);
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
			SetApplyApproveShow();
		});
		$(document).ready(function(){
			SetApplyApproveShow();
		});
	</script>
</html>