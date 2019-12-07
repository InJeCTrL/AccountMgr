<?php
	/* 新增用户账号 */
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
	// 已登录，获取用户身份列表
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 不是超级管理员，强制注销
		if ($_SESSION['Type'] != '超级管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问新增用户');
			unset($_SESSION['Online']);
			exit();
		}
		// 是超级管理员
		else
		{
			// 身份证号码
			if (isset($_REQUEST['uid']) && $_REQUEST['uid'] != '')
				$UID = $_REQUEST['uid'];
			else
			{
				echo '身份证号码为空！';
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
			// 姓名
			if (isset($_REQUEST['name']) && $_REQUEST['name'] != '')
				$Name = $_REQUEST['name'];
			else
			{
				echo '姓名为空！';
				exit();
			}
			// 身份
			if (isset($_REQUEST['type']) && $_REQUEST['type'] != '')
				$Type = $_REQUEST['type'];
			else
			{
				echo '身份为空！';
				exit();
			}
			// 密码1
			if (isset($_REQUEST['pwd1']) && $_REQUEST['pwd1'] != '')
				$pwd1 = $_REQUEST['pwd1'];
			else
			{
				echo '密码为空！';
				exit();
			}
			// 密码2
			if (isset($_REQUEST['pwd2']) && $_REQUEST['pwd2'] != '')
				$pwd2 = $_REQUEST['pwd2'];
			else
			{
				echo '确认密码为空！';
				exit();
			}
			// 验证两次输入密码一致
			if ($pwd1 != $pwd2)
			{
				echo '确认密码与密码不一致，请重新输入！';
				exit();
			}
			// 安全字符串
			if (isset($_REQUEST['sec']) && $_REQUEST['sec'] != '')
				$Sec = $_REQUEST['sec'];
			else
			{
				echo '安全字符串为空！';
				exit();
			}
			// 管辖范围
			if (isset($_REQUEST['area_list']) && $_REQUEST['area_list'] != '')
				$Area = $_REQUEST['area_list'];
			else
			{
				echo '至少要有一个管辖范围！';
				exit();
			}
			// 提交账号申请请求
			$Result = AddUser($conn, $_SESSION['UserID'], $UID, $TEL, $Name, $Type, $pwd1, $Sec, "基本信息-用户管理-新增用户");
			SetUserAreaList($conn, $_SESSION['UserID'], $Result['@ID'], $Area, "基本信息-用户管理-新增用户-设置用户管辖范围");
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