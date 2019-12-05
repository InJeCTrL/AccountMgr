<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>用户登录</title>
		<script src="js/jquery.min.js"></script>
		<script src="js/bootstrap.min.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css">
	</head>
	<body style="background-image: url(./images/loginBG.jpg);background-repeat:no-repeat;background-size: cover;width: 100%;height: 100%;overflow: hidden;position: absolute;">
		<?php
			session_start();
			// 禁止重复找回密码
			unset($_SESSION['enResetPwd']);
			include_once('./conn/DBMgr.php');
			$conn = Connect();
			// 已登录, 注销现账号
			if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
			{
				SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '注销');
				unset($_SESSION['Online']);
			}
			DisConnect($conn);
		?>
		<div class="container" style="margin-top: 10%; background-color: rgba(255,255,255,0.6);">
	        <div class="form row">
	            <div class="form-horizontal col-md-offset-3">
	                <h3 class="form-title">物业系统登录</h3>
	                <br />
	                <div class="col-md-9">
	                    <div class="input-group">
	                    	<span class="input-group-addon"><i class="glyphicon glyphicon-user glyphicon-lg"></i></span>
	                        <input class="form-control required" type="text" placeholder="身份证号码 / 手机号码" id="UIDTEL" name="UIDTEL" autofocus="autofocus" maxlength="30"/>
	                    </div>
	                    <br />
	                    <div class="input-group">
	                    	<span class="input-group-addon"><i class="glyphicon glyphicon-lock glyphicon-lg"></i></span>
	                        <input class="form-control required" type="password" placeholder="密码" id="password" name="password" maxlength="30"/>
	                    </div>
	                    <br />
	                    <div class="input-group">
	                    	<span class="input-group-addon"><i class="glyphicon glyphicon-edit glyphicon-lg"></i></span>
	                        <input style="width: calc(100% - 100px)" class="form-control required" type="text" placeholder="验证码" id="captcha" name="captcha" maxlength="4"/>
	                        <img style="padding-top: 2px;width: 100px;" id="capin" src="./captcha.php" onclick="this.src='captcha.php?' + new Date().getTime();" />
	                    </div>
	                    <br />
	                    <div class="form-group">
	                    	<button class="btn btn-danger btn-block" id="forget" name="forget">忘记密码</button>
	                    </div>
	                    <div class="form-group">
	                    	<button class="btn btn-warning btn-block" id="apply" name="apply">申请账号</button>
	                    </div>
	                    <div class="form-group">
	                        <button class="btn btn-success btn-block" id="login" name="login">登录</button>
	                    </div>
	                </div>
	            </div>
	        </div>
	   </div>
	</body>
	<script>
		$('#login').bind('click', function(){
			var UIDTEL = $('#UIDTEL').val();
			var PWD = $('#password').val();
			var cap = $('#captcha').val();
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/checklogin.php',
	         		type : "post",
	         		data : {uidtel:UIDTEL, pwd:PWD, capt:cap},
	        		async : false,
    			}
    		).responseText;
    		if (ret != "登录成功！")
    		{
    			alert(ret);
    		}
    		else
    		{
    			$(location).attr('href', 'main.php');
    		}
		});
		$('#forget').bind('click', function(){
			$(location).attr('href', './RecUser.php');
		});
		$('#apply').bind('click', function(){
			$(location).attr('href', './Apply.php');
		});
	</script>
</html>