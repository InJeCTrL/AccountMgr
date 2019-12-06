<?php
	// 查看/修改用户账号
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
		    	<a style="text-decoration: none;" href="#" onclick="$('#mainview').load('./BasicInfo/UserManagement.php')">用户管理</a>
		    </li>
		    <li class="active">
		    	用户详细信息
		    </li>
		</ol>
		<div class="tabbable" id="tabs">
			<ul class="nav nav-tabs">
				<li class="active">
					<a id="tab_info" name="tab_info" href="#panel-info" data-toggle="tab">用户信息</a>
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
				                <select id="Type" class="form-control">
                    			</select>
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
				            	<div class="form-group col-lg-5">
					            	<label>管辖范围(已选)</label>
					            	<select id="Area_sel" multiple="multiple" class="form-control">
					            	</select>
					            </div>
					            <div class="form-group col-lg-3">
					            	<label>&nbsp;</label>
					            	<button id="addone" class="form-control btn btn-primary">添加</button>
					            	<button id="addall" class="form-control btn btn-primary">全部添加</button>
				            		<button id="delone" class="form-control btn btn-warning">删除</button>
				            		<button id="delall" class="form-control btn btn-warning">全部删除</button>
					            </div>
					            <div class="form-group col-lg-5">
					            	<label>未选列表</label>
					            	<select id="Area_not" multiple="multiple" class="form-control">
					            	</select>
					            </div>
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
		var UserID = <?php echo $_REQUEST['UserID']; ?>;
		// 获取个人信息并显示
		function SetInfoShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/GetUserInfo.php',
	         		type : "post",
	         		data : {userid:UserID},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret = JSON.parse(ret);
    		$('#Name').val(obj_ret['Name']);
    		$('#Type').html(obj_ret['Type']);
    		$('#TEL').val(obj_ret['TEL']);
    		$('#UID').val(obj_ret['UID']);
    		$('#Area_sel').html(obj_ret['Area_sel']);
    		$('#Area_not').html(obj_ret['Area_not']);
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
    		var Type = $('#Type').val();
    		// 已选管辖范围
    		var Area = [];
			$('#Area_sel').children().each(function(){
				Area.push($(this).val());
			});
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/doSetUserInfo.php',
	         		type : "post",
	         		data : {userid:UserID, name:Name, tel:TEL, uid:UID, type:Type, area_list:Area},
	        		async : false,
    			}
    		).responseText;
    		if (ret != '')
    		{
	    		alert(ret);
	    		if (ret === '修改成功！')
	    		{
	    			$('#top').load('./top.php');
	    			$('#left').load('./left.php');
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
					url : "./BasicInfo/doSetUserpwd.php",
					type : "post",
					data : {userid:UserID, PWD1:Pwd1, PWD2:Pwd2},
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
					url : "./BasicInfo/doSetUsersec.php",
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
		// 删除按钮
		$('#delone').bind('click', function(){
			var ops = $('#Area_sel').children();
			for (var i = ops.length - 1; i >= 0; i--)
			{
				if (ops[i].selected == true)
			    {
					$('#Area_not').append(ops[i]);
				}
			}
		});
		// 添加按钮
		$('#addone').bind('click', function(){
			var ops = $('#Area_not').children();
			for (var i = ops.length - 1; i >= 0; i--)
			{
				if (ops[i].selected == true)
			    {
					$('#Area_sel').append(ops[i]);
				}
			}
		});
		// 添加所有按钮
		$('#addall').bind('click', function(){
			var ops = $('#Area_not').children();
			for (var i = ops.length - 1; i >= 0; i--)
			{
				$('#Area_sel').append(ops[i]);
			}
		});
		// 删除所有按钮
		$('#delall').bind('click', function(){
			var ops = $('#Area_sel').children();
			for (var i = ops.length - 1; i >= 0; i--)
			{
				$('#Area_not').append(ops[i]);
			}
		});
		// 打开详细信息页面时获取详细信息并显示
		$(document).ready(SetInfoShow());
	</script>
</html>