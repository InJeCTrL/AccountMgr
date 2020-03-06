<?php
	// 以json方式返回翻页列表与账目清单列表
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
	// 已登录，获取翻页列表与账目清单列表
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 页码
		if (isset($_REQUEST['Page']) && $_REQUEST['Page'] != '')
		{
			$Page = $_REQUEST['Page'];
		}
		else
		{
			exit();
		}
		// 缴费年份
		if (isset($_REQUEST['year']))
		{
			$Year = $_REQUEST['year'];
		}
		else
		{
			exit();
		}
		// 缴费月份
		if (isset($_REQUEST['month']))
		{
			$Month = $_REQUEST['month'];
		}
		else
		{
			exit();
		}
		// 缴费日期
		if (isset($_REQUEST['day']))
		{
			$Day = $_REQUEST['day'];
		}
		else
		{
			exit();
		}
		// 缴费类型
		if (isset($_REQUEST['type']))
		{
			$Type = $_REQUEST['type'];
		}
		else
		{
			exit();
		}
		// 缴费人姓名
		if (isset($_REQUEST['name']))
		{
			$Name = $_REQUEST['name'];
		}
		else
		{
			exit();
		}
		// 缴费人电话号码
		if (isset($_REQUEST['tel']))
		{
			$Tel = $_REQUEST['tel'];
		}
		else
		{
			exit();
		}
		// 楼盘
		if (isset($_REQUEST['aid']))
		{
			$AID = $_REQUEST['aid'];
		}
		else
		{
			exit();
		}
		// 标志当前用户合法查找的楼盘
		if ($AID === '')
		{
			$legal_area = 1;
		}
		else
		{
			$legal_area = (int)(IsLegalArea($conn, $_SESSION['UserID'], $AID));
		}
		// 非法访问其它楼盘
		if ($legal_area === 0)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问其它楼盘');
			unset($_SESSION['Online']);
			exit();
		}
		// 缴费目标补偿
		if (isset($_REQUEST['addtarget']))
		{
			$AddTarget = $_REQUEST['addtarget'];
		}
		else
		{
			exit();
		}
		$ret = [];
		// 获取账目清单列表
		$row_AccountCount = GetAccountCount($conn, $_SESSION['UserID'], $Year, $Month, $Day, $Type, $Name, $Tel, $AID, $AddTarget);
		// 结果总数
		$AccountCount = (int)($row_AccountCount['@Result']);
		$ret['AccountCount'] = $AccountCount;
		// 总页数
		$PageNum = max(ceil($AccountCount / 10), 1);
		// 返回首页
		if ($Page === '«')
		{
			$Page = 1;
		}
		// 转到尾页
		else if ($Page === '»')
		{
			$Page = $PageNum;
		}
		else if ($Page <= 0)
		{
			$Page = 1;
		}
		else if ($Page > $PageNum)
		{
			$Page = $PageNum;
		}
		else
		{
			$Page = 1;
		}
		$Page = (int)$Page;
		// 页码为自然数
		$Offset = ($Page - 1) * 10;
		// 获取账目清单列表
		$Res = GetAccountList($conn, $Offset, 10, $_SESSION['UserID'], $Year, $Month, $Day, $Type, $Name, $Tel, $AID, $AddTarget);
		$ret['Res'] = "";
		for ($i = 0; $i < count($Res); $i++)
		{
			$ret['Res'][$i] = [$Res[$i][0], $Res[$i][1], $Res[$i][2], $Res[$i][3], $Res[$i][4], $Res[$i][5], $Res[$i][6]];
		}
		$ret['PageLimit'] = "
			<li><a href='#'>&laquo;</a>
	    	</li>";
		// 跳转到首页之后插入省略号
		if ($Page - 3 > 1)
		{
			$ret['PageLimit'] .= "
				<li><a href='#'>...</a>
	    		</li>";
		}
		for ($i = $Page - 3; $i <= $Page + 3; $i++)
		{
			// 页码在总页面范围
			if ($i >= 1 && $i <= $PageNum)
			{
				$ret['PageLimit'] .= "
					<li " . ($i === $Page ? "class='active'" : "") . "><a href='#'>" . $i . "</a>
		    		</li>";
			}
		}
		// 跳转到尾页之前插入省略号
		if ($i - 1 < $PageNum)
		{
			$ret['PageLimit'] .= "
				<li><a href='#'>...</a>
	    		</li>";
		}
	    $ret['PageLimit'] .= "
		    <li><a href='#'>&raquo;</a>
		    </li>";
        echo json_encode($ret);
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>