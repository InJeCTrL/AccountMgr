<?php
	/* 楼盘管辖-商铺信息-修改商铺信息 */
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
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问商铺信息-查看/修改');
			unset($_SESSION['Online']);
			exit();
		}
		else
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
			$legal_shop = (int)(IsLegalShop($conn, $_SESSION['UserID'], $SID)['@Result']);
			// 非法获取其它商铺信息
			if ($legal_shop === 0)
			{
				SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限修改其它商铺信息');
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
			// 标志当前用户对楼盘的访问是否合法
			$legal_area = (int)(IsLegalArea($conn, $_SESSION['UserID'], $AreaID)['@Result']);
			// 非法获取其它楼盘信息
			if ($legal_area === 0)
			{
				SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它楼盘信息');
				unset($_SESSION['Online']);
				exit();
			}
			// 商铺名称
			if (isset($_REQUEST['shopname']) && $_REQUEST['shopname'] != '')
				$ShopName = $_REQUEST['shopname'];
			else
			{
				echo '商铺名称为空！';
				exit();
			}
			// 住户姓名
			if (isset($_REQUEST['name']) && $_REQUEST['name'] != '')
				$Name = $_REQUEST['name'];
			else
			{
				$Name = 0;
			}
			// 电话号码
			if (isset($_REQUEST['tel']) && $_REQUEST['tel'] != '')
				$TEL = $_REQUEST['tel'];
			else
			{
				$TEL = '';
			}
			// 预设物业费单价
			if (isset($_REQUEST['pmcu']) && $_REQUEST['pmcu'] != '')
				$PMCU = $_REQUEST['pmcu'];
			else
			{
				$PMCU = 0;
			}
			// 预设电费单价
			if (isset($_REQUEST['elu']) && $_REQUEST['elu'] != '')
				$ELU = $_REQUEST['elu'];
			else
			{
				$ELU = 0;
			}
			// 预设垃圾清运费
			if (isset($_REQUEST['tf']) && $_REQUEST['tf'] != '')
				$TF = $_REQUEST['tf'];
			else
			{
				$TF = 0;
			}
			// 设置商铺信息
			$Result = SetShopInfo($conn, $_SESSION['UserID'], $SID, $AreaID, $ShopName, $Name, $TEL, $PMCU, $ELU, $TF, "楼盘管辖-商铺信息-修改商铺信息");
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