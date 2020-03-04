<?php
	// 以json方式返回缴费月份列表
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
		// 获取当前用户所属缴费月份列表
		$ret = array();
		for ($target = 0; $target < 3; $target++)
		{
			$Res = GetYearMonth($conn, $_SESSION['UserID'], $target);
			$ret[$target] = "";
			for ($i = 0; $i < count($Res); $i++)
			{
				$ret[$target] .= 
				"<option value = '" . $Res[$i][0] . "." . $Res[$i][1] . "'>" . $Res[$i][0] . "年" . $Res[$i][1] . "月</option>";
			}
			if (count($Res) === 0)
			{
				$ret[$target] = "<option value='-1.-1'>无相关收费数据</option>";
			}
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