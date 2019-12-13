<?php
	// 根据传入的HouseHoldID返回住户的详细信息(json)
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
		// 标志当前用户对住户的访问是否合法
		$legal = (int)(IsLegalHouseHold($conn, $_SESSION['UserID'], $HID)['@Result']);
		// 非法获取其它住户信息
		if ($legal === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它住户信息');
			unset($_SESSION['Online']);
			exit();
		}
		// 待返回数组
		$ret = [];
		// 获取住户信息
		$HouseHoldInfo = GetHouseHold($conn, $HID);
		$AreaID = $HouseHoldInfo['@AreaID'];
		$BID = $HouseHoldInfo['@BID'];
		$ret['RoomCode'] = $HouseHoldInfo['@RoomCode'];
		$ret['Name'] = $HouseHoldInfo['@Name'];
		$ret['TEL'] = $HouseHoldInfo['@TEL'];
		$ret['square'] = $HouseHoldInfo['@square'];
		// 获取楼盘列表
		$Res = GetUserAreaList($conn, $_SESSION['UserID']);
		$ret['AreaID'] = "";
		for ($i = 0; $i < count($Res); $i++)
		{
			$ret['AreaID'] .= 
			"<option " . ($Res[$i][0] === (int)($AreaID) ? "selected='selected'" : "") . " value = '" . $Res[$i][0] . "'>" . $Res[$i][1] . "</option>";
		}
		// 获取归属楼盘下属楼栋列表
		$Res = GetBuildingList($conn, 0, 0, $AreaID, '');
		$ret['BID'] = "";
		for ($i = 0; $i < count($Res); $i++)
		{
			$ret['BID'] .= 
			"<option " . ($Res[$i][0] === (int)($BID) ? "selected='selected'" : "") . " value = '" . $Res[$i][0] . "'>" . $Res[$i][2] . "</option>";
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