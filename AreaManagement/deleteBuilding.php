<?php
	// 删除给定ID对应的楼栋
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
		// 楼栋ID
		if (isset($_REQUEST['BID']) && $_REQUEST['BID'] != '')
		{
			$BID = $_REQUEST['BID'];
			$BID = (int)$BID;
		}
		else
		{
			exit();
		}
		// 标志当前用户合法查找的楼栋
		$legal = (int)(IsLegalBuilding($conn, $_SESSION['UserID'], $BID)['@Result']);
		// 非法删除其它楼盘
		if ($legal === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限删除其它楼盘');
			unset($_SESSION['Online']);
			exit();
		}
		$Result = DeleteBuilding($conn, $_SESSION['UserID'], $BID);
		echo $Result['@Result'] === null ? '0' : $Result['@Result'];
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>