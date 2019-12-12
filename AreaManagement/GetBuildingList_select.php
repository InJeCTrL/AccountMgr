<?php
	// 以json方式返回楼栋列表
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
		// 楼盘ID
		if (isset($_REQUEST['areaid']) && $_REQUEST['areaid'] != '')
		{
			$AreaID = $_REQUEST['areaid'];
			$AreaID = (int)$AreaID;
		}
		else
		{
			exit();
		}
		// 标志当前用户合法查找的楼盘
		$legal = (int)(IsLegalArea($conn, $_SESSION['UserID'], $AreaID));
		// 非法查找其它楼盘的楼栋
		if ($legal === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它楼盘');
			unset($_SESSION['Online']);
			exit();
		}
		// 获取当前楼盘下属楼栋列表
		$Res = GetBuildingList($conn, 0, 0, $AreaID, '');
		$ret = "";
		for ($i = 0; $i < count($Res); $i++)
		{
			$ret .= 
			"<option value = '" . $Res[$i][0] . "'>" . $Res[$i][2] . "</option>";
		}
        echo json_encode($ret);
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>