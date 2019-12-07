<?php
	session_start();
	include "conn/DBMgr.php";
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
		<ul id="main-nav" class="nav nav-tabs nav-stacked" style="">
		    <li>
		        <a href="#systemSetting1" class="nav-header collapsed" data-toggle="collapse" style="background: darkorange;color: white;">
		        	<i class="glyphicon glyphicon-list-alt"></i>
					基本信息
		            <span class="pull-right glyphicon glyphicon-chevron-down"></span>
		        </a>
		        <ul id="systemSetting1" class="nav nav-list collapse secondmenu" style="height: 0px;">
		            <li><a id="UserInfo" name="UserInfo" href="#"><i class="glyphicon glyphicon-user"></i>个人信息</a></li>
		            <?php
		            	if ($_SESSION['Type'] === '超级管理员')
						{
					?>
		            <li><a id="UserManage" name="UserManage" href="#"><i class="glyphicon glyphicon-th-list"></i>用户管理</a></li>
		            <li><a id="ApplyApprove" href="#"><i class="glyphicon glyphicon-asterisk"></i>审核注册</a></li>
		            <li><a href="#"><i class="glyphicon glyphicon-edit"></i>楼盘列表</a></li>
		            <li><a href="#"><i class="glyphicon glyphicon-eye-open"></i>操作日志</a></li>
		            <?php
						}
		            ?>
		        </ul>
		    </li>
		    <li>
		        <a href="#systemSetting2" class="nav-header collapsed" data-toggle="collapse" style="background: darkorange;color: white;">
		        	<i class="glyphicon glyphicon-dashboard"></i>
					楼盘管辖
		            <span class="pull-right glyphicon glyphicon-chevron-down"></span>
		        </a>
		        <ul id="systemSetting2" class="nav nav-list collapse secondmenu" style="height: 0px;">
		            <li><a href="#"><i class="glyphicon glyphicon-calendar"></i>数据概览</a></li>
		            <li><a href="#"><i class="glyphicon glyphicon-home"></i>楼栋列表</a></li>
		            <li><a href="#"><i class="glyphicon glyphicon-asterisk"></i>业/户主信息</a></li>
		            <?php
		            	if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
						{
		            ?>
		            <li><a href="#"><i class="glyphicon glyphicon-edit"></i>常费用设置</a></li>
		            <?php
		            	}
		            ?>
		        </ul>
		    </li>
		    <li>
		        <a href="#" style="background: darkorange;color: white;">
		            <i class="glyphicon glyphicon-credit-card"></i>
					缴费充值
		        </a>
		    </li>
		    <?php
            	if ($_SESSION['Type'] === '超级管理员')
				{
            ?>
		    <li>
		        <a href="#systemSetting4" class="nav-header collapsed" data-toggle="collapse" style="background: darkorange;color: white;">
		        	<i class="glyphicon glyphicon-fire"></i>
					系统维护
		            <span class="pull-right glyphicon glyphicon-chevron-down"></span>
		        </a>
		        <ul id="systemSetting4" class="nav nav-list collapse secondmenu" style="height: 0px;">
		            <li><a href="#"><i class="glyphicon glyphicon-floppy-save"></i>数据备份</a></li>
		            <li><a href="#"><i class="glyphicon glyphicon-saved"></i>数据恢复</a></li>
		        </ul>
		    </li>
		    <?php
				}
		    ?>
		</ul>
	</body>
	<script>
		// 单击个人信息
		$('#UserInfo').bind('click', function(){
			$('#mainview').load('./BasicInfo/index.php');
		});
		// 单击用户管理
		$('#UserManage').bind('click', function(){
			$('#mainview').load('./BasicInfo/UserManagement.php');
		});
		// 单击审核注册
		$('#ApplyApprove').bind('click', function(){
			$('#mainview').load('./BasicInfo/ApplyApprove.php');
		});
	</script>
</html>