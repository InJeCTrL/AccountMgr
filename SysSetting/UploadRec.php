<?php
	/* 系统维护-数据恢复-上传备份文件 */
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
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问数据恢复-上传备份文件');
			unset($_SESSION['Online']);
			exit();
		}
		else
		{
			$FolderPath = str_replace('\\', '/', __DIR__ . '/tmp/');
			// 上传备份文件前清空临时文件夹
			$obj_path = scandir($FolderPath);
			foreach ($obj_path as $file)
			{
				if($file !="." && $file !="..")
				{
					unlink(iconv('utf-8', 'gbk', $FolderPath) . $file);
				}
			}
			$ret = [];
			// 文件上传失败
			if (!isset($_FILES['upfile']) || $_FILES['upfile']['error'] != 0)
			{
				$ret['err'] = 1;
				$ret['msg'] = '上传失败！';
			}
			// 文件上传成功
			else
			{
				$name = $_FILES['upfile']['name'];
				$path = iconv('utf-8', 'gbk', $FolderPath . $name);
				// 移动上传的文件到临时文件夹
				move_uploaded_file($_FILES['upfile']['tmp_name'], $path);
				$num_tbl = CheckValid($conn, $path);
				// 检查通过
				if ($num_tbl != -1)
				{
					$ret['err'] = 0;
					$ret['msg'] = '备份文件上传成功！';
					$ret['num'] = $num_tbl;
				}
				// 检查未通过
				else
				{
					$ret['err'] = 1;
					$ret['msg'] = '上传的备份文件不能通过校验！';
				}
				unlink($path);
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
<?php
	// 检查上传的压缩文件是否有效
	function CheckValid($conn, $FilePath)
	{
		// 缺少备份文件标志
		$invalid = false;
		$zip = new ZipArchive();
		// 打开上传的压缩文件
		$zip->open($FilePath);
		// 获取数据表列表
		$Result = GetTblList($conn);
		foreach ($Result as $tbl)
		{
			// 数据表对应的备份文件不存在
			if ($zip->statName($tbl[0] . '.csv') === false)
			{
				$invalid = true;
				break;
			}
		}
		if ($invalid === true)
		{
			$zip->close();
			return -1;
		}
		else
		{
			$zip->extractTo(dirname($FilePath));
			$zip->close();
			return count($Result);
		}
	}
?>