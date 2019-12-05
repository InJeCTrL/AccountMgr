<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>重设密码</title>
		<script src="../js/jquery.min.js"></script>
		<script src="../js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="../css/bootstrap.min.css">
	</head>
	<?php
		session_start();
		include_once('../conn/DBMgr.php');
		$conn = Connect();
		// 已登录, 注销现账号
		if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
		{
			SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-登录状态访问重设(找回)密码页面');
			unset($_SESSION['Online']);
			Header("HTTP/1.1 303 See Other"); 
			Header("Location: ../index.php");
		}
		// 检查用户身份的页面未核实用户身份, 不允许重置密码
		if (!isset($_SESSION['enResetPwd']))
		{
			Header("HTTP/1.1 303 See Other"); 
			Header("Location: ../index.php");
		}
		DisConnect($conn);
	?>
	<body style="background-image: url(../images/loginBG.jpg);background-repeat:no-repeat;background-size: cover;width: 100%;height: 100%;overflow: hidden;position: absolute;">
		<div class="container" style="margin-top: 8%; background-color: rgba(255,255,255,0.6);">
	        <div class="form row">
	            <div class="form-horizontal col-md-offset-3">
	                <h3 class="form-title">重设密码</h3>
	                <br />
	                <div class="col-md-9">
	                    <div class="input-group">
	                    	<span class="input-group-addon"><i class="glyphicon glyphicon-asterisk glyphicon-lg"></i></span>
	                        <input class="form-control required" type="password" placeholder="新密码" id="password" name="password" autofocus="autofocus" maxlength="1000"/>
	                        <input class="form-control required" type="password" placeholder="确认新密码" id="password_re" name="password_re" autofocus="autofocus" maxlength="1000"/>
	                    </div>
	                    <br />
	                    <div class="form-group">
	                    	<button class="btn btn-success btn-block" id="set" name="set">确认</button>
	                    </div>
	                    <div class="form-group">
	                        <button class="btn btn-primary btn-block" id="login" name="login">返回登录</button>
	                    </div>
	                </div>
	            </div>
	        </div>
	   </div>
	</body>
	<script>
		$('#set').bind('click', function(){
			var pwd1 = $('#password').val();
			var pwd2 = $('#password_re').val();
			var ret = $.ajax
			(
				{
	        		url : './dosetpwd.php',
	         		type : "post",
	         		data : {PWD1:pwd1, PWD2:pwd2},
	        		async : false,
    			}
    		).responseText;
    		alert(ret);
    		if (ret === '修改成功！')
    		{
    			$(location).attr('href', '../index.php');
    		}
		});
		$('#login').bind('click', function(){
    		$(location).attr('href', '../index.php');
		});
	</script>
</html>