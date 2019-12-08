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
		    	操作日志
		    </li>
		</ol>
        <h3>条件筛选</h3>
        <div class="row">
            <div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">时间</span>
                    <select id="Time" class="form-control">
                    	<option value="">任意</option>
                    	<option value="0">当天</option>
                    	<option value="1">最近三天</option>
                    	<option value="2">最近七天</option>
                    	<option value="3">最近三十天</option>
                    	<option value="4">最近九十天</option>
                    	<option value="5">最近180天</option>
                    	<option value="6">最近一年</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">远程地址</span>
                    <input id="IP" class="form-control" type="text">
                </div>
            </div>
            <div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">操作者姓名</span>
                    <input id="OpName" class="form-control" type="text">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-lg-5">
                <div class="input-group">
                    <span class="input-group-addon">操作者身份证号码</span>
                    <input id="UID" class="form-control" type="text">
                </div>
            </div>
            <div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">模块/单元名称</span>
                    <select id="ModName" class="form-control">
                    	<option value="">任意</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-lg-3">
                <div class="input-group">
                    <span class="input-group-addon">涉及表名</span>
                    <select id="tblName" class="form-control">
                    	<option value="">任意</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
        	<div class="form-group col-lg-12">
        		<div class="input-group">
        			<span class="input-group-addon">行为</span>
        			<input id="Action" class="form-control" type="text" />
        		</div>
        	</div>
        </div>
        <div class="row">
        	<div class="form-group col-lg-12">
        		<button id="doquery" class="btn btn-success btn-block">查询</button>
        	</div>
        </div>
		<div class="table-responsive">
		    <table class="table table-striped ">
		        <thead>
		            <tr>
		            	<th class="col-lg-1">时间</th>
		                <th class="col-lg-1">远程地址</th>
		                <th class="col-lg-1">操作者姓名</th>
		                <th class="col-lg-2">操作者身份证号码</th>
		                <th class="col-lg-2">模块/单元</th>
		                <th class="col-lg-3">行为</th>
		                <th class="col-lg-1">涉及表名</th>
		                <th class="col-lg-1">涉及行ID</th>
		            </tr>
		        </thead>
		        <tbody id="Loglist" name="Loglist">
		        </tbody>
		    </table>
		</div>
		<ul id="pagelimit" name="pagelimit" class="pagination" style="float: right;">
		</ul>
		<ul class="nav nav-pills pagination" style="float: left;">
			<li class="active">
				 <a href="#"><span id="LogCount" name="LogCount" class="badge pull-right"></span>查得日志数量：</a>
			</li>
		</ul>
	</body>
	<script>
		var search_Time = '', search_IP = '', search_OpName = '',
	        search_UID = '', search_ModName = '', search_tblName = '',
	        search_Action = '', page = 1;
		// 获取日志列表并显示
		function SetLogListShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/GetLogList.php',
	         		type : "post",
	         		data : {Page:page, time:search_Time, ip:search_IP, opname:search_OpName,
	         				uid:search_UID, modname:search_ModName, tblName:search_tblName,
	         				action:search_Action},
	        		async : false,
    			}
    		).responseText;
    		// 获取传回的json，根据json设置日志列表与翻页显示
			if (ret != '')
			{
				var obj_ret = JSON.parse(ret);
				$('#Loglist').html(obj_ret['Res']);
				$('#LogCount').text(obj_ret['LogCount']);
				$('#pagelimit').html(obj_ret['PageLimit']);
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
			// 为每个翻页按钮绑定事件
			$('.pagination li').each(function(){
				// 单击按钮时获取按钮字符串并传入翻页页面
				$(this).bind('click', function(){
					page = $(this).children()[0].innerText;
					SetLogListShow();
				});
			});
		}
		// 查询按钮
		$('#doquery').bind('click', function(){
			// 查询赋值
			search_Time = $('#Time').val();
			search_IP = $('#IP').val();
			search_OpName = $('#OpName').val();
	        search_UID = $('#UID').val();
	        search_ModName = $('#ModName').val();
	        search_tblName = $('#tblName').val();
	        search_Action = $('#Action').val();
			SetLogListShow();
		});
		$(document).ready(function(){
			SetLogListShow();
			var ret_modname = $.ajax
			(
				{
	        		url : './BasicInfo/GetModNameList.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		$('#ModName').html($('#ModName').html() + JSON.parse(ret_modname));
    		var ret_tblname = $.ajax
			(
				{
	        		url : './BasicInfo/GetTblList.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		$('#tblName').html($('#tblName').html() + JSON.parse(ret_tblname));
		});
	</script>
</html>