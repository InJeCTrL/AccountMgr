<?php
	/* 设置申请注册账号的权限 */
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
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问审批注册');
			unset($_SESSION['Online']);
			exit();
		}
		// 是超级管理员
		else
		{
			// 用户ID
			if (isset($_REQUEST['userid']) && $_REQUEST['userid'] != '')
				$UserID = $_REQUEST['userid'];
			else
			{
				echo '用户不存在！';
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
			// 管辖范围
			if (isset($_REQUEST['area_list']) && $_REQUEST['area_list'] != '')
				$Area = $_REQUEST['area_list'];
			else
			{
				echo '至少要有一个管辖范围！';
				exit();
			}
			// 修改账号权限
			$UserInfo = GetUser($conn, $UserID);
			$Result = SetUserInfo($conn, $_SESSION['UserID'], $UserID, $UserInfo['@UserName'], $UserInfo['@TEL'], $UserInfo['@UID'], $Type, "基本信息-审核注册-修改用户权限");
			SetUserAreaList($conn, $_SESSION['UserID'], $UserID, $Area, "基本信息-审核注册-设置用户管辖范围");
			echo $Result['@Result'] === '修改成功！' ? '正式用户添加成功！' : $Result['@Result'];
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>