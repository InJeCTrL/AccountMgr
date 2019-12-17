<?php
	/* 系统维护-数据恢复-恢复数据到数据库 */
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
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问数据恢复-恢复数据到数据库');
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
			// 临时文件夹路径
			$FolderPath = str_replace('\\', '/', __DIR__ . '/tmp/');
			// 数据表列表
			$list_tbl = GetTblList($conn);
			// 待恢复的csv备份文件
			$TblName = $list_tbl[$i_tbl][0];
			$FilePath = $FolderPath . $TblName . '.csv';
			$ret = [];
			// 解压后csv文件缺失 或 数据表数目校验不通过
			if (file_exists(iconv('utf-8', 'gbk', $FilePath)) === false || $count_tbl != count($list_tbl))
			{
				$ret['err'] = 1;
				$ret['msg'] = '上传的备份文件不能通过二次校验！';
			}
			else
			{
				// 恢复csv到数据表，返回导入行数
				$Result = RecTable($conn, $_SESSION['UserID'], $TblName, $FilePath, "系统维护-数据恢复-恢复数据到数据库");
				$ret['increment'] = ($i_tbl < $count_tbl - 1) ? (double)(100 / $count_tbl) : -1;
				$ret['msg'] = '导入了 ' . $TblName . ' 影响： ' . $Result['Result'] . ' 行';
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