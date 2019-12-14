<?php
	// 根据传入的ShopID返回商铺的详细信息(json)
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
	// 已登录
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 商铺ID
		if (isset($_REQUEST['SID']) && $_REQUEST['SID'] != '')
		{
			$SID = $_REQUEST['SID'];
			$SID = (int)$SID;
		}
		else
		{
			exit();
		}
		// 标志当前用户对商铺的访问是否合法
		$legal = (int)(IsLegalShop($conn, $_SESSION['UserID'], $SID)['@Result']);
		// 非法获取其它商铺信息
		if ($legal === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它商铺信息');
			unset($_SESSION['Online']);
			exit();
		}
		// 待返回数组
		$ret = [];
		// 获取商铺信息
		$ShopInfo = GetShop($conn, $SID);
		$AreaID = $ShopInfo['@AreaID'];
		$ret['ShopName'] = $ShopInfo['@ShopName'];
		$ret['Name'] = $ShopInfo['@Name'];
		$ret['TEL'] = $ShopInfo['@TEL'];
		$ret['PMCU'] = $ShopInfo['@PMCU'];
		$ret['ELU'] = $ShopInfo['@ELU'];
		$ret['TF'] = $ShopInfo['@TF'];
		// 获取楼盘列表
		$Res = GetUserAreaList($conn, $_SESSION['UserID']);
		$ret['AreaID'] = "";
		for ($i = 0; $i < count($Res); $i++)
		{
			$ret['AreaID'] .= 
			"<option " . ($Res[$i][0] === (int)($AreaID) ? "selected='selected'" : "") . " value = '" . $Res[$i][0] . "'>" . $Res[$i][1] . "</option>";
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