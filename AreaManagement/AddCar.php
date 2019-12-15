<?php
	// 新增车辆
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
		// 不是管理员及以上，强制注销
		if ($_SESSION['Type'] != '超级管理员' && $_SESSION['Type'] != '管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问商铺信息-新增商铺');
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
		    	楼盘管辖
		    </li>
		    <li class="active">
		    	<a style="text-decoration: none;" href="#" onclick="$('#mainview').load('./AreaManagement/CarList.php')">车辆信息</a>
		    </li>
		    <li class="active">
		    	新增车辆
		    </li>
		</ol>
		<div class="container" style="width: 80%;">
			<div role="form" class="form-horizontal">
				<br />
	        	<div class="form-group">
	                <label for="AreaID">归属楼盘</label>
	                <select id="AreaID" class="form-control">
        			</select>
	           </div>
	            <div class="form-group">
	                <label for="CarCode">车牌号</label>
	                <input type="text" class="form-control" id="CarCode" maxlength="20">
	            </div>
	            <div class="form-group">
	                <label for="Name">车主姓名</label>
	                <input type="text" class="form-control" id="Name" maxlength="1000">
	            </div>
	            <div class="form-group">
	                <label for="TEL">电话号码</label>
	                <input type="text" class="form-control" id="TEL" maxlength="30">
	            </div>
	            <div class="form-group">
	                <button id="submitadd" name="submitadd" type="button" class="btn btn-success btn-block">提交新增</button>
	            </div>
	        </div>
        </div>
	</body>
	<script>
		// 获取管辖范围列表并显示
		function SetInfoShow()
		{
    		var ret_Area = $.ajax
			(
				{
	        		url : './AreaManagement/GetUserAreaList.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret_Area = JSON.parse(ret_Area);
    		$('#AreaID').html(obj_ret_Area);
		}
		// 单击提交新增
		$('#submitadd').bind('click', function(){
			var AreaID = $('#AreaID').val();
    		var CarCode = $('#CarCode').val();
    		var Name = $('#Name').val();
    		var TEL = $('#TEL').val();
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/doAddCar.php',
	         		type : "post",
	         		data : {aid:AreaID, carcode:CarCode, name:Name, tel:TEL},
	        		async : false,
    			}
    		).responseText;
    		if (ret != '')
    		{
	    		alert(ret);
	    		if (ret === '新增成功！')
	    		{
	    			$('#mainview').load('./AreaManagement/CarList.php');
	    		}
    		}
    		// 返回为空则认为用户下线，刷新页面
    		else
    		{
    			window.location.reload();
    		}
		});
		// 打开新增车辆页面时获取管辖范围列表并显示
		$(document).ready(function(){
			SetInfoShow();
		});
	</script>
</html>