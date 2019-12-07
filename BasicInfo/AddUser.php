<?php
	// 新增用户
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
	// 已登录
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 不是超级管理员，强制注销
		if ($_SESSION['Type'] != '超级管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问用户管理-新增用户');
			unset($_SESSION['Online']);
			exit();
		}
	}
	else
	{
		exit();
	}
	DisConnect($conn);
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
		    	<a style="text-decoration: none;" href="#" onclick="$('#mainview').load('./BasicInfo/UserManagement.php')">用户管理</a>
		    </li>
		    <li class="active">
		    	新增用户
		    </li>
		</ol>
		<div class="container" style="width: 80%;">
			<div role="form" class="form-horizontal">
				<br />
	        	<div class="form-group">
	                <label for="name">姓名</label>
	                <input type="text" class="form-control" id="Name" maxlength="1000">
	            </div>
	            <div class="form-group">
	                <label for="type">身份</label>
	                <select id="Type" class="form-control">
        			</select>
	            </div>
	            <div class="form-group">
	                <label for="tel">电话号码</label>
	                <input type="text" class="form-control" id="TEL" maxlength="30">
	            </div>
	            <div class="form-group">
	                <label for="uid">身份证号码</label>
	                <input type="text" class="form-control" id="UID" maxlength="30">
	            </div>
	            <div class="form-group">
	                <label for="pwd1">密码</label>
	                <input type="password" class="form-control" id="Pwd1" maxlength="1000">
	            </div>
	            <div class="form-group">
	                <label for="pwd2">确认密码</label>
	                <input type="password" class="form-control" id="Pwd2" maxlength="1000">
	            </div>
	            <div class="form-group">
	                <label for="sec">安全字符串</label>
	                <input type="text" class="form-control" id="Sec" maxlength="1000">
	            </div>
	            <div class="form-group">
	            	<div class="form-group col-lg-5">
		            	<label>管辖范围(已选)</label>
		            	<select id="Area_sel" multiple="multiple" class="form-control">
		            	</select>
		            </div>
		            <div class="form-group col-lg-3">
		            	<label>&nbsp;</label>
		            	<button id="addone" class="form-control btn btn-primary">添加</button>
		            	<button id="addall" class="form-control btn btn-primary">全部添加</button>
	            		<button id="delone" class="form-control btn btn-warning">删除</button>
	            		<button id="delall" class="form-control btn btn-warning">全部删除</button>
		            </div>
		            <div class="form-group col-lg-5">
		            	<label>未选列表</label>
		            	<select id="Area_not" multiple="multiple" class="form-control">
		            	</select>
		            </div>
	            </div>
	            <div class="form-group">
	                <button id="submitadd" name="submitadd" type="button" class="btn btn-success btn-block">提交新增</button>
	            </div>
	        </div>
        </div>
	</body>
	<script>
		// 获取身份列表与管辖范围列表并显示
		function SetInfoShow()
		{
			var ret_Type = $.ajax
			(
				{
	        		url : './BasicInfo/GetUserTypeList.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret_Type = JSON.parse(ret_Type);
    		$('#Type').html(obj_ret_Type);
    		var ret_Area = $.ajax
			(
				{
	        		url : './BasicInfo/GetAreaList.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret_Area = JSON.parse(ret_Area);
    		$('#Area_not').html(obj_ret_Area);
		}
		// 单击修改个人信息时提交修改
		$('#submitadd').bind('click', function(){
			var Name = $('#Name').val();
    		var TEL = $('#TEL').val();
    		var UID = $('#UID').val();
    		var Type = $('#Type').val();
    		var Pwd1 = $('#Pwd1').val();
			var Pwd2 = $('#Pwd2').val();
			var Sec = $('#Sec').val();
    		// 已选管辖范围
    		var Area = [];
			$('#Area_sel').children().each(function(){
				Area.push($(this).val());
			});
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/doAddUser.php',
	         		type : "post",
	         		data : {name:Name, tel:TEL, uid:UID, type:Type, pwd1:Pwd1, pwd2:Pwd2, sec:Sec, area_list:Area},
	        		async : false,
    			}
    		).responseText;
    		if (ret != '')
    		{
	    		alert(ret);
	    		if (ret === '新增成功！')
	    		{
	    			$('#mainview').load('./BasicInfo/UserManagement.php');
	    		}
    		}
    		// 返回为空则认为用户下线，刷新页面
    		else
    		{
    			window.location.reload();
    		}
		});
		// 删除按钮
		$('#delone').bind('click', function(){
			var ops = $('#Area_sel').children();
			for (var i = ops.length - 1; i >= 0; i--)
			{
				if (ops[i].selected == true)
			    {
					$('#Area_not').append(ops[i]);
				}
			}
		});
		// 添加按钮
		$('#addone').bind('click', function(){
			var ops = $('#Area_not').children();
			for (var i = ops.length - 1; i >= 0; i--)
			{
				if (ops[i].selected == true)
			    {
					$('#Area_sel').append(ops[i]);
				}
			}
		});
		// 添加所有按钮
		$('#addall').bind('click', function(){
			var ops = $('#Area_not').children();
			for (var i = ops.length - 1; i >= 0; i--)
			{
				$('#Area_sel').append(ops[i]);
			}
		});
		// 删除所有按钮
		$('#delall').bind('click', function(){
			var ops = $('#Area_sel').children();
			for (var i = ops.length - 1; i >= 0; i--)
			{
				$('#Area_not').append(ops[i]);
			}
		});
		// 打开新增用户页面时获取身份列表与管辖范围列表并显示
		$(document).ready(SetInfoShow());
	</script>
</html>