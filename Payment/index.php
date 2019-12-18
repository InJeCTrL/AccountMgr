<?php
	// 查看/修改住户信息
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
		    	楼盘管辖
		    </li>
		    <li class="active">
		    	<a style="text-decoration: none;" href="#" onclick="$('#mainview').load('./AreaManagement/HouseHoldList.php')">住户信息</a>
		    </li>
		    <li class="active">
		    	住户详细信息
		    </li>
		</ol>
		<div class="container" style="width: 80%;">
			<div role="form" class="form-horizontal">
				<br />
				<div class="form-group">
	                <label for="AreaID">归属楼盘</label>
	                <?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <select id="AreaID" class="form-control">
        			</select>
        			<?php
						}
						else
						{
					?>
					<select disabled="disabled" id="AreaID" class="form-control">
        			</select>
					<?php
						}
        			?>
	            </div>
	            <div class="form-group">
	                <label for="BID">归属楼栋</label>
	                <?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <select id="BID" class="form-control">
        			</select>
        			<?php
						}
						else
						{
					?>
					<select disabled="disabled" id="BID" class="form-control">
        			</select>
					<?php
						}
        			?>
	            </div>
	            <div class="form-group">
	            	<label for="RoomCode">门牌号</label>
	            	<?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <input type="text" class="form-control" id="RoomCode" maxlength="500">
                	<?php
						}
						else
						{
					?>
					<input disabled="disabled" type="text" class="form-control" id="RoomCode" maxlength="500">
					<?php
						}
        			?>
	            </div>
	            <div class="form-group">
	                <label for="Name">住户姓名</label>
	                <?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <input type="text" class="form-control" id="Name" maxlength="1000">
	            	<?php
						}
						else
						{
					?>
					<input disabled="disabled" type="text" class="form-control" id="Name" maxlength="1000">
					<?php
						}
        			?>
	            </div>
	            <div class="form-group">
	                <label for="TEL">住户电话号码</label>
	                <?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <input type="text" class="form-control" id="TEL" maxlength="30">
	            	<?php
						}
						else
						{
					?>
					<input disabled="disabled" type="text" class="form-control" id="TEL" maxlength="30">
					<?php
						}
        			?>
	            </div>
	            <div class="form-group">
	                <label for="square">住房面积</label>
	                <?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <input type="text" class="form-control" id="square" maxlength="30">
	                <?php
						}
						else
						{
					?>
					<input disabled="disabled" type="text" class="form-control" id="square" maxlength="30">
					<?php
						}
        			?>
	            </div>
	            <?php
	                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
					{
				?>
                <div class="form-group">
	                <button id="mdfinfo" name="mdfinfo" type="button" class="btn btn-success btn-block">提交修改</button>
	            </div>
    			<?php
					}
				?>
	        </div>
        </div>
	</body>
	<script>
		var hid = <?php echo $_REQUEST['HID']; ?>;
		// 获取住户信息并显示
		function SetInfoShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/GetHouseHoldInfo.php',
	         		type : "post",
	         		data : {HID:hid},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret = JSON.parse(ret);
    		$('#AreaID').html(obj_ret['AreaID']);
    		$('#BID').html(obj_ret['BID']);
    		$('#RoomCode').val(obj_ret['RoomCode']);
    		$('#Name').val(obj_ret['Name']);
    		$('#TEL').val(obj_ret['TEL']);
    		$('#square').val(obj_ret['square']);
		}
		// 单击修改住户信息时提交修改
		$('#mdfinfo').bind('click', function(){
			var AreaID = $('#AreaID').val();
    		var BID = $('#BID').val();
    		var RoomCode = $('#RoomCode').val();
    		var Name = $('#Name').val();
    		var TEL = $('#TEL').val();
    		var square = $('#square').val();
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/doSetHouseHoldInfo.php',
	         		type : "post",
	         		data : {HID:hid, areaid:AreaID, bid:BID, roomcode:RoomCode, name:Name, tel:TEL, sq:square},
	        		async : false,
    			}
    		).responseText;
    		if (ret != '')
    		{
	    		alert(ret);
    		}
    		// 返回为空则认为用户下线，刷新页面
    		else
    		{
    			window.location.reload();
    		}
		});
		// 改变楼盘选中
		$('#AreaID').bind('change', function(){
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
		});
		// 打开详细信息页面时获取详细信息并显示
		$(document).ready(SetInfoShow());
	</script>
</html>