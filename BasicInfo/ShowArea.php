<?php
	// 查看/修改楼盘
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
		    	<a style="text-decoration: none;" href="#" onclick="$('#mainview').load('./BasicInfo/AreaList.php')">楼盘列表</a>
		    </li>
		    <li class="active">
		    	楼盘信息查看/编辑
		    </li>
		</ol>
		
		<div class="container" style="width: 80%;">
			<div role="form" class="form-horizontal">
				<br />
	        	<div class="form-group">
	                <label for="name">楼盘名称</label>
	                <input type="text" class="form-control" id="Name" maxlength="1000">
	            </div>
	            <div class="form-group">
	                <button id="mdfinfo" name="mdfinfo" type="button" class="btn btn-success btn-block">提交修改</button>
	            </div>
	        </div>
		</div>
	</body>
	<script>
		var AreaID = <?php echo $_REQUEST['AreaID']; ?>;
		// 获取楼盘信息并显示
		function SetInfoShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/GetAreaInfo.php',
	         		type : "post",
	         		data : {aid:AreaID},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret = JSON.parse(ret);
    		$('#Name').val(obj_ret['Name']);
		}
		// 单击修改信息时提交修改
		$('#mdfinfo').bind('click', function(){
			var Name = $('#Name').val();
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/doSetAreaInfo.php',
	         		type : "post",
	         		data : {aid:AreaID, name:Name},
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