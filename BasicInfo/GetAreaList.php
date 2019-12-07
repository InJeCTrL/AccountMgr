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
		// 不是超级管理员，强制注销
		if ($_SESSION['Type'] != '超级管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问管辖范围(楼盘)列表');
			unset($_SESSION['Online']);
			exit();
		}
		// 是超级管理员
		else
		{
			// 获取用户身份列表
			$Res = GetAreaList($conn);
			$ret = "";
			for ($i = 0; $i < count($Res); $i++)
			{
				$ret .= 
				"<option value = '" . $Res[$i][0] . "'>" . $Res[$i][1] . "</option>";
			}
            echo json_encode($ret);
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>