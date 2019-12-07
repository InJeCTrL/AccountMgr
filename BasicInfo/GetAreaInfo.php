<?php
	// 根据传入的AreaID返回楼盘的信息(json)
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
		// 不是超级管理员，强制注销
		if ($_SESSION['Type'] != '超级管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问楼盘列表-查看/修改');
			unset($_SESSION['Online']);
			exit();
		}
		// 是超级管理员
		else
		{
			// 楼盘ID
			if (isset($_REQUEST['aid']) && $_REQUEST['aid'] != '')
			{
				$AreaID = $_REQUEST['aid'];
				$AreaID = (int)$AreaID;
			}
			else
			{
				exit();
			}
			// 待返回数组
			$ret = [];
			// 获取楼盘信息
			$AreaInfo = GetArea($conn, $AreaID);
			$ret['Name'] = $AreaInfo['@AreaName'];
            echo json_encode($ret);
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>