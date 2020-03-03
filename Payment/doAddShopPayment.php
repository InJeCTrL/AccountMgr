<?php
	/* 新增商铺缴费账目 */
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
		// 不是管理员及以上权限，强制注销
		if ($_SESSION['Type'] != '超级管理员' && $_SESSION['Type'] != '管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问新增商铺缴费账目');
			unset($_SESSION['Online']);
			exit();
		}
		else
		{
			// 商铺ID
			if (isset($_REQUEST['SID']) && $_REQUEST['SID'] != '')
				$SID = $_REQUEST['SID'];
			else
			{
				echo '商铺为空！';
				exit();
			}
			// 本次缴费人姓名
			if (isset($_REQUEST['name']) && $_REQUEST['name'] != '')
				$Name = $_REQUEST['name'];
			else
			{
				echo '本次缴费人姓名为空！';
				exit();
			}
			// 本次缴费人电话号码
			if (isset($_REQUEST['tel']) && $_REQUEST['tel'] != '')
				$TEL = $_REQUEST['tel'];
			else
			{
				echo '本次缴费人电话号码为空！';
				exit();
			}
			// 实收物业费
			if (isset($_REQUEST['pmc']) && $_REQUEST['pmc'] != '')
				$PMC = $_REQUEST['pmc'];
			else
			{
				echo '实收物业费为空！';
				exit();
			}
			// 实收电费
			if (isset($_REQUEST['ele']) && $_REQUEST['ele'] != '')
				$ELE = $_REQUEST['ele'];
			else
			{
				echo '实收电费为空！';
				exit();
			}
			// 实收垃圾清运费
			if (isset($_REQUEST['tf']) && $_REQUEST['tf'] != '')
				$TF = $_REQUEST['tf'];
			else
			{
				echo '实收垃圾清运费为空！';
				exit();
			}
			// 商铺缴费月份列表
			if (isset($_REQUEST['Month']))
				$Month = $_REQUEST['Month'];
			else
			{
				echo '商铺缴费月份列表传递失败！';
				exit();
			}
			// 是否同时缴车费
			if (isset($_REQUEST['CFlag']) && $_REQUEST['CFlag'] != '')
				$CFlag = $_REQUEST['CFlag'];
			else
			{
				echo '车费标志为空！';
				exit();
			}
			// 实收车费
			if (isset($_REQUEST['CFee']) && $_REQUEST['CFee'] != '')
				$CFee = $_REQUEST['CFee'];
			else
			{
				echo '实收车费为空！';
				exit();
			}
			// 车费月份列表
			if (isset($_REQUEST['CarMonth']))
				$CarMonth = $_REQUEST['CarMonth'];
			else
			{
				echo '车费月份列表传递失败！';
				exit();
			}
			// 票据时间
			if (isset($_REQUEST['Time']) && $_REQUEST['Time'] != '')
				$TicketTime = $_REQUEST['Time'];
			else
			{
				echo '票据时间为空！';
				exit();
			}
			// 车辆ID
			if (isset($_REQUEST['CID']) && $_REQUEST['CID'] != '')
				$CID = $_REQUEST['CID'];
			else
			{
				echo '车辆ID为空！';
				exit();
			}
			// 车牌号
			if (isset($_REQUEST['CarCode']) && $_REQUEST['CarCode'] != '')
				$CarCode = $_REQUEST['CarCode'];
			else
			{
				echo '车牌号为空！';
				exit();
			}
			// 标志当前用户对商铺的访问是否合法
			$legal_shop = (int)(IsLegalShop($conn, $_SESSION['UserID'], $SID)['@Result']);
			// 非法获取其它商铺信息
			if ($legal_shop === 0)
			{
				SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限添加其它商铺缴费记录');
				unset($_SESSION['Online']);
				exit();
			}
			$retstr = "";
			$CPId = null;
			// 先记录车费缴费
			if ($CFlag === 'true')
			{
				if ($CID != '-1')
				{
					// 标志当前用户对车辆的访问是否合法
					$legal_car = (int)(IsLegalCar($conn, $_SESSION['UserID'], $CID)['@Result']);
					// 非法获取其它车辆信息
					if ($legal_car === 0)
					{
						SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限添加其它车辆缴费记录');
						unset($_SESSION['Online']);
						exit();
					}
				}
				// 新增住户车辆缴费
				$infoCarPayment = AddShopCarPayment($conn, $_SESSION['UserID'], $SID, $CarMonth, $Name, $TEL, $TicketTime, $CID, $CarCode, $CFee, "收费与账目-票据录入-商铺对应车辆缴费");
				$retstr .= '商铺车辆缴费' . $infoCarPayment['@Result'] . "\n";
				$CPId = $infoCarPayment['@ID'];
			}
			// 新增商铺缴费
			$retstr .= '商铺缴费' . AddShopPayment($conn, $_SESSION['UserID'], $SID, $Month, $Name, $TEL, $PMC, $ELE, $TF, $CPId, $TicketTime, "收费与账目-票据录入-商铺缴费")['@Result'];
			
			echo $retstr;
		}
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>