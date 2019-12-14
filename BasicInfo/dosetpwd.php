<?php
	/* 找回密码-重设密码, 并清除允许找回密码session */
	session_start();
	include_once('../conn/DBMgr.php');
	$conn = Connect();
	// 已登录, 注销现账号, 并跳转到登录页面
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-登录状态访问重设(找回)密码');
		unset($_SESSION['Online']);
		Header("HTTP/1.1 303 See Other"); 
		Header("Location: ../index.php");
	}
	else
	{
		// 密码1
		if (isset($_REQUEST['PWD1']) && $_REQUEST['PWD1'] != '')
			$pwd1 = $_REQUEST['PWD1'];
		else
		{
			echo '密码为空！';
			exit();
		}
		// 密码2
		if (isset($_REQUEST['PWD2']) && $_REQUEST['PWD2'] != '')
			$pwd2 = $_REQUEST['PWD2'];
		else
		{
			echo '确认密码为空！';
			exit();
		}
		if ($pwd1 != $pwd2)
		{
			echo '两次密码输入不一致，请重新输入！';
		}
		else
		{
			if (!isset($_SESSION['enResetPwd']))
				exit();
			// 待找回密码的账号ID
			$targetUserID = $_SESSION['enResetPwd'];
			// 禁止重复找回密码
			unset($_SESSION['enResetPwd']);
			// 重设密码
			SetUserPassword($conn, -1, $targetUserID, $pwd1, "找回密码-重设密码");
			// 重置登录状态
			SignOut($conn, $targetUserID, $targetUserID, '找回密码-重置登录状态');
			echo '修改成功！';
		}
	}
	DisConnect($conn);
?>