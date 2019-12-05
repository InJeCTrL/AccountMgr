<?php
	session_start();
	include "conn/DBMgr.php";
	$conn = Connect();
	// 根据用户ID获取用户信息
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		$UserInfo = GetUser($conn, $_SESSION['UserID']);
		$_SESSION['UID'] = $UserInfo['@UID'];
		$_SESSION['TEL'] = $UserInfo['@TEL'];
		$_SESSION['UserName'] = $UserInfo['@UserName'];
		$_SESSION['Type'] = $UserInfo['@strType'];
		$_SESSION['Online'] = $UserInfo['@Online'];
	}
	DisConnect($conn);
	// 未登录
	if (!isset($_SESSION['Online']) || $_SESSION['Online'] == 0)
	{
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
	</head>
	<body>
		<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		    <div class="container-fluid"> 
			    <div class="navbar-header">
			        <a class="navbar-brand" href="#" style="color: whitesmoke;">物业系统</a>
			    </div>
			    <div>
			    	<p class="navbar-text navbar-right"><?php echo $_SESSION['Type'];?>, <?php echo $_SESSION['UserName']?> 
			       		<a href="./index.php" class="navbar-link">退出</a>
			    	</p>
			   	</div>
		    </div>
		</nav>
	</body>
</html>
