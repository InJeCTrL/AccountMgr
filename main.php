<?php
	session_start();
	// 禁止重复找回密码
	unset($_SESSION['enResetPwd']);
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
	// 未登录, 跳转到登录界面
	if (!isset($_SESSION['Online']) || $_SESSION['Online'] == 0)
	{	
		Header("HTTP/1.1 303 See Other"); 
		Header("Location: ./index.php");
		exit();
	}
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<script src="js/bootstrap-datetimepicker.min.js"></script>
		<script src="js/bootstrap-datetimepicker.zh-CN.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css" />
		<title>物业系统</title>
	</head>
	<body style="padding-top: 70px;">
		<div id="top"></div>
		<div class="container-fluid">
            <div class="row">
                <div id="left" class="col-sm-2">
                </div>
                <div id="mainview" name="mainview" class="col-sm-10">
                </div>
            </div>
        </div>
        <div>
        	<div class="footer">
		    	<p class="text-center">
		    		2019 AMGR InJeCTrL
		    	</p>
			</div>
        </div>
    </body>
    <script>
    	$(document).ready(function(){
        	$('#top').load('./top.php');
        	$('#left').load('./left.php');
        	$('#mainview').load('./AreaManagement/index.php');
       	});
    </script>
</html>