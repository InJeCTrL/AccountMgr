<?php
	/* 楼盘管辖-楼栋列表-修改楼栋信息 */
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
		// 不是管理员及以上，强制注销
		if ($_SESSION['Type'] != '超级管理员' && $_SESSION['Type'] != '管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问楼栋列表-查看/修改');
			unset($_SESSION['Online']);
			exit();
		}
		else
		{
			// 楼栋ID
			if (isset($_REQUEST['BID']) && $_REQUEST['BID'] != '')
			{
				$BID = $_REQUEST['BID'];
				$BID = (int)$BID;
			}
			else
			{
				exit();
			}
			// 标志当前用户对楼栋的访问是否合法
			$legal = (int)(IsLegalBuilding($conn, $_SESSION['UserID'], $BID)['@Result']);
			// 非法获取其它楼盘信息
			if ($legal === 0)
			{
				SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它楼栋信息');
				unset($_SESSION['Online']);
				exit();
			}
			// 楼盘ID
			if (isset($_REQUEST['areaid']) && $_REQUEST['areaid'] != '')
				$AreaID = $_REQUEST['areaid'];
			else
			{
				echo '楼盘不存在！';
				exit();
			}
			// 楼栋号
			if (isset($_REQUEST['bno']) && $_REQUEST['bno'] != '')
				$BNo = $_REQUEST['bno'];
			else
			{
				echo '楼栋号为空！';
				exit();
			}
			// 物业费单价
			if (isset($_REQUEST['pmcu']) && $_REQUEST['pmcu'] != '')
				$PMCU = $_REQUEST['pmcu'];
			else
			{
				$PMCU = 0;
			}
			// 公摊费
			if (isset($_REQUEST['prsf']) && $_REQUEST['prsf'] != '')
				$PRSF = $_REQUEST['prsf'];
			else
			{
				$PRSF = 0;
			}
			// 垃圾清运费
			if (isset($_REQUEST['tf']) && $_REQUEST['tf'] != '')
				$TF = $_REQUEST['tf'];
			else
			{
				$TF = 0;
			}
			// 设置楼栋信息
			$Result = SetBuildingInfo($conn, $_SESSION['UserID'], $BID, $AreaID, $BNo, $PMCU, $PRSF, $TF, "楼盘管辖-楼栋列表-修改楼栋信息");
			echo $Result['@Result'];
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>