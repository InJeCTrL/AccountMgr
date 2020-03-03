<?php
	// 手工录入车辆票据
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
	// 未登录
	if (!isset($_SESSION['Online']) || $_SESSION['Online'] == 0)
	{	
		exit();
	}
	// 不是管理员及以上，强制注销
	if ($_SESSION['Type'] != '超级管理员' && $_SESSION['Type'] != '管理员')
	{
		SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问手工录入住户票据');
		unset($_SESSION['Online']);
		exit();
	}
	DisConnect($conn);
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8">
	</head>
	<body>
		<ol class="breadcrumb">
		    <li class="active">
		    	收费与账目
		    </li>
		    <li class="active">
		    	<a style="text-decoration: none;" href="#" onclick="$('#mainview').load('./Payment/ImportTicket.php')">筛选列表</a>
		    </li>
		    <li class="active">
		    	手工录入车辆票据
		    </li>
		</ol>
		<div class="row">
            <div class="form-group col-lg-12">
                <div class="input-group">
	                <span class="input-group-addon">票据时间选择</span>
	                <input type="text" id="TicketTime" class="form-control">
	            </div>
	        </div>
	   	</div>
		<div class="row">
            <div class="form-group col-lg-6">
                <div class="input-group">
	                <span class="input-group-addon">归属楼盘</span>
	                <select disabled="disabled" id="AreaID" class="form-control">
	                </select>
	            </div>
	        </div>
	        <div class="form-group col-lg-6">
                <div class="input-group">
	                <span class="input-group-addon">车牌号</span>
	                <input type="text" disabled="disabled" id="CarCode" class="form-control">
	            </div>
	        </div>
	   	</div>
	   	<div class="row">
            <div class="form-group col-lg-6">
                <div class="input-group">
	                <span class="input-group-addon">本次缴费者姓名</span>
	                <input type="text" class="form-control" id="Name" maxlength="1000">
	            </div>
	        </div>
	        <div class="form-group col-lg-6">
                <div class="input-group">
	                <span class="input-group-addon">本次缴费者电话号码</span>
	                <input type="text" class="form-control" id="TEL" maxlength="30">
	            </div>
	        </div>
	   	</div>
	   	<div class="row">
	   		<div class="form-group col-lg-12">
                <div class="input-group">
	                <span class="input-group-addon">待缴费月份</span>
	                <input type="text" class="form-control" id="ToAddYear" maxlength="4" placeholder="年份">
	                <input type="text" class="form-control" id="ToAddMonth" maxlength="2" placeholder="月份">
	                <select id="Month_pre" multiple="multiple" class="form-control">
	            	</select>
	                <span id="ToAddYearMonth" class="btn btn-primary input-group-addon">添加</span>
	                <span id="ToDeleteYearMonth" class="btn btn-warning input-group-addon">删除选中</span>
	                <span id="ToDeleteAllYearMonth" class="btn btn-warning input-group-addon">删除所有</span>
	            </div>
	        </div>
        </div>
	   	<div class="row">
	   		<div class="form-group col-lg-12">
	   			<div class="input-group">
	   				<span class="input-group-addon">实收车费</span>
	   				<input type="text" class="form-control" id="CarFee" value="0" />
	   			</div>
	   		</div>
	   	</div>
        <div class="form-group">
            <button id="addpay" name="addpay" type="button" class="btn btn-success btn-block">提交本次收费信息</button>
        </div>
	</body>
	<script>
		var cid = <?php echo $_REQUEST['CID']; ?>;
		// 获取车辆信息并显示
		function SetInfoShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/GetCarInfo.php',
	         		type : "post",
	         		data : {CID:cid},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret = JSON.parse(ret);
    		$('#AreaID').html(obj_ret['AreaID']);
    		$('#CarCode').val(obj_ret['CarCode']);
    		$('#Name').val(obj_ret['Name']);
    		$('#TEL').val(obj_ret['TEL']);
		}
		// 增加新的缴费月份
		$('#ToAddYearMonth').bind('click', function(){
    		var ToAddYear = $('#ToAddYear').val();
    		var ToAddMonth = $('#ToAddMonth').val();
    		if (ToAddYear === '' || ToAddMonth === '')
    		{
    			alert("年份或月份为空！");
    		}
    		else
    		{
    			var PreList = $('#Month_pre').val();
    			// 判断重复
    			var distinct = true;
				var count = $('#Month_pre').find('option').length;
				for (var i = 0; i < count; i++)
				{
					if ($('#Month_pre').get(0).options[i].value === ToAddYear + '.' + ToAddMonth)
					{
					    distinct = false;
					    break;
					}
				}
				if (distinct === false)
				{
					alert("对应的月份已在列表中！");
				}
				else
				{
		    		var ret = $.ajax
					(
						{
			        		url : './Payment/CheckCarMonthPaid.php',
			         		type : "post",
			         		data : {CID:cid, Year:ToAddYear, Month:ToAddMonth},
			        		async : false,
		    			}
		    		).responseText;
		    		var obj_ret = JSON.parse(ret);
		    		if (obj_ret['IsPaid'] === 0)
		    		{
		    			$('#ToAddYear').val('');
		    			$('#ToAddMonth').val('');
		    			$('#Month_pre').append("<option value='" + ToAddYear + "." + ToAddMonth + "'>" + ToAddYear + "." + ToAddMonth + "</option>");
		    		}
		    		else
		    		{
		    			alert("该车辆此月份已缴费");
		    		}
				}
    		}
		});
		// 删除所有月份
		$('#ToDeleteAllYearMonth').bind('click', function(){
    		$('#Month_pre').empty();
    	});
		// 删除选中月份
		$('#ToDeleteYearMonth').bind('click', function(){
    		var ops = $('#Month_pre').children();
			for (var i = ops.length - 1; i >= 0; i--)
			{
				if (ops[i].selected == true)
			    {
			    	ops[i].remove();
				}
			}
    	});
		// 提交收费信息
		$('#addpay').bind('click', function(){
    		var Name = $('#Name').val();
    		var TEL = $('#TEL').val();
    		var CarFee = $('#CarFee').val();
			var TTime = $('#TicketTime').val();
			var opsA = $('#Month_pre').children();
			for (var i = opsA.length - 1; i >= 0; i--)
			{
			    opsA[i].selected = true;
			}
			var list_Month = $('#Month_pre').val();
			var ret = $.ajax
			(
				{
	        		url : './Payment/doAddCarPayment.php',
	         		type : "post",
	         		data : {CID:cid, name:Name, tel:TEL, Month:list_Month, CFee:CarFee, Time: TTime},
	        		async : false,
    			}
    		).responseText;
    		if (ret != '')
    		{
	    		alert(ret);
	    		if (ret === "车辆缴费新增成功！")
	    		{
	    			$('#mainview').load('./Payment/ImportTicket.php');
	    		}
    		}
    		// 返回为空则认为用户下线，刷新页面
    		else
    		{
    			window.location.reload();
    		}
		});
		// 初始获取基本信息
		$(document).ready(SetInfoShow());
		// 票据时间选择器
		$('#TicketTime').datetimepicker({
			format: 'yyyy-mm-dd',
			language: 'zh-CN',
			weekStart: 1,
			autoclose: true,
			startView: 'year',
			maxView: 'year',
			minView: 2
		});
	</script>
</html>