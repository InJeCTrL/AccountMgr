<?php
	// 返回数据概览页面应显示的信息(json)
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
		if (isset($_REQUEST['AreaID']) && $_REQUEST['AreaID'] != '')
		{
			$AreaID = $_REQUEST['AreaID'];
			$AreaID = (int)$AreaID;
		}
		else
		{
			exit();
		}
		// 获取当前用户所属楼盘列表
		$UserArea = GetUserAreaList($conn, $_SESSION['UserID']);
		// 标志当前用户合法查找的楼盘
		$legal = 0;
		$AreaName = '';
		for ($i = 0; $i < count($UserArea); $i++)
		{
			// 找到查找的楼盘ID
			if ($AreaID === (int)($UserArea[$i][0]))
			{
				$legal = 1;
				$AreaName = $UserArea[$i][1];
			}
		}
		// 非法获取其它楼盘信息
		if ($legal === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它楼盘');
			unset($_SESSION['Online']);
			exit();
		}
		// 待返回数组
		$ret = [];
		// 当前查看的楼盘名称
		$ret['EachArea_Name'] = $AreaName;
		// 单个楼盘内三种缴费个体的数量列表
		$ret['EachArea_Rate'][0]['value'] = (int)(GetHouseHoldCount($conn, $_SESSION['UserID'], $AreaID, '', '', '', '', '')['@Result']);
		$ret['EachArea_Rate'][0]['name'] = '住户';
		$ret['EachArea_Rate'][1]['value'] = (int)(GetShopCount($conn, $_SESSION['UserID'], $AreaID, '', '', '')['@Result']);
		$ret['EachArea_Rate'][1]['name'] = '商铺';
		$ret['EachArea_Rate'][2]['value'] = (int)(GetCarCount($conn, $_SESSION['UserID'], $AreaID, '', '', '')['@Result']);
		$ret['EachArea_Rate'][2]['name'] = '车辆';
		
        echo json_encode($ret);
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>