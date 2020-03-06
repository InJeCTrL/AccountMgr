<?php
	// 删除给定ID对应的账目
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
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问账目清单-删除账目');
			unset($_SESSION['Online']);
			exit();
		}
		else
		{
			// 账目ID
			if (isset($_REQUEST['PID']) && $_REQUEST['PID'] != '')
			{
				$PID = $_REQUEST['PID'];
			}
			else
			{
				exit();
			}
			// 账目类型
			if (isset($_REQUEST['Type']) && $_REQUEST['Type'] != '')
			{
				$Type = $_REQUEST['Type'];
			}
			else
			{
				exit();
			}
			// 是否级联
			if (isset($_REQUEST['Cascade']) && $_REQUEST['Cascade'] != '')
			{
				$Cascade = $_REQUEST['Cascade'];
			}
			else
			{
				exit();
			}
			$Result = DeletePayment($conn, $_SESSION['UserID'], $PID, $Type, $Cascade);
			echo $Result['@Result'] === null ? '0' : $Result['@Result'];
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>