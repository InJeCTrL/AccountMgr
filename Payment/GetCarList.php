<?php
	// 以json方式返回车辆列表
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
	// 已登录，获取车辆列表
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 车牌号
		if (isset($_REQUEST['CarCode']) && $_REQUEST['CarCode'] != '')
		{
			$CarCode = $_REQUEST['CarCode'];
		}
		else
		{
			exit();
		}
		// 获取当前车辆列表
		$Res = GetCarList($conn, 0, 0, $_SESSION['UserID'], "", $CarCode, "", "");
		$ret['List'] = "";
		$ret['Num'] = count($Res);
		for ($i = 0; $i < $ret['Num']; $i++)
		{
			$ret['List'] .= 
			"<option value = '" . $Res[$i][0] . "'>" . $Res[$i][2] . "</option>";
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