<?php
	// 删除给定ID对应的住户
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
		// 住户ID
		if (isset($_REQUEST['HID']) && $_REQUEST['HID'] != '')
		{
			$HID = $_REQUEST['HID'];
			$HID = (int)$HID;
		}
		else
		{
			exit();
		}
		// 标志当前用户合法查找的住户
		$legal = (int)(IsLegalHouseHold($conn, $_SESSION['UserID'], $HID)['@Result']);
		// 非法删除其它住户
		if ($legal === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限删除其它住户');
			unset($_SESSION['Online']);
			exit();
		}
		$Result = DeleteHouseHold($conn, $_SESSION['UserID'], $HID);
		echo $Result['@Result'] === null ? '0' : $Result['@Result'];
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>