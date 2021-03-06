<?php
	// 删除给定ID对应的用户账号
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
	// 已登录，获取用户列表与翻页列表
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 不是超级管理员，强制注销
		if ($_SESSION['Type'] != '超级管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问用户管理-强制离线');
			unset($_SESSION['Online']);
			exit();
		}
		// 是超级管理员
		else
		{
			// 需删除的用户ID
			if (isset($_REQUEST['UserID']) && $_REQUEST['UserID'] != '')
				$UserID = $_REQUEST['UserID'];
			else
			{
				exit();
			}
			$Result = DeleteUser($conn, $_SESSION['UserID'], $UserID);
			echo $Result['@Result'] === null ? '0' : $Result['@Result'];
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>