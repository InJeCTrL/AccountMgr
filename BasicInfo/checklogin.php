<?php
	/* 验证账号密码, 设置登录状态(数据库、session) */
	session_start();
	// 禁止重复找回密码
	unset($_SESSION['enResetPwd']);
	include_once('../conn/DBMgr.php');
	$conn = Connect();
	// 已登录, 注销现账号, 并跳转到登录页面
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-登录状态访问登录验证');
		unset($_SESSION['Online']);
		Header("HTTP/1.1 303 See Other"); 
		Header("Location: ../index.php");
	}
	else
	{
		// 身份证号码或手机号码
		if (isset($_REQUEST['uidtel']) && $_REQUEST['uidtel'] != '')
			$UIDTEL = $_REQUEST['uidtel'];
		else
		{
			echo '身份证号码或电话号码为空！';
			exit();
		}
		// 密码
		if (isset($_REQUEST['pwd']) && $_REQUEST['pwd'] != '')
			$Password = $_REQUEST['pwd'];
		else
		{
			echo '密码为空！';
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
			// 获取账号验证结果并登录, 返回用户ID
			$Result = VerifyUser($conn, $UIDTEL, $Password, "登录");
			// 设置session
			$_SESSION['UserID'] = $Result['@outID'];
			// 根据用户ID获取用户信息
			$UserInfo = GetUser($conn, $Result['@outID']);
			$_SESSION['UID'] = $UserInfo['@UID'];
			$_SESSION['TEL'] = $UserInfo['@TEL'];
			$_SESSION['UserName'] = $UserInfo['@UserName'];
			$_SESSION['Type'] = $UserInfo['@strType'];
			$_SESSION['Online'] = $UserInfo['@Online'];
			echo $Result['@Result'];
		}
	}
	DisConnect($conn);
?>