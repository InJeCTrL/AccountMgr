<?php
	// 查看/修改商铺信息
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
		    	<a style="text-decoration: none;" href="#" onclick="$('#mainview').load('./AreaManagement/ShopList.php')">商铺信息</a>
		    </li>
		    <li class="active">
		    	商铺详细信息
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
	            	<label for="ShopName">商铺名称</label>
	            	<?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <input type="text" class="form-control" id="ShopName" maxlength="1000">
                	<?php
						}
						else
						{
					?>
					<input disabled="disabled" type="text" class="form-control" id="ShopName" maxlength="1000">
					<?php
						}
        			?>
	            </div>
	            <div class="form-group">
	                <label for="Name">店主姓名</label>
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
	            <div class="form-group">
	                <label for="PMCU">预设物业费单价(元/每平米)</label>
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
	                <label for="ELU">预设电费单价(元/度)</label>
	                <?php
		                if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
					?>
	                <input type="text" class="form-control" id="ELU" maxlength="30">
	                <?php
						}
						else
						{
					?>
					<input disabled="disabled" type="text" class="form-control" id="ELU" maxlength="30">
					<?php
						}
        			?>
	            </div>
	            <div class="form-group">
	                <label for="TF">预设垃圾清运费</label>
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
		var sid = <?php echo $_REQUEST['SID']; ?>;
		// 获取商铺信息并显示
		function SetInfoShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/GetShopInfo.php',
	         		type : "post",
	         		data : {SID:sid},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret = JSON.parse(ret);
    		$('#AreaID').html(obj_ret['AreaID']);
    		$('#ShopName').val(obj_ret['ShopName']);
    		$('#Name').val(obj_ret['Name']);
    		$('#TEL').val(obj_ret['TEL']);
    		$('#PMCU').val(obj_ret['PMCU']);
    		$('#ELU').val(obj_ret['ELU']);
    		$('#TF').val(obj_ret['TF']);
		}
		// 单击修改商铺信息时提交修改
		$('#mdfinfo').bind('click', function(){
			var AreaID = $('#AreaID').val();
    		var ShopName = $('#ShopName').val();
    		var Name = $('#Name').val();
    		var TEL = $('#TEL').val();
    		var PMCU = $('#PMCU').val();
    		var ELU = $('#ELU').val();
    		var TF = $('#TF').val();
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/doSetShopInfo.php',
	         		type : "post",
	         		data : {SID:sid, areaid:AreaID, shopname:ShopName, name:Name, tel:TEL, pmcu:PMCU, elu:ELU, tf:TF},
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