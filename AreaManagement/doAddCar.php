<?php
	/* 新增车辆 */
	session_start();
	include_once('../conn/DBMgr.php');
	$conn = Connect();
	// 当前session显示已登录，更新session以验证
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
		// 不是管理员及以上权限，强制注销
		if ($_SESSION['Type'] != '超级管理员' && $_SESSION['Type'] != '管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问新增车辆');
			unset($_SESSION['Online']);
			exit();
		}
		else
		{
			// 楼盘ID
			if (isset($_REQUEST['aid']) && $_REQUEST['aid'] != '')
				$AreaID = $_REQUEST['aid'];
			else
			{
				echo '楼盘为空！';
				exit();
			}
			// 标志当前用户对楼盘的访问是否合法
			$legal_area = (int)(IsLegalArea($conn, $_SESSION['UserID'], $AreaID)['@Result']);
			// 非法获取其它楼盘信息
			if ($legal_area === 0)
			{
				SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它楼盘信息');
				unset($_SESSION['Online']);
				exit();
			}
			// 车牌号
			if (isset($_REQUEST['carcode']) && $_REQUEST['carcode'] != '')
				$CarCode = $_REQUEST['carcode'];
			else
			{
				echo '车牌号为空！';
				exit();
			}
			// 车主姓名
			if (isset($_REQUEST['name']) && $_REQUEST['name'] != '')
				$Name = $_REQUEST['name'];
			else
			{
				$Name = '';
			}
			// 电话号码
			if (isset($_REQUEST['tel']) && $_REQUEST['tel'] != '')
				$TEL = $_REQUEST['tel'];
			else
			{
				$TEL = '';
			}
			// 新增车辆
			$Result = AddCar($conn, $_SESSION['UserID'], $AreaID, $CarCode, $Name, $TEL, "楼盘管辖-车辆信息-新增车辆");
			echo $Result['@Result'];
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>