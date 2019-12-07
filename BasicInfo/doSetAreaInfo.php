<?php
	/* 基本信息-楼盘列表-修改楼盘信息 */
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
				$AreaID = $_REQUEST['aid'];
			else
			{
				echo '楼盘不存在！';
				exit();
			}
			// 楼盘名称
			if (isset($_REQUEST['name']) && $_REQUEST['name'] != '')
				$Name = $_REQUEST['name'];
			else
			{
				echo '楼盘名称为空！';
				exit();
			}
			// 设置楼盘信息
			$Result = SetAreaInfo($conn, $_SESSION['UserID'], $AreaID, $Name, "基本信息-楼盘列表-修改楼盘信息");
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