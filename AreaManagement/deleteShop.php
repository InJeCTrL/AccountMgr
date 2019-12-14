<?php
	// 删除给定ID对应的商铺
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
		// 商铺ID
		if (isset($_REQUEST['SID']) && $_REQUEST['SID'] != '')
		{
			$SID = $_REQUEST['SID'];
			$SID = (int)$SID;
		}
		else
		{
			exit();
		}
		// 标志当前用户合法查找的商铺
		$legal = (int)(IsLegalShop($conn, $_SESSION['UserID'], $SID)['@Result']);
		// 非法删除其它商铺
		if ($legal === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限删除其它商铺');
			unset($_SESSION['Online']);
			exit();
		}
		$Result = DeleteShop($conn, $_SESSION['UserID'], $SID);
		echo $Result['@Result'] === null ? '0' : $Result['@Result'];
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>