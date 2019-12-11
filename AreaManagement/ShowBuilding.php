<?php
	// 查看/修改楼栋信息
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
		    	<a style="text-decoration: none;" href="#" onclick="$('#mainview').load('./AreaManagement/BuildingList.php')">楼栋列表</a>
		    </li>
		    <li class="active">
		    	楼栋详细信息
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
	                <label for="BNo">楼栋号</label>
	                <?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <input type="text" class="form-control" id="BNo" maxlength="1000">
        			<?php
						}
						else
						{
					?>
					<input disabled="disabled" type="text" class="form-control" id="BNo" maxlength="1000">
					<?php
						}
        			?>
	            </div>
	            <div class="form-group">
	                <label for="PMCU">预设统一物业费单价(元/每平米)</label>
	                <?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <input type="text" class="form-control" id="PMCU" maxlength="30">
        			<?php
						}
						else
						{
					?>
					<input disabled="disabled" type="text" class="form-control" id="PMCU" maxlength="30">
					<?php
						}
        			?>
	            </div>
	            <div class="form-group">
	                <label for="PRSF">预设统一公摊费(元)</label>
	                <?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <input type="text" class="form-control" id="PRSF" maxlength="30">
        			<?php
						}
						else
						{
					?>
					<input disabled="disabled" type="text" class="form-control" id="PRSF" maxlength="30">
					<?php
						}
        			?>
	            </div>
	            <div class="form-group">
	                <label for="TF">预设统一垃圾清运费(元)</label>
	                <?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <input type="text" class="form-control" id="TF" maxlength="30">
        			<?php
						}
						else
						{
					?>
					<input disabled="disabled" type="text" class="form-control" id="TF" maxlength="30">
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
		var bid = <?php echo $_REQUEST['BID']; ?>;
		// 获取楼栋信息并显示
		function SetInfoShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/GetBuildingInfo.php',
	         		type : "post",
	         		data : {BID:bid},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret = JSON.parse(ret);
    		$('#AreaID').html(obj_ret['AreaID']);
    		$('#BNo').val(obj_ret['BNo']);
    		$('#PMCU').val(obj_ret['PMCU']);
    		$('#PRSF').val(obj_ret['PRSF']);
    		$('#TF').val(obj_ret['TF']);
		}
		// 单击修改楼栋信息时提交修改
		$('#mdfinfo').bind('click', function(){
			var AreaID = $('#AreaID').val();
    		var BNo = $('#BNo').val();
    		var PMCU = $('#PMCU').val();
    		var PRSF = $('#PRSF').val();
    		var TF = $('#TF').val();
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/doSetBuildingInfo.php',
	         		type : "post",
	         		data : {BID:bid, areaid:AreaID, bno:BNo, pmcu:PMCU, prsf:PRSF, tf:TF},
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