<?php
	// 以json方式返回翻页列表与楼栋列表
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
	// 已登录，获取翻页列表与楼栋列表
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
		// 楼栋号
		if (isset($_REQUEST['bno']))
		{
			$BNo = $_REQUEST['bno'];
		}
		else
		{
			exit();
		}
		$ret = [];
		// 获取楼栋数量
		$row_BuildingCount = GetBuildingCount($conn, $AID, $BNo);
		// 楼栋总数
		$BuildingCount = (int)($row_BuildingCount['@Result']);
		$ret['BuildingCount'] = $BuildingCount;
		// 总页数
		$PageNum = max(ceil($BuildingCount / 10), 1);
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
		// 获取楼栋列表
		$Res = GetBuildingList($conn, $Offset, 10, $AID, $BNo);
		$ret['Res'] = "";
		for ($i = 0; $i < count($Res); $i++)
		{
			$ret['Res'] .= 
			"<tr>
				<td><input type='checkbox' class='chksel' /></td>
                <td>" . $Res[$i][1] . "</td>
                <td>" . $Res[$i][2] . "</td>
                <td>
                    <div id=" . $Res[$i][0] . " class='btn-group'>
                        <a href='#' class='btn btn-primary mdf'>" . (($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员') ? "查看/修改" : "查看") . "</a>" . 
                        (($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员') ? "<a href='#' class='btn btn-danger del'>删除</a>" : "") . 
                    "</div>
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