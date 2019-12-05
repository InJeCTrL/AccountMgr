<?php
	// 根据session返回当前已登录用户的个人信息(json)
	session_start();
	$arr_info = [];
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		$arr_info['UID'] = $_SESSION['UID'];
		$arr_info['TEL'] = $_SESSION['TEL'];
		$arr_info['Name'] = $_SESSION['UserName'];
		$arr_info['Type'] = $_SESSION['Type'];
		$str_json = json_encode($arr_info);
		echo $str_json;
	}
	else
	{
		Header("HTTP/1.1 303 See Other"); 
		Header("Location: ../index.php");
		exit();
	}
?>