<?php
	/* 基本信息-用户管理-修改用户安全字符串 */
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
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问用户管理-修改用户密码');
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
			// 安全字符串
			if (isset($_REQUEST['SEC']) && $_REQUEST['SEC'] != '')
				$Sec = $_REQUEST['SEC'];
			else
			{
				echo '安全字符串为空！';
				exit();
			}
			// 设置密码
			SetUserSec($conn, $_SESSION['UserID'], $UserID, $Sec, "基本信息-用户管理-修改用户安全字符串 ");
			echo '修改成功！';
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>