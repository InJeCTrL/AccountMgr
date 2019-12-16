<?php
	/* 系统维护-数据备份-备份数据到本地 */
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
		// 不是超级管理员，强制注销
		if ($_SESSION['Type'] != '超级管理员')
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问数据备份-备份数据到本地');
			unset($_SESSION['Online']);
			exit();
		}
		else
		{
			// 数据表序号
			if (isset($_REQUEST['i_tbl']) && $_REQUEST['i_tbl'] != '')
			{
				$i_tbl = $_REQUEST['i_tbl'];
				$i_tbl = (int)$i_tbl;
			}
			else
			{
				exit();
			}
			// 数据表总数
			if (isset($_REQUEST['count_tbl']) && $_REQUEST['count_tbl'] != '')
			{
				$count_tbl = $_REQUEST['count_tbl'];
				$count_tbl = (int)$count_tbl;
			}
			else
			{
				exit();
			}
			$ret = [];
			// 备份数据表到允许目录，传回备份表的占比
			$Result = BakTable($conn, $i_tbl, "系统维护-数据备份-备份数据到本地");
			// 尚未备份到最后一个表
			if ($i_tbl < $count_tbl - 1)
			{
				$ret['increment'] = $Result['@Result'];
				$ret['link'] = '';
			}
			// 备份完最后一个表，ZIP打包
			else
			{
				$ret['increment'] = -1;
				$ret['link'] = PackBak();
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