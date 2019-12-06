<?php
	/* 基本信息-用户管理-修改用户密码 */
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
				SetUserPassword($conn, $_SESSION['UserID'], $UserID, $pwd1, "基本信息-用户管理-修改用户密码");
				echo '修改成功！';
			}
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>