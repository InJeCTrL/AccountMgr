<?php
	session_start();
	// 禁止重复找回密码
	unset($_SESSION['enResetPwd']);
	include "../conn/DBMgr.php";
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
		<ol class="breadcrumb">
		    <li class="active">
		    	基本信息
		    </li>
		    <li class="active">
		    	个人信息
		    </li>
		</ol>
		<div class="tabbable" id="tabs">
			<ul class="nav nav-tabs">
				<li class="active">
					<a id="tab_info" name="tab_info" href="#panel-info" data-toggle="tab">我的信息</a>
				</li>
				<li>
					<a id="tab_pwd" name="tab_pwd" href="#panel-changepwd" data-toggle="tab">修改密码</a>
				</li>
				<li>
					<a id="tab_sec" name="tab_sec" href="#panel-changesec" data-toggle="tab">修改安全字符串</a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="panel-info">
					<div class="container" style="width: 80%;">
						<div role="form" class="form-horizontal">
							<br />
				        	<div class="form-group">
				                <label for="name">姓名</label>
				                <input type="text" class="form-control" id="Name" maxlength="1000">
				            </div>
				            <div class="form-group">
				                <label for="type">身份</label>
				                <input disabled="disabled" type="text" class="form-control" id="Type" maxlength="1000">
				            </div>
				            <div class="form-group">
				                <label for="tel">电话号码</label>
				                <input type="text" class="form-control" id="TEL" maxlength="30">
				            </div>
				            <div class="form-group">
				                <label for="uid">身份证号码</label>
				                <input type="text" class="form-control" id="UID" maxlength="30">
				            </div>
				            <div class="form-group">
				                <button id="mdfinfo" name="mdfinfo" type="button" class="btn btn-success btn-block">提交修改</button>
				            </div>
				        </div>
			        </div>
				</div>
				<div class="tab-pane" id="panel-changepwd">
					<div class="container" style="width: 80%;">
						<form role="form" class="form-horizontal">
							<br />
				        	<div class="form-group">
				                <label for="pwd1">密码</label>
				                <input type="password" class="form-control" id="Pwd1" maxlength="1000">
				            </div>
				            <div class="form-group">
				                <label for="pwd2">确认密码</label>
				                <input type="password" class="form-control" id="Pwd2" maxlength="1000">
				            </div>
				            <div class="form-group">
				                <button id="mdfpwd" name="mdfpwd" type="button" class="btn btn-success btn-block">提交修改</button>
				            </div>
				        </form>
					</div>
				</div>
				<div class="tab-pane" id="panel-changesec">
					<div class="container" style="width: 80%;">
						<form role="form" class="form-horizontal">
							<br />
				        	<div class="form-group">
				                <label for="sec">安全字符串</label>
				                <input type="text" class="form-control" id="Sec" maxlength="1000">
				            </div>
				            <div class="form-group">
				                <button id="mdfsec" name="mdfsec" type="button" class="btn btn-success btn-block">提交修改</button>
				            </div>
				        </form>
					</div>
				</div>
			</div>
		</div>
	</body>
	<script>
		// 获取个人信息并显示
		function SetInfoShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/GetPersonalInfo.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret = JSON.parse(ret);
    		$('#Name').val(obj_ret['Name']);
    		$('#Type').val(obj_ret['Type']);
    		$('#TEL').val(obj_ret['TEL']);
    		$('#UID').val(obj_ret['UID']);
		}
		// 切换tab到我的信息时清空我的信息输入框
		$('#tab_info').bind('click', SetInfoShow);
		// 切换tab到修改密码时清空密码输入框
		$('#tab_pwd').bind('click', function(){
			$('#Pwd1').val("");
			$('#Pwd2').val("");
		});
		// 切换tab到修改安全字符串时清空安全字符串输入框
		$('#tab_sec').bind('click', function(){
			$('#Sec').val("");
		});
		// 单击修改个人信息时提交修改
		$('#mdfinfo').bind('click', function(){
			var Name = $('#Name').val();
    		var TEL = $('#TEL').val();
    		var UID = $('#UID').val();
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/doSetPersonalInfo.php',
	         		type : "post",
	         		data : {name:Name, tel:TEL, uid:UID},
	        		async : false,
    			}
    		).responseText;
    		if (ret != '')
    		{
	    		alert(ret);
	    		if (ret === '修改成功！')
	    		{
	    			$('#top').load('./top.php');
	    		}
    		}
    		// 返回为空则认为用户下线，刷新页面
    		else
    		{
    			window.location.reload();
    		}
		});
		// 单击修改密码时提交修改
		$('#mdfpwd').bind('click', function(){
			var Pwd1 = $('#Pwd1').val();
			var Pwd2 = $('#Pwd2').val();
			var ret = $.ajax
			(
				{
					url : "./BasicInfo/dosetpwd_loggedin.php",
					type : "post",
					data : {PWD1:Pwd1, PWD2:Pwd2},
					async : false,
				}
			).responseText;
			if (ret != '')
			{
				alert(ret);
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
		});
		// 单击修改安全字符串时提交修改
		$('#mdfsec').bind('click', function(){
			var Sec = $('#Sec').val();
			var ret = $.ajax
			(
				{
					url : "./BasicInfo/dosetsec.php",
					type : "post",
					data : {SEC:Sec},
					async : false,
				}
			).responseText;
			if (ret != '')
			{
				alert(ret);
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
		});
		// 打开个人信息页面时获取个人信息并显示
		$(document).ready(SetInfoShow());
	</script>
</html>