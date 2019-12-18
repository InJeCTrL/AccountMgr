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
		    	收费与账目
		    </li>
		    <li class="active">
		    	收费打票
		    </li>
		</ol>
		<div class="tabbable" id="tabs">
			<ul class="nav nav-tabs">
				<li class="active">
					<a id="tab_household" name="tab_household" href="#panel-household" data-toggle="tab">住户缴费</a>
				</li>
				<li>
					<a id="tab_shop" name="tab_shop" href="#panel-shop" data-toggle="tab">商铺缴费</a>
				</li>
				<li>
					<a id="tab_car" name="tab_car" href="#panel-car" data-toggle="tab">仅车辆缴费</a>
				</li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="panel-household">
					<h3>条件筛选</h3>
			        <div class="row">
			            <div class="form-group col-lg-4">
			                <div class="input-group">
			                    <span class="input-group-addon">楼盘</span>
			                    <select id="AreaID_household" class="form-control">
			                    	<option value="">任意</option>
			                    </select>
			                </div>
			            </div>
			            <div class="form-group col-lg-4">
			                <div class="input-group">
			                    <span class="input-group-addon">楼栋</span>
			                    <select id="BID" class="form-control">
			                    	<option value="">任意</option>
			                    </select>
			                </div>
			            </div>
			            <div class="form-group col-lg-4">
			                <div class="input-group">
			                    <span class="input-group-addon">门牌号</span>
			                    <input id="RoomCode" type="text" class="form-control" />
			                </div>
			            </div>
			        </div>
			        <div class="row">
			        	<div class="form-group col-lg-4">
			                <div class="input-group">
			                    <span class="input-group-addon">住户姓名</span>
			                    <input id="Name_household" type="text" class="form-control" />
			                </div>
			            </div>
			            <div class="form-group col-lg-4">
			                <div class="input-group">
			                    <span class="input-group-addon">电话号码</span>
			                    <input id="TEL_household" type="text" class="form-control" />
			                </div>
			            </div>
			            <div class="form-group col-lg-4">
			                <div class="input-group">
			                    <span class="input-group-addon">住房面积</span>
			                    <input id="square" type="text" class="form-control" />
			                </div>
			            </div>
			        </div>
			        <div class="row">
						<div class="form-group col-lg-12">
			        		<button id="doquery_household" class="btn btn-success btn-block">查询</button>
			        	</div>
			        </div>
					<div class="table-responsive">
					    <table class="table table-striped ">
					        <thead>
					            <tr>
					                <th class="col-lg-2">楼盘名称</th>
					                <th class="col-lg-2">楼栋号</th>
					                <th class="col-lg-2">门牌号</th>
					                <th class="col-lg-3">住户姓名</th>
					                <th class="col-lg-3">操作</th>
					            </tr>
					        </thead>
					        <tbody id="householdlist" name="householdlist">
					        </tbody>
					    </table>
					</div>
					<ul id="pagelimit_household" name="pagelimit_household" class="pagination page_household" style="float: right;">
					</ul>
					<ul class="nav nav-pills pagination" style="float: left;">
						<li class="active">
							 <a href="#"><span id="HouseHoldCount" name="HouseHoldCount" class="badge pull-right"></span>查得住户数量：</a>
						</li>
					</ul>
				</div>
				<div class="tab-pane" id="panel-shop">
					<h3>条件筛选</h3>
			        <div class="row">
			            <div class="form-group col-lg-6">
			                <div class="input-group">
			                    <span class="input-group-addon">楼盘</span>
			                    <select id="AreaID_shop" class="form-control">
			                    	<option value="">任意</option>
			                    </select>
			                </div>
			            </div>
			            <div class="form-group col-lg-6">
			                <div class="input-group">
			                    <span class="input-group-addon">商铺名称</span>
			                    <input id="ShopName" type="text" class="form-control" />
			                </div>
			            </div>
			        </div>
			        <div class="row">
			        	<div class="form-group col-lg-6">
			                <div class="input-group">
			                    <span class="input-group-addon">店主姓名</span>
			                    <input id="Name_shop" type="text" class="form-control" />
			                </div>
			            </div>
			            <div class="form-group col-lg-6">
			                <div class="input-group">
			                    <span class="input-group-addon">电话号码</span>
			                    <input id="TEL_shop" type="text" class="form-control" />
			                </div>
			            </div>
			        </div>
			        <div class="row">
						<div class="form-group col-lg-12">
			        		<button id="doquery_shop" class="btn btn-success btn-block">查询</button>
			        	</div>
			        </div>
					<div class="table-responsive">
					    <table class="table table-striped ">
					        <thead>
					            <tr>
					                <th class="col-lg-2">楼盘名称</th>
					                <th class="col-lg-2">商铺名称</th>
					                <th class="col-lg-3">店主姓名</th>
					                <th class="col-lg-2">电话号码</th>
					                <th class="col-lg-3">操作</th>
					            </tr>
					        </thead>
					        <tbody id="shoplist" name="shoplist">
					        </tbody>
					    </table>
					</div>
					<ul id="pagelimit_shop" name="pagelimit_shop" class="pagination page_shop" style="float: right;">
					</ul>
					<ul class="nav nav-pills pagination" style="float: left;">
						<li class="active">
							 <a href="#"><span id="ShopCount" name="ShopCount" class="badge pull-right"></span>查得商铺数量：</a>
						</li>
					</ul>
				</div>
				<div class="tab-pane" id="panel-car">
					<h3>条件筛选</h3>
			        <div class="row">
			            <div class="form-group col-lg-6">
			                <div class="input-group">
			                    <span class="input-group-addon">楼盘</span>
			                    <select id="AreaID_car" class="form-control">
			                    	<option value="">任意</option>
			                    </select>
			                </div>
			            </div>
			            <div class="form-group col-lg-6">
			                <div class="input-group">
			                    <span class="input-group-addon">车牌号</span>
			                    <input id="CarCode" type="text" class="form-control" />
			                </div>
			            </div>
			        </div>
			        <div class="row">
			        	<div class="form-group col-lg-6">
			                <div class="input-group">
			                    <span class="input-group-addon">车主姓名</span>
			                    <input id="Name_car" type="text" class="form-control" />
			                </div>
			            </div>
			            <div class="form-group col-lg-6">
			                <div class="input-group">
			                    <span class="input-group-addon">电话号码</span>
			                    <input id="TEL_car" type="text" class="form-control" />
			                </div>
			            </div>
			        </div>
			        <div class="row">
						<div class="form-group col-lg-12">
			        		<button id="doquery_car" class="btn btn-success btn-block">查询</button>
			        </div>
			        </div>
					<div class="table-responsive">
					    <table class="table table-striped ">
					        <thead>
					            <tr>
					                <th class="col-lg-2">楼盘名称</th>
					                <th class="col-lg-2">车牌号</th>
					                <th class="col-lg-3">车主姓名</th>
					                <th class="col-lg-2">电话号码</th>
					                <th class="col-lg-3">操作</th>
					            </tr>
					        </thead>
					        <tbody id="carlist" name="carlist">
					        </tbody>
					    </table>
					</div>
					<ul id="pagelimit_car" name="pagelimit_car" class="pagination page_car" style="float: right;">
					</ul>
					<ul class="nav nav-pills pagination" style="float: left;">
						<li class="active">
							 <a href="#"><span id="CarCount" name="CarCount" class="badge pull-right"></span>查得车辆数量：</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</body>
	<script>
		var search_AreaID_household = '', search_AreaID_shop = '', search_AreaID_car = '', 
			search_BuildingID = '', search_RoomCode = '', search_square = '',
			search_ShopName = '', search_CarCode = '',
			search_Name_household = '', search_Name_shop = '', search_Name_car = '', 
			search_TEL_household = '', search_TEL_shop = '', search_TEL_car = '', 
			page_household = 1, page_shop = 1, page_car = 1;
		// 获取住户列表并显示
		function SetHouseHoldListShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './Payment/GetHouseHoldList.php',
	         		type : "post",
	         		data : {Page:page_household, aid:search_AreaID_household, bid:search_BuildingID, roomcode:search_RoomCode, name:search_Name_household, tel:search_TEL_household, square:search_square},
	        		async : false,
    			}
    		).responseText;
    		// 获取传回的json，根据json设置住户列表与翻页显示
			if (ret != '')
			{
				var obj_ret = JSON.parse(ret);
				$('#householdlist').html(obj_ret['Res']);
				$('#HouseHoldCount').text(obj_ret['HouseHoldCount']);
				$('#pagelimit_household').html(obj_ret['PageLimit']);
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
			// 为每个翻页按钮绑定事件
			$('.page_household li').each(function(){
				// 单击按钮时获取按钮字符串并传入翻页页面
				$(this).bind('click', function(){
					page_household = $(this).children()[0].innerText;
					SetHouseHoldListShow();
				});
			});
			// 为每个住户收费打票按钮添加事件
			$('.charge_household').each(function(){
				$(this).bind('click', function(){
					var hid = $(this).parent().attr('id');
					$('#mainview').load('./Payment/Pay_HouseHold.php?HID=' + hid);
				});
			});
		}
		// 住户列表查询按钮
		$('#doquery_household').bind('click', function(){
			// 查询赋值
			search_AreaID_household = $('#AreaID_household').val();
			search_BuildingID = $('#BID').val();
			search_RoomCode = $('#RoomCode').val();
			search_Name_household = $('#Name_household').val();
			search_TEL_household = $('#TEL_household').val();
			search_square = $('#square').val();
			SetHouseHoldListShow();
		});
		// 改变住户楼盘选中
		$('#AreaID_household').bind('change', function(){
			var aid = $('#AreaID_household').val();
			var ret_buildinglist = $.ajax
			(
				{
	        		url : './AreaManagement/GetBuildingList_select.php',
	         		type : "post",
	         		data : {areaid:aid},
	        		async : false,
    			}
    		).responseText;
    		$('#BID').html('<option value="">任意</option>');
    		$('#BID').html($('#BID').html() + JSON.parse(ret_buildinglist));
		});
		// 获取商铺列表并显示
		function SetShopListShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './Payment/GetShopList.php',
	         		type : "post",
	         		data : {Page:page_shop, aid:search_AreaID_shop, shopname:search_ShopName, name:search_Name_shop, tel:search_TEL_shop},
	        		async : false,
    			}
    		).responseText;
    		// 获取传回的json，根据json设置商铺列表与翻页显示
			if (ret != '')
			{
				var obj_ret = JSON.parse(ret);
				$('#shoplist').html(obj_ret['Res']);
				$('#ShopCount').text(obj_ret['ShopCount']);
				$('#pagelimit_shop').html(obj_ret['PageLimit']);
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
			// 为每个翻页按钮绑定事件
			$('.page_shop li').each(function(){
				// 单击按钮时获取按钮字符串并传入翻页页面
				$(this).bind('click', function(){
					page_shop = $(this).children()[0].innerText;
					SetShopListShow();
				});
			});
			// 为每个商铺收费打票添加事件
			$('.charge_shop').each(function(){
				$(this).bind('click', function(){
					var sid = $(this).parent().attr('id');
					$('#mainview').load('./Payment/Pay_Shop.php?SID=' + sid);
				});
			});
		}
		// 商铺列表查询按钮
		$('#doquery_shop').bind('click', function(){
			// 查询赋值
			search_AreaID_shop = $('#AreaID_shop').val();
			search_ShopName = $('#ShopName').val();
			search_Name_shop = $('#Name_shop').val();
			search_TEL_shop = $('#TEL_shop').val();
			SetShopListShow();
		});
		// 获取车辆列表并显示
		function SetCarListShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './Payment/GetCarList.php',
	         		type : "post",
	         		data : {Page:page_car, aid:search_AreaID_car, carcode:search_CarCode, name:search_Name_car, tel:search_TEL_car},
	        		async : false,
    			}
    		).responseText;
    		// 获取传回的json，根据json设置车辆列表与翻页显示
			if (ret != '')
			{
				var obj_ret = JSON.parse(ret);
				$('#carlist').html(obj_ret['Res']);
				$('#CarCount').text(obj_ret['CarCount']);
				$('#pagelimit_car').html(obj_ret['PageLimit']);
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
			// 为每个翻页按钮绑定事件
			$('.page_car li').each(function(){
				// 单击按钮时获取按钮字符串并传入翻页页面
				$(this).bind('click', function(){
					page_car = $(this).children()[0].innerText;
					SetCarListShow();
				});
			});
			// 为每个仅车辆缴费添加事件
			$('.charge_car').each(function(){
				$(this).bind('click', function(){
					var cid = $(this).parent().attr('id');
					$('#mainview').load('./Payment/Pay_Car.php?CID=' + cid);
				});
			});
		}
		// 车辆列表查询按钮
		$('#doquery_car').bind('click', function(){
			// 查询赋值
			search_AreaID_car = $('#AreaID_car').val();
			search_CarCode = $('#CarCode').val();
			search_Name_car = $('#Name_car').val();
			search_TEL_car = $('#TEL_car').val();
			SetCarListShow();
		});
		$(document).ready(function(){
			var ret_arealist = $.ajax
			(
				{
	        		url : './AreaManagement/GetUserAreaList.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		$('#AreaID_household').html($('#AreaID_household').html() + JSON.parse(ret_arealist));
    		$('#AreaID_shop').html($('#AreaID_shop').html() + JSON.parse(ret_arealist));
    		$('#AreaID_car').html($('#AreaID_car').html() + JSON.parse(ret_arealist));
			SetHouseHoldListShow();
			SetShopListShow();
			SetCarListShow();
		});
	</script>
</html>