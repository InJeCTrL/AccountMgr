<?php
	// 以json方式返回住户预计缴费金额
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
	// 已登录，获取住户预计缴费金额
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 住户ID
		if (isset($_REQUEST['HID']) && $_REQUEST['HID'] != '')
		{
			$HID = $_REQUEST['HID'];
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
		// 标志当前用户合法查找的住户
		$legal_household = (int)(IsLegalHouseHold($conn, $_SESSION['UserID'], $HID));
		// 非法访问未授权访问的住户
		if ($legal_household === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它住户');
			unset($_SESSION['Online']);
			exit();
		}
		$ret = [];
		// 获取住户预计缴费金额
		$Fee = GetHouseHoldPay($conn, $HID, $Count);
		// 获取住户预计物业费金额
		$PMC = (double)($Fee['@PMC']);
		$ret['PMC'] = $PMC;
		// 获取住户预计公摊费金额
		$PRSF = (double)($Fee['@PRSF']);
		$ret['PRSF'] = $PRSF;
		// 获取住户预计垃圾清运费金额
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