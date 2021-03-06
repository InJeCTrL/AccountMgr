<?php
	/* 楼盘管辖-住户信息-修改住户信息 */
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
		// 不是管理员及以上，强制注销
		if ($_SESSION['Type'] != '超级管理员' && $_SESSION['Type'] != '管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问住户信息-查看/修改');
			unset($_SESSION['Online']);
			exit();
		}
		else
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
			$legal_household = (int)(IsLegalHouseHold($conn, $_SESSION['UserID'], $HID)['@Result']);
			// 非法获取其它住户信息
			if ($legal_household === 0)
			{
				SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限修改其它住户信息');
				unset($_SESSION['Online']);
				exit();
			}
			// 楼盘ID
			if (isset($_REQUEST['areaid']) && $_REQUEST['areaid'] != '')
				$AreaID = $_REQUEST['areaid'];
			else
			{
				echo '楼盘不存在！';
				exit();
			}
			// 标志当前用户对楼盘的访问是否合法
			$legal_area = (int)(IsLegalArea($conn, $_SESSION['UserID'], $AreaID)['@Result']);
			// 非法获取其它楼盘信息
			if ($legal_area === 0)
			{
				SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它楼盘信息');
				unset($_SESSION['Online']);
				exit();
			}
			// 楼栋ID
			if (isset($_REQUEST['bid']) && $_REQUEST['bid'] != '')
				$BID = $_REQUEST['bid'];
			else
			{
				echo '楼栋不存在！';
				exit();
			}
			// 标志当前用户对楼栋的访问是否合法
			$legal_building = (int)(IsLegalBuilding($conn, $_SESSION['UserID'], $BID)['@Result']);
			// 非法获取其它楼栋信息
			if ($legal_building === 0)
			{
				SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它楼栋信息');
				unset($_SESSION['Online']);
				exit();
			}
			// 门牌号
			if (isset($_REQUEST['roomcode']) && $_REQUEST['roomcode'] != '')
				$RoomCode = $_REQUEST['roomcode'];
			else
			{
				echo '门牌号为空！';
				exit();
			}
			// 住户姓名
			if (isset($_REQUEST['name']) && $_REQUEST['name'] != '')
				$Name = $_REQUEST['name'];
			else
			{
				$Name = 0;
			}
			// 电话号码
			if (isset($_REQUEST['tel']) && $_REQUEST['tel'] != '')
				$TEL = $_REQUEST['tel'];
			else
			{
				$TEL = '';
			}
			// 住房面积
			if (isset($_REQUEST['sq']) && $_REQUEST['sq'] != '')
				$square = $_REQUEST['sq'];
			else
			{
				$square = 0;
			}
			// 设置住户信息
			$Result = SetHouseHoldInfo($conn, $_SESSION['UserID'], $HID, $AreaID, $BID, $RoomCode, $Name, $TEL, $square, "楼盘管辖-住户信息-修改住户信息");
			echo $Result['@Result'];
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>