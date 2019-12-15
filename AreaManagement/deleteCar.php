<?php
	// 删除给定ID对应的车辆
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
		// 车辆ID
		if (isset($_REQUEST['CID']) && $_REQUEST['CID'] != '')
		{
			$CID = $_REQUEST['CID'];
			$CID = (int)$CID;
		}
		else
		{
			exit();
		}
		// 标志当前用户合法查找的车辆
		$legal = (int)(IsLegalCar($conn, $_SESSION['UserID'], $CID)['@Result']);
		// 非法删除其它车辆
		if ($legal === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限删除其它车辆');
			unset($_SESSION['Online']);
			exit();
		}
		$Result = DeleteCar($conn, $_SESSION['UserID'], $CID);
		echo $Result['@Result'] === null ? '0' : $Result['@Result'];
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>