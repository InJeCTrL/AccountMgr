<?php
	// 手工录入商铺票据
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
		SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问手工录入商铺票据');
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
		    	手工录入商铺票据
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
	                <span class="input-group-addon">商铺名称</span>
	                <input type="text" disabled="disabled" id="ShopName" class="form-control">
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
	   		<div class="form-group col-lg-4">
	   			<div class="input-group">
	   				<span class="input-group-addon">实收物业费</span>
	   				<input type="text" class="form-control" id="PMC" value="0" />
	   			</div>
	   		</div>
	   		<div class="form-group col-lg-4">
	   			<div class="input-group">
	   				<span class="input-group-addon">实收电费</span>
	   				<input type="text" class="form-control" id="ELE" value="0" />
	   			</div>
	   		</div>
	   		<div class="form-group col-lg-4">
	   			<div class="input-group">
	   				<span class="input-group-addon">实收垃圾清运费</span>
	   				<input type="text" class="form-control" id="TF" value="0" />
	   			</div>
	   		</div>
	   		<div class="form-group col-lg-12">
		   		<div class="form-group">
		            <button id="calcpay" name="calcpay" type="button" class="btn btn-primary btn-block">计算费用</button>
		        </div>
	        </div>
	   	</div>
	   	<div class="row">
	   		<div class="form-group col-lg-3">
	   			<div class="checkbox">
	   				<label>
	   					<input type="checkbox" id="AlsoCar" />
	   					同时对车辆收费
	   				</label>
	   			</div>
	   		</div>
	   	</div>
	   	<div id="AlsoCarFee" class="row" hidden="hidden">
	   		<div class="form-group col-lg-12">
	   			<div class="input-group">
	                <span class="input-group-addon">搜索车牌号</span>
	                <input id="CarSearch" type="text" class="form-control" />
	            </div>
	   		</div>
	   		<div class="form-group col-lg-12">
	   			<div class="input-group">
	                <span class="input-group-addon">车辆</span>
	                <select id="CID" class="form-control">
	                	<option value="-1">新车辆</option>
	                </select>
	            </div>
	   		</div>
	   		<div class="form-group col-lg-12">
                <div class="input-group">
	                <span class="input-group-addon">待缴费月份</span>
	                <input type="text" class="form-control" id="ToAddCarYear" maxlength="4" placeholder="年份">
	                <input type="text" class="form-control" id="ToAddCarMonth" maxlength="2" placeholder="月份">
	                <select id="CarMonth_pre" multiple="multiple" class="form-control">
	            	</select>
	                <span id="ToAddCarYearMonth" class="btn btn-primary input-group-addon">添加</span>
	                <span id="ToDeleteCarYearMonth" class="btn btn-warning input-group-addon">删除选中</span>
	                <span id="ToDeleteAllCarYearMonth" class="btn btn-warning input-group-addon">删除所有</span>
	            </div>
	        </div>
	        <div class="form-group col-lg-12">
	   			<div class="input-group">
	   				<span class="input-group-addon">实收车费</span>
	   				<input type="text" class="form-control" id="CF" value="0" />
	   			</div>
	   		</div>
	   	</div>
	   	<div class="row">
	   		<div class="form-group col-lg-12">
	   			<div class="input-group">
	   				<span class="input-group-addon">总金额</span>
	   				<input disabled="disabled" type="text" class="form-control" id="Total" value="0" />
	   			</div>
	   		</div>
	   	</div>
        <div class="form-group">
            <button id="addpay" name="addpay" type="button" class="btn btn-success btn-block">提交本次收费信息</button>
        </div>
	</body>
	<script>
		var sid = <?php echo $_REQUEST['SID']; ?>;
		// 获取住户信息并显示
		function SetInfoShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/GetShopInfo.php',
	         		type : "post",
	         		data : {SID:sid},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret = JSON.parse(ret);
    		$('#AreaID').html(obj_ret['AreaID']);
    		$('#ShopName').val(obj_ret['ShopName']);
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
			        		url : './Payment/CheckShopMonthPaid.php',
			         		type : "post",
			         		data : {SID:sid, Year:ToAddYear, Month:ToAddMonth},
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
		    			alert("该商铺此月份已缴费");
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
    	// 设置CID列表
    	function SetCarCode()
    	{
    		var CarSearchTxt = $('#CarSearch').val();
    		if (CarSearchTxt != '')
    		{
	    		var ret = $.ajax
				(
					{
		        		url : './Payment/GetCarList.php',
		         		type : "post",
		         		data : {CarCode:CarSearchTxt},
		        		async : false,
	    			}
	    		).responseText;
	    		var obj_ret = JSON.parse(ret);
	    		// 新车辆
	    		if (obj_ret['Num'] === 0)
	    		{
	    			$('#CID').html("<option value='-1'>新车辆</option>");
	    		}
	    		else
	    		{
	    			$('#CID').html(obj_ret['List']);
	    		}
    		}
    		else
    		{
    			$('#CID').html("<option value='-1'>新车辆</option>");
    		}
    		$('#CarSearch').val(CarSearchTxt);
    	}
    	// 搜索车牌号
    	$('#CarSearch').bind('input propertychange', function(){
    		SetCarCode();
    	});
    	// 增加新的车辆缴费月份
		$('#ToAddCarYearMonth').bind('click', function(){
    		var ToAddCarYear = $('#ToAddCarYear').val();
    		var ToAddCarMonth = $('#ToAddCarMonth').val();
    		if (ToAddCarYear === '' || ToAddCarMonth === '')
    		{
    			alert("年份或月份为空！");
    		}
    		else
    		{
    			var PreList = $('#CarMonth_pre').val();
    			// 判断重复
    			var distinct = true;
				var count = $('#CarMonth_pre').find('option').length;
				for (var i = 0; i < count; i++)
				{
					if ($('#CarMonth_pre').get(0).options[i].value === ToAddCarYear + '.' + ToAddCarMonth)
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
					var cid = $('#CID').val();
		    		var ret = $.ajax
					(
						{
			        		url : './Payment/CheckCarMonthPaid.php',
			         		type : "post",
			         		data : {CID:cid, Year:ToAddCarYear, Month:ToAddCarMonth},
			        		async : false,
		    			}
		    		).responseText;
		    		var obj_ret = JSON.parse(ret);
		    		if (obj_ret['IsPaid'] === 0)
		    		{
		    			$('#ToAddCarYear').val('');
		    			$('#ToAddCarMonth').val('');
		    			$('#CarMonth_pre').append("<option value='" + ToAddCarYear + "." + ToAddCarMonth + "'>" + ToAddCarYear + "." + ToAddCarMonth + "</option>");
		    		}
		    		else
		    		{
		    			alert("该车辆此月份已缴费");
		    		}
				}
    		}
		});
		// 删除所有月份
		$('#ToDeleteAllCarYearMonth').bind('click', function(){
    		$('#CarMonth_pre').empty();
    	});
		// 删除选中月份
		$('#ToDeleteCarYearMonth').bind('click', function(){
    		var ops = $('#CarMonth_pre').children();
			for (var i = ops.length - 1; i >= 0; i--)
			{
				if (ops[i].selected == true)
			    {
			    	ops[i].remove();
				}
			}
    	});
    	// 同时对车辆收费
		$('#AlsoCar').bind('click', function(){
			if ($('#AlsoCar').get(0).checked === true)
			{
				$('#AlsoCarFee').removeAttr("hidden");
			}
    		else
    		{
    			$('#AlsoCarFee').attr("hidden", "hidden");
    		}
    		SetSumFee();
    	});
    	// 计算金额
		$('#calcpay').bind('click', function(){
    		var count = $('#Month_pre').find('option').length;
			var ret = $.ajax
			(
				{
	        		url : './Payment/CalcShopPay.php',
	         		type : "post",
	         		data : {SID:sid, Count:count},
	        		async : false,
    			}
    		).responseText;
    		var obj_ret = JSON.parse(ret);
			$('#PMC').val(obj_ret['PMC']);
			$('#ELE').val(obj_ret['ELE']);
			$('#TF').val(obj_ret['TF']);
			SetSumFee();
		});
		// 提交收费信息
		$('#addpay').bind('click', function(){
    		var Name = $('#Name').val();
    		var TEL = $('#TEL').val();
    		var PMC = $('#PMC').val();
    		var ELE = $('#ELE').val();
    		var TF = $('#TF').val();
    		var opsA = $('#Month_pre').children();
			for (var i = opsA.length - 1; i >= 0; i--)
			{
			    opsA[i].selected = true;
			}
			var list_Month = $('#Month_pre').val();
			var cid = $('#CID').val();
			var cnum = $("#CarSearch").val();
			var opsB = $('#CarMonth_pre').children();
			for (var i = opsB.length - 1; i >= 0; i--)
			{
			    opsB[i].selected = true;
			}
			var list_CarMonth = $('#CarMonth_pre').val();
			var CarFlag = $('#AlsoCar').get(0).checked;
			var CarFee = $('#CF').val();
			var TTime = $('#TicketTime').val();
			var ret = $.ajax
			(
				{
	        		url : './Payment/doAddShopPayment.php',
	         		type : "post",
	         		data : {SID:sid, name:Name, tel:TEL, pmc:PMC, ele:ELE, tf:TF, Month:list_Month, CarMonth:list_CarMonth, CFlag:CarFlag, CFee:CarFee, Time: TTime, CID: cid, CarCode: cnum},
	        		async : false,
    			}
    		).responseText;
    		if (ret != '')
    		{
	    		alert(ret);
	    		if (ret === "商铺车辆缴费新增成功！\n商铺缴费新增成功！" || ret === "商铺缴费新增成功！")
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
		// 初始获取基本信息与计算后的预设价格
		$(document).ready(SetInfoShow());
		// 实收物业费变化
		$('#PMC').bind('input propertychange', function(){
			SetSumFee();
		});
		// 实收电费变化
		$('#ELE').bind('input propertychange', function(){
			SetSumFee();
		});
		// 实收垃圾清运费变化
		$('#TF').bind('input propertychange', function(){
			SetSumFee();
		});
		// 实收车费变化
		$('#CF').bind('input propertychange', function(){
			SetSumFee();
		});
		// 设置总金额计算值
		function SetSumFee()
		{
			var PMC = parseFloat($('#PMC').val());
			var ELE = parseFloat($('#ELE').val());
			var TF = parseFloat($('#TF').val());
			var CarFee = 0;
			if ($('#AlsoCar').get(0).checked === true)
			{
				$('#AlsoCarFee').removeAttr("hidden");
				CarFee = parseFloat($('#CF').val());
			}
    		else
    		{
    			$('#AlsoCarFee').attr("hidden", "hidden");
    		}
			$('#Total').val(PMC + ELE + TF + CarFee); 
		}
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