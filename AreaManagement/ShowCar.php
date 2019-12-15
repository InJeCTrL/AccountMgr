<?php
	// 查看/修改车辆信息
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
		    	<a style="text-decoration: none;" href="#" onclick="$('#mainview').load('./AreaManagement/CarList.php')">车辆信息</a>
		    </li>
		    <li class="active">
		    	车辆详细信息
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
	            	<label for="CarCode">车牌号</label>
	            	<?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <input type="text" class="form-control" id="CarCode" maxlength="20">
                	<?php
						}
						else
						{
					?>
					<input disabled="disabled" type="text" class="form-control" id="CarCode" maxlength="20">
					<?php
						}
        			?>
	            </div>
	            <div class="form-group">
	                <label for="Name">车主姓名</label>
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
	                <label for="TEL">电话号码</label>
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
		var cid = <?php echo $_REQUEST['CID']; ?>;
		// 获取车辆信息并显示
		function SetInfoShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/GetCarInfo.php',
	         		type : "post",
	         		data : {CID:cid},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret = JSON.parse(ret);
    		$('#AreaID').html(obj_ret['AreaID']);
    		$('#CarCode').val(obj_ret['CarCode']);
    		$('#Name').val(obj_ret['Name']);
    		$('#TEL').val(obj_ret['TEL']);
		}
		// 单击修改车辆信息时提交修改
		$('#mdfinfo').bind('click', function(){
			var AreaID = $('#AreaID').val();
    		var CarCode = $('#CarCode').val();
    		var Name = $('#Name').val();
    		var TEL = $('#TEL').val();
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/doSetCarInfo.php',
	         		type : "post",
	         		data : {CID:cid, areaid:AreaID, carcode:CarCode, name:Name, tel:TEL},
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
		// 打开详细信息页面时获取详细信息并显示
		$(document).ready(SetInfoShow());
	</script>
</html>