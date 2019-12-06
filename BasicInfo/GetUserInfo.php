<?php
	// 根据传入的UserID返回用户的个人信息(json)
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
	// 已登录，获取用户个人信息
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 不是超级管理员，强制注销
		if ($_SESSION['Type'] != '超级管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问用户管理-查看/修改');
			unset($_SESSION['Online']);
			exit();
		}
		// 是超级管理员
		else
		{
			// 用户ID
			if (isset($_REQUEST['userid']) && $_REQUEST['userid'] != '')
			{
				$UserID = $_REQUEST['userid'];
				$UserID = (int)$UserID;
			}
			else
			{
				exit();
			}
			// 待返回数组
			$ret = [];
			// 获取用户信息
			$UserInfo = GetUser($conn, $UserID);
			$ret['Name'] = $UserInfo['@UserName'];
			$ret['TEL'] = $UserInfo['@TEL'];
			$ret['UID'] = $UserInfo['@UID'];
			// 获取用户身份列表
			$Res = GetUserTypeList($conn);
			$ret['Type'] = "";
			for ($i = 0; $i < count($Res); $i++)
			{
				$ret['Type'] .= 
				"<option " . ($Res[$i][0] === (int)($UserInfo['@intType']) ? "selected='selected'" : "") . " value = '" . $Res[$i][0] . "'>" . $Res[$i][1] . "</option>";
			}
			// 获取用户负责的管辖范围
			$UserArea = GetUserAreaList($conn, $UserID);
			$ret['Area_sel'] = "";
			for ($i = 0; $i < count($UserArea); $i++)
			{
				$ret['Area_sel'] .=
				"<option value = '" . $UserArea[$i][0] . "'>" . $UserArea[$i][1] . "</option>";
			}
			// 获取所有管辖范围
			$Area = GetAreaList($conn);
			$ret['Area_not'] = "";
			for ($i_Area = 0; $i_Area < count($Area); $i_Area++)
			{
				$addlist = 1;
				for ($i_UserArea = 0; $i_UserArea < count($UserArea); $i_UserArea++)
				{
					// 找到相同, 不加入
					if ($Area[$i_Area][0] === $i_UserArea[$i_Area][0])
					{
						$addlist = 0;
						break;
					}
				}
				if ($addlist === 1)
				{
					$ret['Area_not'] .=
					"<option value = '" . $Area[$i_Area][0] . "'>" . $Area[$i_Area][1] . "</option>";
				}
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