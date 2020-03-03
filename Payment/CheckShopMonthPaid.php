<?php
	// 以json方式返回商铺给定月份是否已缴费
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
	// 已登录，获取商铺给定月份是否已缴费
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 商铺ID
		if (isset($_REQUEST['SID']) && $_REQUEST['SID'] != '')
		{
			$SID = $_REQUEST['SID'];
		}
		else
		{
			exit();
		}
		// 年份
		if (isset($_REQUEST['Year']) && $_REQUEST['Year'] != '')
		{
			$Year = $_REQUEST['Year'];
		}
		else
		{
			exit();
		}
		// 月份
		if (isset($_REQUEST['Month']) && $_REQUEST['Month'] != '')
		{
			$Month = $_REQUEST['Month'];
		}
		else
		{
			exit();
		}
		// 标志当前用户合法查找的商铺
		$legal_shop = (int)(IsLegalShop($conn, $_SESSION['UserID'], $SID));
		// 非法访问未授权访问的商铺
		if ($legal_shop === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它商铺');
			unset($_SESSION['Online']);
			exit();
		}
		$ret = [];
		// 获取商铺指定月份是否已缴费
		$IsPaid = (int)(IsShopPaidMonth($conn, $SID, $Year, $Month)['@Result']);
		$ret['IsPaid'] = $IsPaid;
		
        echo json_encode($ret);
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>