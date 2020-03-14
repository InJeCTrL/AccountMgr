<?php
	// 以json方式返回翻页列表与车辆缴费月份列表
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
	// 已登录，获取翻页列表与车辆缴费月份列表
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
		// 楼盘ID
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
		// 车牌号
		if (isset($_REQUEST['carcode']))
		{
			$CarCode = $_REQUEST['carcode'];
		}
		else
		{
			exit();
		}
		// 车主姓名
		if (isset($_REQUEST['name']))
		{
			$Name = $_REQUEST['name'];
		}
		else
		{
			exit();
		}
		// 电话号码
		if (isset($_REQUEST['tel']))
		{
			$TEL = $_REQUEST['tel'];
		}
		else
		{
			exit();
		}
		// 时间
		if (isset($_REQUEST['YearMonth']))
		{
			$YearMonth = explode(".", $_REQUEST['YearMonth']);
			$Year = $YearMonth[0];
			$Month = $YearMonth[1];
		}
		else
		{
			exit();
		}
		// 显示已缴费列表
		if (isset($_REQUEST['ShowPaid']))
		{
			$ShowPaid = $_REQUEST['ShowPaid'];
		}
		else
		{
			exit();
		}
		$ret = [];
		// 获取车辆缴费月份列表
		$row_CarCount = GetCarCountByPaymentMonth($conn, $_SESSION['UserID'], $AID, $CarCode, $Name, $TEL, $Year, $Month, $ShowPaid);
		// 结果总数
		$CarCount = (int)($row_CarCount['@Result']);
		$ret['CarCount'] = $CarCount;
		// 总页数
		$PageNum = max(ceil($CarCount / 10), 1);
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
		else if ($Page === '...')
		{
			$Page = 1;
		}
		$Page = (int)$Page;
		// 页码为自然数
		$Offset = ($Page - 1) * 10;
		// 获取车辆缴费月份列表
		$Res = GetCarListByPaymentMonth($conn, $Offset, 10, $_SESSION['UserID'], $AID, $CarCode, $Name, $TEL, $Year, $Month, $ShowPaid);
		$ret['Res'] = "";
		for ($i = 0; $i < count($Res); $i++)
		{
			$ret['Res'][$i] = [$Res[$i][0], $Res[$i][1], $Res[$i][2], $Res[$i][3], $Res[$i][4], $Res[$i][5]];
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