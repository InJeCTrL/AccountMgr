<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
		<title>申请账号</title>
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
			// 已登录, 注销现账号, 并跳转到登录页面
			if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
			{
				SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-登录状态访问申请账号页面');
				unset($_SESSION['Online']);
				DisConnect($conn);
				Header("HTTP/1.1 303 See Other"); 
				Header("Location: ./index.php");
				exit();
			}
		?>
		<div class="container" style="margin-top: 3%; background-color: rgba(255,255,255,0.6);">
	        <div class="form row">
	            <div class="form-horizontal col-md-offset-3">
	                <h3 class="form-title">申请账号</h3>
	                <br />
	                <div class="col-md-9">
	                    <div class="input-group">
	                    	<span class="input-group-addon"><i class="glyphicon glyphicon-tag glyphicon-lg"></i></span>
	                        <input class="form-control required" type="text" placeholder="身份证号码" id="UID" name="UID" autofocus="autofocus" maxlength="30"/>
	                    </div>
	                    <br />
	                    <div class="input-group">
	                    	<span class="input-group-addon"><i class="glyphicon glyphicon-phone glyphicon-lg"></i></span>
	                        <input class="form-control required" type="text" placeholder="电话号码" id="TEL" name="TEL" maxlength="30"/>
	                    </div>
	                    <br />
	                    <div class="input-group">
	                    	<span class="input-group-addon"><i class="glyphicon glyphicon-user glyphicon-lg"></i></span>
	                        <input class="form-control required" type="text" placeholder="姓名" id="Name" name="Name" maxlength="1000"/>
	                    </div>
	                    <br />
	                    <div class="input-group">
	                    	<span class="input-group-addon"><i class="glyphicon glyphicon-asterisk glyphicon-lg"></i></span>
	                        <input class="form-control required" type="password" placeholder="密码" id="pwd1" name="pwd1" maxlength="1000"/>
	                        <input class="form-control required" type="password" placeholder="确认密码" id="pwd2" name="pwd2" maxlength="1000"/>
	                    </div>
	                    <br />
	                    <div class="input-group">
	                    	<span class="input-group-addon"><i class="glyphicon glyphicon-lock glyphicon-lg"></i></span>
	                        <input class="form-control required" type="text" placeholder="安全字符串" id="secstr" name="secstr" maxlength="1000"/>
	                    </div>
	                    <br />
	                    <div class="input-group">
	                    	<span class="input-group-addon"><i class="glyphicon glyphicon-edit glyphicon-lg"></i></span>
	                        <input style="width: calc(100% - 100px)" class="form-control required" type="text" placeholder="验证码" id="captcha" name="captcha" maxlength="4"/>
	                        <img style="padding-top: 2px;width: 100px;" id="capin" src="./captcha.php" onclick="this.src='captcha.php?' + new Date().getTime();" />
	                    </div>
	                    <br />
	                    <div class="form-group">
	                    	<button class="btn btn-success btn-block" id="apply" name="apply">提交申请</button>
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
		$('#apply').bind('click', function(){
			var UID= $('#UID').val();
			var TEL = $('#TEL').val();
			var Name = $('#Name').val();
			var PWD1 = $('#pwd1').val();
			var PWD2 = $('#pwd2').val();
			var Sec = $('#secstr').val();
			var cap = $('#captcha').val();
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/doApply.php',
	         		type : "post",
	         		data : {uid:UID, tel:TEL, name:Name, pwd1:PWD1, pwd2:PWD2, sec:Sec, capt:cap},
	        		async : false,
    			}
    		).responseText;
    		alert(ret);
    		if (ret == "申请完成，请等待审核通过！")
    		{
    			$(location).attr('href', './index.php');
    		}
		});
		$('#login').bind('click', function(){
    		$(location).attr('href', './index.php');
		});
	</script>
</html>