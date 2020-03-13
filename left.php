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
		            <li><a id="AreaList" href="#"><i class="glyphicon glyphicon-edit"></i>楼盘列表</a></li>
		            <li><a id="LogList" href="#"><i class="glyphicon glyphicon-eye-open"></i>操作日志</a></li>
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
		            <li><a id="DataView" href="#"><i class="glyphicon glyphicon-calendar"></i>数据概览</a></li>
		            <li><a id="BuildingList" href="#"><i class="glyphicon glyphicon-home"></i>楼栋列表</a></li>
		            <li><a id="HouseHoldList" href="#"><i class="glyphicon glyphicon-asterisk"></i>住户信息</a></li>
		            <li><a id="ShopList" href="#"><i class="glyphicon glyphicon-shopping-cart"></i>商铺信息</a></li>
		            <li><a id="CarList" href="#"><i class="glyphicon glyphicon-flag"></i>车辆信息</a></li>
		        </ul>
		    </li>
		    <li>
		        <a href="#systemSetting3" class="nav-header collapsed" data-toggle="collapse" style="background: darkorange;color: white;">
		        	<i class="glyphicon glyphicon-credit-card"></i>
					收费与账目
		            <span class="pull-right glyphicon glyphicon-chevron-down"></span>
		        </a>
		        <ul id="systemSetting3" class="nav nav-list collapse secondmenu" style="height: 0px;">
		            <li><a id="AccountList" href="#"><i class="glyphicon glyphicon-calendar"></i>账目清单</a></li>
		    <?php
            	if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
				{
            ?>
		            <li><a id="ImportTicket" href="#"><i class="glyphicon glyphicon-plus"></i>票据录入</a></li>
		    <?php
				}
		    ?>
		            <li><a id="PaymentMonth" href="#"><i class="glyphicon glyphicon-tags"></i>缴费月份</a></li>
		        </ul>
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
		            <li><a id="DataBak" href="#"><i class="glyphicon glyphicon-floppy-save"></i>数据备份</a></li>
		            <li><a id="DataRec" href="#"><i class="glyphicon glyphicon-saved"></i>数据恢复</a></li>
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
		// 单击楼盘列表
		$('#AreaList').bind('click', function(){
			$('#mainview').load('./BasicInfo/AreaList.php');
		});
		// 单击操作日志
		$('#LogList').bind('click', function(){
			$('#mainview').load('./BasicInfo/LogList.php');
		});
		// 单击数据概览
		$('#DataView').bind('click', function(){
			$('#mainview').load('./AreaManagement/index.php');
		});
		// 单击楼栋列表
		$('#BuildingList').bind('click', function(){
			$('#mainview').load('./AreaManagement/BuildingList.php');
		});
		// 单击住户信息
		$('#HouseHoldList').bind('click', function(){
			$('#mainview').load('./AreaManagement/HouseHoldList.php');
		});
		// 单击商铺信息
		$('#ShopList').bind('click', function(){
			$('#mainview').load('./AreaManagement/ShopList.php');
		});
		// 单击车辆信息
		$('#CarList').bind('click', function(){
			$('#mainview').load('./AreaManagement/CarList.php');
		});
		// 单击数据备份
		$('#DataBak').bind('click', function(){
			$('#mainview').load('./SysSetting/index.php');
		});
		// 单击数据恢复
		$('#DataRec').bind('click', function(){
			$('#mainview').load('./SysSetting/DataRec.php');
		});
		// 单击账目清单
		$('#AccountList').bind('click', function(){
			$('#mainview').load('./Payment/index.php');
		});
		// 单击票据录入
		$('#ImportTicket').bind('click', function(){
			$('#mainview').load('./Payment/ImportTicket.php');
		});
		// 单击缴费月份
		$('#PaymentMonth').bind('click', function(){
			$('#mainview').load('./Payment/PaymentMonth.php');
		});
	</script>
</html>