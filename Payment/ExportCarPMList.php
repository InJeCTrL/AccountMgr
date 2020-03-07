<?php
	// 导出车辆缴费月份列表到CSV
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
	// 已登录，获取车辆缴费月份列表
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 楼盘ID
		if (isset($_REQUEST['aid']))
		{
			$AID = $_REQUEST['aid'];
		}
		else
		{
			exit();
		}
		// 标志当前用户合法查找的楼盘
		if ($AID === '')
		{
			$legal_area = 1;
		}
		else
		{
			$legal_area = (int)(IsLegalArea($conn, $_SESSION['UserID'], $AID));
		}
		// 非法访问其它楼盘
		if ($legal_area === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它楼盘');
			unset($_SESSION['Online']);
			exit();
		}
		// 车牌号
		if (isset($_REQUEST['carcode']))
		{
			$CarCode = $_REQUEST['carcode'];
		}
		else
		{
			exit();
		}
		// 车主姓名
		if (isset($_REQUEST['name']))
		{
			$Name = $_REQUEST['name'];
		}
		else
		{
			exit();
		}
		// 电话号码
		if (isset($_REQUEST['tel']))
		{
			$TEL = $_REQUEST['tel'];
		}
		else
		{
			exit();
		}
		// 时间
		if (isset($_REQUEST['YearMonth']))
		{
			$YearMonth = explode(".", $_REQUEST['YearMonth']);
			$Year = $YearMonth[0];
			$Month = $YearMonth[1];
		}
		else
		{
			exit();
		}
		// 显示已缴费列表
		if (isset($_REQUEST['ShowPaid']))
		{
			$ShowPaid = $_REQUEST['ShowPaid'];
		}
		else
		{
			exit();
		}
		// 获取车辆缴费月份列表
		$Res = GetCarListByPaymentMonth($conn, $Offset, 10, $_SESSION['UserID'], $AID, $CarCode, $Name, $TEL, $Year, $Month, $ShowPaid);
		ob_clean();
		header("Content-Type: application/force-download");
		header("Content-type:text/csv;charset=gb2312");
		header("Content-Disposition:filename=车辆缴费月份.csv");
		echo iconv("utf-8", "gb2312", "\"时间\",\"楼盘名称\",\"车牌号\",\"车主姓名\",\"电话号码\"\r");
		ob_end_flush();
		for ($i = 0; $i < count($Res); $i++)
		{
			echo '"' . iconv("utf-8", "gb2312", str_replace('"', '""', $Res[$i][1])) . '",' . 
				'"' . iconv("utf-8", "gb2312", str_replace('"', '""', $Res[$i][2])) . '",' . 
				'"' . iconv("utf-8", "gb2312", str_replace('"', '""', $Res[$i][3])) . '",' . 
				'"' . iconv("utf-8", "gb2312", str_replace('"', '""', $Res[$i][4])) . '",' . 
				'"' . iconv("utf-8", "gb2312", str_replace('"', '""', $Res[$i][5])) . '"' . "\r";
			flush();
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>