<?php
	// 设置申请注册账号的所属管辖范围与身份
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
		    	<a style="text-decoration: none;" href="#" onclick="$('#mainview').load('./BasicInfo/ApplyApprove.php')">审核注册</a>
		    </li>
		    <li class="active">
		    	设置权限
		    </li>
		</ol>
		<div class="container" style="width: 80%;">
			<div role="form" class="form-horizontal">
				<br />
	        	<div class="form-group">
	                <label for="name">姓名</label>
	                <input disabled="disabled" type="text" class="form-control" id="Name" maxlength="1000">
	            </div>
	            <div class="form-group">
	                <label for="type">身份</label>
	                <select id="Type" class="form-control">
        			</select>
	            </div>
	            <div class="form-group">
	                <label for="tel">电话号码</label>
	                <input disabled="disabled" type="text" class="form-control" id="TEL" maxlength="30">
	            </div>
	            <div class="form-group">
	                <label for="uid">身份证号码</label>
	                <input disabled="disabled" type="text" class="form-control" id="UID" maxlength="30">
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
	                <button id="submitpass" name="submitpass" type="button" class="btn btn-success btn-block">完成审批</button>
	            </div>
	        </div>
        </div>
	</body>
	<script>
		var UserID = <?php echo $_REQUEST['UserID']; ?>;
		// 获取身份列表与管辖范围列表并显示
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
    		$('#TEL').val(obj_ret['TEL']);
    		$('#UID').val(obj_ret['UID']);
			var ret_Type = $.ajax
			(
				{
	        		url : './BasicInfo/GetUserTypeList.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret_Type = JSON.parse(ret_Type);
    		$('#Type').html(obj_ret_Type);
    		var ret_Area = $.ajax
			(
				{
	        		url : './BasicInfo/GetAreaList.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret_Area = JSON.parse(ret_Area);
    		$('#Area_not').html(obj_ret_Area);
		}
		// 单击完成审批时提交修改
		$('#submitpass').bind('click', function(){
    		var Type = $('#Type').val();
    		// 已选管辖范围
    		var Area = [];
			$('#Area_sel').children().each(function(){
				Area.push($(this).val());
			});
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/doPassApply.php',
	         		type : "post",
	         		data : {userid:UserID, type:Type, area_list:Area},
	        		async : false,
    			}
    		).responseText;
    		if (ret != '')
    		{
	    		alert(ret);
	    		if (ret === '正式用户添加成功！')
	    		{
	    			$('#mainview').load('./BasicInfo/ApplyApprove.php');
	    		}
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
		// 打开新增用户页面时获取身份列表与管辖范围列表并显示
		$(document).ready(SetInfoShow());
	</script>
</html>