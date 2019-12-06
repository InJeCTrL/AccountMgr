<?php
	/* 基本信息-用户管理-修改用户信息 */
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
		// 不是超级管理员，强制注销
		if ($_SESSION['Type'] != '超级管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问用户管理-修改用户信息');
			unset($_SESSION['Online']);
			exit();
		}
		// 是超级管理员
		else
		{
			// 用户ID
			if (isset($_REQUEST['userid']) && $_REQUEST['userid'] != '')
				$UserID = $_REQUEST['userid'];
			else
			{
				echo '用户不存在！';
				exit();
			}
			// 姓名
			if (isset($_REQUEST['name']) && $_REQUEST['name'] != '')
				$Name = $_REQUEST['name'];
			else
			{
				echo '姓名为空！';
				exit();
			}
			// 身份
			if (isset($_REQUEST['type']) && $_REQUEST['type'] != '')
				$Type = $_REQUEST['type'];
			else
			{
				echo '身份为空！';
				exit();
			}
			// 电话号码
			if (isset($_REQUEST['tel']) && $_REQUEST['tel'] != '')
				$TEL = $_REQUEST['tel'];
			else
			{
				echo '电话号码为空！';
				exit();
			}
			// 身份证号码
			if (isset($_REQUEST['uid']) && $_REQUEST['uid'] != '')
				$UID = $_REQUEST['uid'];
			else
			{
				echo '身份证号码为空！';
				exit();
			}
			// 管辖范围
			if (isset($_REQUEST['area_list']) && $_REQUEST['area_list'] != '')
				$Area = $_REQUEST['area_list'];
			else
			{
				echo '至少要有一个管辖范围！';
				exit();
			}
			// 设置用户信息
			$Result = SetUserInfo($conn, $_SESSION['UserID'], $UserID, $Name, $TEL, $UID, $Type, "基本信息-用户管理-修改用户信息");
			SetUserAreaList($conn, $_SESSION['UserID'], $UserID, $Area, "基本信息-用户管理-设置用户管辖范围");
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