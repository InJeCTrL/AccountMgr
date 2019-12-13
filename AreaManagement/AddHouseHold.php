<?php
	// 新增住户
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
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问楼栋列表-新增楼栋');
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
		    	<a style="text-decoration: none;" href="#" onclick="$('#mainview').load('./AreaManagement/HouseHoldList.php')">住户信息</a>
		    </li>
		    <li class="active">
		    	新增住户
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
	                <label for="BID">归属楼栋</label>
	                <select id="BID" class="form-control">
        			</select>
	            </div>
	            <div class="form-group">
	                <label for="RoomCode">门牌号</label>
	                <input type="text" class="form-control" id="RoomCode" maxlength="500">
	            </div>
	            <div class="form-group">
	                <label for="Name">住户姓名</label>
	                <input type="text" class="form-control" id="Name" maxlength="1000">
	            </div>
	            <div class="form-group">
	                <label for="TEL">住户电话号码</label>
	                <input type="text" class="form-control" id="TEL" maxlength="30">
	            </div>
	            <div class="form-group">
	                <label for="square">住房面积</label>
	                <input type="text" class="form-control" id="square" maxlength="30">
	            </div>
	            <div class="form-group">
	                <button id="submitadd" name="submitadd" type="button" class="btn btn-success btn-block">提交新增</button>
	            </div>
	        </div>
        </div>
	</body>
	<script>
		// 刷新归属楼栋列表
		function FlashBuildingList()
		{
			var aid = $('#AreaID').val();
			var ret_buildinglist = $.ajax
			(
				{
	        		url : './AreaManagement/GetBuildingList_select.php',
	         		type : "post",
	         		data : {areaid:aid},
	        		async : false,
    			}
    		).responseText;
    		$('#BID').html(JSON.parse(ret_buildinglist));
		}
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
    		var BID = $('#BID').val();
    		var RoomCode = $('#RoomCode').val();
    		var Name = $('#Name').val();
    		var TEL = $('#TEL').val();
    		var square = $('#square').val();
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/doAddHouseHold.php',
	         		type : "post",
	         		data : {aid:AreaID, bid:BID, roomcode:RoomCode, name:Name, tel:TEL, sq:square},
	        		async : false,
    			}
    		).responseText;
    		if (ret != '')
    		{
	    		alert(ret);
	    		if (ret === '新增成功！')
	    		{
	    			$('#mainview').load('./AreaManagement/HouseHoldList.php');
	    		}
    		}
    		// 返回为空则认为用户下线，刷新页面
    		else
    		{
    			window.location.reload();
    		}
		});
		// 改变楼盘选中
		$('#AreaID').bind('change', function(){
			FlashBuildingList();
		});
		// 打开新增住户页面时获取管辖范围列表并显示
		$(document).ready(function(){
			SetInfoShow();
			FlashBuildingList();
		});
	</script>
</html>