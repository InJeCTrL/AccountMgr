<?php
	// 导出账目清单列表到CSV
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
	// 已登录，获取账目清单列表
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 缴费年份
		if (isset($_REQUEST['year']))
		{
			$Year = $_REQUEST['year'];
		}
		else
		{
			exit();
		}
		// 缴费月份
		if (isset($_REQUEST['month']))
		{
			$Month = $_REQUEST['month'];
		}
		else
		{
			exit();
		}
		// 缴费日期
		if (isset($_REQUEST['day']))
		{
			$Day = $_REQUEST['day'];
		}
		else
		{
			exit();
		}
		// 缴费类型
		if (isset($_REQUEST['type']))
		{
			$Type = $_REQUEST['type'];
		}
		else
		{
			exit();
		}
		// 缴费人姓名
		if (isset($_REQUEST['name']))
		{
			$Name = $_REQUEST['name'];
		}
		else
		{
			exit();
		}
		// 缴费人电话号码
		if (isset($_REQUEST['tel']))
		{
			$Tel = $_REQUEST['tel'];
		}
		else
		{
			exit();
		}
		// 楼盘
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
		// 缴费目标补偿
		if (isset($_REQUEST['addtarget']))
		{
			$AddTarget = $_REQUEST['addtarget'];
		}
		else
		{
			exit();
		}
		$ret = [];
		// 获取账目清单列表
		$Res = GetAccountList($conn, 0, 0, $_SESSION['UserID'], $Year, $Month, $Day, $Type, $Name, $Tel, $AID, $AddTarget);
		ob_clean();
		header("Content-Type: application/force-download");
		header("Content-type:text/csv;charset=gb2312");
		header("Content-Disposition:filename=账目清单.csv");
		echo iconv("utf-8", "gb2312", "\"缴费时间\",\"缴费类型\",\"缴费人姓名\",\"缴费人电话号码\",\"缴费目标\",\"缴费内容\"\r");
		ob_end_flush();
		for ($i = 0; $i < count($Res); $i++)
		{
			
			switch ($Res[$i][2])
			{
				case '0':case 0:
					$Type = '住户';
					break;
				case '1':case 1:
					$Type = '住户-车辆混合';
					break;
				case '2':case 2:
					$Type = '商铺';
					break;
				case '3':case 3:
					$Type = '商铺-车辆混合';
					break;
				case '4':case 4:
					$Type = '单独收费车辆';
					break;
			}
			echo '"' . iconv("utf-8", "gb2312", str_replace('"', '""', $Res[$i][1])) . '",' . 
				'"' . iconv("utf-8", "gb2312", str_replace('"', '""', $Type)) . '",' . 
				'"' . iconv("utf-8", "gb2312", str_replace('"', '""', $Res[$i][3])) . '",' . 
				'"' . iconv("utf-8", "gb2312", str_replace('"', '""', $Res[$i][4])) . '",' . 
				'"' . iconv("utf-8", "gb2312", str_replace('"', '""', $Res[$i][5])) . '",' . 
				'"' . iconv("utf-8", "gb2312", str_replace('"', '""', $Res[$i][6])) . '"' . "\r";
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