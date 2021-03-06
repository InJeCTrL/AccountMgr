<?php
	// 以json方式返回管辖范围(楼盘)列表
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
		// 获取当前用户所属楼盘列表
		$Res = GetUserAreaList($conn, $_SESSION['UserID']);
		$ret = "";
		for ($i = 0; $i < count($Res); $i++)
		{
			$ret .= 
			"<option value = '" . $Res[$i][0] . "'>" . $Res[$i][1] . "</option>";
		}
        echo json_encode($ret);
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>