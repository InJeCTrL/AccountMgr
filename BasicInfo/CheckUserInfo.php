<?php
	/* 验证账号信息, 设置允许重置密码的session */
	session_start();
	// 禁止重复找回密码
	unset($_SESSION['enResetPwd']);
	include_once('../conn/DBMgr.php');
	$conn = Connect();
	// 已登录, 注销现账号, 并跳转到登录页面
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-登录状态访问用户身份验证');
		unset($_SESSION['Online']);
		Header("HTTP/1.1 303 See Other"); 
		Header("Location: ../index.php");
	}
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
		// 安全字符串
		if (isset($_REQUEST['sec']) && $_REQUEST['sec'] != '')
			$Sec = $_REQUEST['sec'];
		else
		{
			echo '安全字符串为空！';
			exit();
		}
		// 验证码
		if (isset($_REQUEST['capt']))
			$captcha_input = $_REQUEST['capt'];
		else
			exit();
		// 验证码转大写
		$captcha_input = strtoupper($captcha_input);
		// 验证码错误
		if ($captcha_input != $_SESSION['captcha'])
		{
			echo "验证码错误！";
		}
		else
		{
			// 获取账号验证结果
			$Result = VerifyUserInfo($conn, $UID, $TEL, $Name, $Sec, "找回账号-验证用户身份");
			// 设置允许重置密码session
			$_SESSION['enResetPwd'] = $Result['@outID'];
			echo $Result['@Result'];
		}
	}
	DisConnect($conn);
?>