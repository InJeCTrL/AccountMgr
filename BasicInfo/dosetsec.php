<?php
	/* 基本信息-修改安全字符串 */
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
	// 已登录，执行修改
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 安全字符串
		if (isset($_REQUEST['SEC']) && $_REQUEST['SEC'] != '')
			$Sec = $_REQUEST['SEC'];
		else
		{
			echo '安全字符串为空！';
			exit();
		}
		// 设置密码
		SetUserSec($conn, $_SESSION['UserID'], $_SESSION['UserID'], $Sec, "基本信息-修改安全字符串");
		echo '修改成功！';
	}
	// 未登录，跳转到登录页面
	else
	{
		exit();
	}
	DisConnect($conn);
?>