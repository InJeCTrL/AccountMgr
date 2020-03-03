<?php
	// 以json方式返回商铺预计缴费金额
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
	// 已登录，获取商铺预计缴费金额
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
		// 月份数量
		if (isset($_REQUEST['Count']) && $_REQUEST['Count'] != '')
		{
			$Count = $_REQUEST['Count'];
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
		// 获取商铺预计缴费金额
		$Fee = GetShopPay($conn, $SID, $Count);
		// 获取商铺预计物业费金额
		$PMC = (double)($Fee['@PMC']);
		$ret['PMC'] = $PMC;
		// 获取商铺预计电费金额
		$ELE = (double)($Fee['@ELE']);
		$ret['ELE'] = $ELE;
		// 获取商铺预计垃圾清运费金额
		$TF = (double)($Fee['@TF']);
		$ret['TF'] = $TF;
		
        echo json_encode($ret);
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>