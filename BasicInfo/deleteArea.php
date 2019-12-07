<?php
	// 删除给定ID对应的管辖范围(楼盘)
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
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问楼盘列表-删除楼盘');
			unset($_SESSION['Online']);
			exit();
		}
		// 是超级管理员
		else
		{
			// 需删除的楼盘ID
			if (isset($_REQUEST['AID']) && $_REQUEST['AID'] != '')
				$AID = $_REQUEST['AID'];
			else
			{
				exit();
			}
			// 级联删除标志
			if (isset($_REQUEST['force']) && $_REQUEST['force'] != '')
				$force = $_REQUEST['force'];
			else
			{
				exit();
			}
			$Result = DeleteArea($conn, $_SESSION['UserID'], $AID, $force);
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