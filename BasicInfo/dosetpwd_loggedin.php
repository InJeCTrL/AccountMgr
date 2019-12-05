<?php
	/* 基本信息-修改密码 */
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
		// 密码1
		if (isset($_REQUEST['PWD1']) && $_REQUEST['PWD1'] != '')
			$pwd1 = $_REQUEST['PWD1'];
		else
		{
			echo '密码为空！';
			exit();
		}
		// 密码2
		if (isset($_REQUEST['PWD2']) && $_REQUEST['PWD2'] != '')
			$pwd2 = $_REQUEST['PWD2'];
		else
		{
			echo '确认密码为空！';
			exit();
		}
		if ($pwd1 != $pwd2)
		{
			echo '两次密码输入不一致，请重新输入！';
		}
		else
		{
			// 设置密码
			SetUserPassword($conn, $_SESSION['UserID'], $_SESSION['UserID'], $pwd1, "基本信息-修改密码");
			echo '修改成功！';
		}
	}
	// 未登录，跳转到登录页面
	else
	{
		exit();
	}
	DisConnect($conn);
?>