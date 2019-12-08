<?php
	// 以json方式返回翻页列表与日志列表
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
	// 已登录，获取翻页列表与日志列表
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 不是超级管理员，强制注销
		if ($_SESSION['Type'] != '超级管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问楼盘列表');
			unset($_SESSION['Online']);
			exit();
		}
		// 是超级管理员
		else
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
			// 时间
			if (isset($_REQUEST['time']))
			{
				$Time = $_REQUEST['time'];
			}
			else
			{
				exit();
			}
			// IP地址
			if (isset($_REQUEST['ip']))
			{
				$IP = $_REQUEST['ip'];
			}
			else
			{
				exit();
			}
			// 操作者姓名
			if (isset($_REQUEST['opname']))
			{
				$OpName = $_REQUEST['opname'];
			}
			else
			{
				exit();
			}
			// 操作者身份证号码
			if (isset($_REQUEST['uid']))
			{
				$UID = $_REQUEST['uid'];
			}
			else
			{
				exit();
			}
			// 模块/单元名称
			if (isset($_REQUEST['modname']))
			{
				$ModName = $_REQUEST['modname'];
			}
			else
			{
				exit();
			}
			// 涉及表名称
			if (isset($_REQUEST['tblName']))
			{
				$tblName = $_REQUEST['tblName'];
			}
			else
			{
				exit();
			}
			// 行为
			if (isset($_REQUEST['action']))
			{
				$Action = $_REQUEST['action'];
			}
			else
			{
				exit();
			}
			$ret = [];
			// 获取楼盘数量
			$row_LogCount = GetLogCount($conn, $Time, $IP, $OpName, $UID, $ModName, $tblName, $Action);
			// 申请楼盘总数
			$LogCount = (int)($row_LogCount['@Result']);
			$ret['LogCount'] = $LogCount;
			// 总页数
			$PageNum = max(ceil($LogCount / 10), 1);
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
			// 获取日志列表
			$Res = GetLogList($conn, $Offset, $Time, $IP, $OpName, $UID, $ModName, $tblName, $Action);
			$ret['Res'] = "";
			for ($i = 0; $i < count($Res); $i++)
			{
				$ret['Res'] .= 
				"<tr id = '" . $Res[$i][0] . "'>
	                <td>" . $Res[$i][1] . "</td>
	                <td>" . $Res[$i][2] . "</td>
	                <td>" . $Res[$i][3] . "</td>
	                <td>" . $Res[$i][4] . "</td>
	                <td>" . $Res[$i][5] . "</td>
	                <td>" . $Res[$i][6] . "</td>
	                <td>" . $Res[$i][7] . "</td>
	                <td>" . ($Res[$i][8] === -1 ? '无效' : $Res[$i][8]) . "</td>
	            </tr>";
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
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>