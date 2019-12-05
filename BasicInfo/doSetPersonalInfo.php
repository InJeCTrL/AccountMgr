<?php
	/* 基本信息-个人信息修改 */
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
	// 已登录，执行修改
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 姓名
		if (isset($_REQUEST['name']) && $_REQUEST['name'] != '')
			$Name = $_REQUEST['name'];
		else
		{
			echo '姓名为空！';
			exit();
		}
		// 电话号码
		if (isset($_REQUEST['tel']) && $_REQUEST['tel'] != '')
			$TEL = $_REQUEST['tel'];
		else
		{
			echo '电话号码为空！';
			exit();
		}
		// 身份证号码
		if (isset($_REQUEST['uid']) && $_REQUEST['uid'] != '')
			$UID = $_REQUEST['uid'];
		else
		{
			echo '身份证号码为空！';
			exit();
		}
		// 设置个人信息
		$Result = SetPersonalInfo($conn, $_SESSION['UserID'], $_SESSION['UserID'], $Name, $TEL, $UID, "基本信息-个人信息修改");
		echo $Result['@Result'];
	}
	// 未登录，跳转到登录页面
	else
	{
		exit();
	}
	DisConnect($conn);
?>