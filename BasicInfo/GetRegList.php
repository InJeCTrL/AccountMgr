<?php
	// 以json方式返回翻页列表与审核注册列表
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
	// 已登录，获取翻页列表与审核注册列表
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 不是超级管理员，强制注销
		if ($_SESSION['Type'] != '超级管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问审核注册');
			unset($_SESSION['Online']);
			exit();
		}
		// 是超级管理员
		else
		{
			// 页码
			if (isset($_REQUEST['Page']) && $_REQUEST['Page'] != '' && $_REQUEST['Page'] != '...')
			{
				$Page = $_REQUEST['Page'];
				$Page = (int)$Page;
			}
			else
			{
				exit();
			}
			// 身份证号码
			if (isset($_REQUEST['UID']))
			{
				$UID = $_REQUEST['UID'];
			}
			else
			{
				exit();
			}
			// 电话号码
			if (isset($_REQUEST['TEL']))
			{
				$TEL = $_REQUEST['TEL'];
			}
			else
			{
				exit();
			}
			// 姓名
			if (isset($_REQUEST['Name']))
			{
				$Name = $_REQUEST['Name'];
			}
			else
			{
				exit();
			}
			$ret = [];
			// 获取正式用户数量
			$row_UserCount = GetRegCount($conn, $UID, $TEL, $Name);
			// 申请注册用户总数
			$UserCount = (int)($row_UserCount['@Result']);
			$ret['UserCount'] = $UserCount;
			// 总页数
			$PageNum = max(ceil($UserCount / 10), 1);
			// 页码为返回首页
			if ($Page === '«' || $Page <= 0)
			{
				$Page = 1;
				$Offset = 0;
			}
			// 页码为转到尾页
			else if ($Page === '»' || $Page > $PageNum)
			{
				$Page = $PageNum;
			}
			// 页码为自然数
			$Offset = ($Page - 1) * 10;
			// 获取待审核注册用户列表
			$Res = GetRegList($conn, $Offset, $UID, $TEL, $Name);
			$ret['Res'] = "";
			for ($i = 0; $i < count($Res); $i++)
			{
				$ret['Res'] .= 
				"<tr>
					<td><input type='checkbox' class='chksel' /></td>
	                <td>" . $Res[$i][3] . "</td>
	                <td>" . $Res[$i][2] . "</td>
	                <td>" . $Res[$i][1] . "</td>
	                <td>
	                    <div id=" . $Res[$i][0] . " class='btn-group'>
	                        <a href='#' class='btn btn-primary pass'>批准</a>
	                        <a href='#' class='btn btn-danger deny'>拒绝</a>
	                    </div>
	                </td>
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
						<li " . ($i === $Page ? "class='active'" : "") . "><a href='#'>" . $PageNum . "</a>
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