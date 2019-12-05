<?php
	/* 提交申请账号请求 */
	session_start();
	include_once('../conn/DBMgr.php');
	$conn = Connect();
	// 已登录, 注销现账号, 并跳转到登录页面
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-登录状态访问申请账号');
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
			// 提交账号申请请求
			$Result = SubmitUserApply($conn, -1, $UID, $TEL, $Name, $pwd1, $Sec, "申请账号");
			echo $Result['@Result'];
		}
	}
	DisConnect($conn);
?>