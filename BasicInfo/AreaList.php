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
		    	楼盘列表
		    </li>
		</ol>
        <h3>条件筛选</h3>
        <div class="row">
            <div class="form-group col-lg-12">
                <div class="input-group">
                    <span class="input-group-addon">楼盘名称</span>
                    <input id="AreaName" class="form-control" type="text">
                </div>
            </div>
        </div>
        <div class="row">
        	<div class="form-group col-lg-6">
        		<button id="doquery" class="btn btn-success btn-block">查询</button>
        	</div>
        	<div class="form-group col-lg-6">
        		<button id="newarea" class="btn btn-primary btn-block">新增楼盘</button>
        	</div>
        </div>
		<div class="table-responsive">
		    <table class="table table-striped ">
		        <thead>
		            <tr>
		            	<th class="col-lg-1"><input type="checkbox" id="chkall" /></th>
		                <th class="col-lg-8">楼盘名称</th>
		                <th class="col-lg-3">操作</th>
		            </tr>
		        </thead>
		        <tbody id="arealist" name="arealist">
		        </tbody>
		    </table>
		</div>
		<div class="row">
        	<div class="form-group col-lg-3">
        		<div class="input-group">
                    <span class="input-group-addon">批量操作：</span>
                    <button id="multidel" class="btn btn-danger btn-block">删除</button>
               </div>
        	</div>
        </div>
		<ul id="pagelimit" name="pagelimit" class="pagination" style="float: right;">
		</ul>
		<ul class="nav nav-pills pagination" style="float: left;">
			<li class="active">
				 <a href="#"><span id="AreaCount" name="AreaCount" class="badge pull-right"></span>查得楼盘数量：</a>
			</li>
		</ul>
	</body>
	<script>
		var search_AreaName = '', page = 1;
		// 删除指定ID的用户账号
		function deleteArea(aid)
		{
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/deleteArea.php',
	         		type : "post",
	         		data : {AID:aid},
	        		async : false,
    			}
    		).responseText;
    		// 删除楼盘
			if (ret != '')
			{
				// 没有有效删除
				if (ret === '0')
				{
					alert('楼盘删除失败！');
				}
				else
				{
					alert('楼盘已删除！');
				}
				SetAreaListShow();
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
		}
		// 获取楼盘列表并显示
		function SetAreaListShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './BasicInfo/GetAreaList_tbl.php',
	         		type : "post",
	         		data : {Page:page, name:search_AreaName},
	        		async : false,
    			}
    		).responseText;
    		// 获取传回的json，根据json设置楼盘列表与翻页显示
			if (ret != '')
			{
				var obj_ret = JSON.parse(ret);
				$('#arealist').html(obj_ret['Res']);
				$('#AreaCount').text(obj_ret['AreaCount']);
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
					SetAreaListShow();
				});
			});
			// 为每个删除按钮添加事件
			$('.del').each(function(){
				$(this).bind('click', function(){
					if (confirm('请谨慎操作！\n删除楼盘会级联删除其下属的所有其它数据！\n确认批量删除选中的楼盘？'))
					{
						var aid = $(this).parent().attr('id');
						deleteArea(aid);
					}
				});
			});
			// 为每个查看修改按钮添加事件
			$('.mdf').each(function(){
				$(this).bind('click', function(){
					var aid = $(this).parent().attr('id');
					$('#mainview').load('./BasicInfo/ShowArea.php?AreaID=' + aid);
				});
			});
		}
		// 全选
		$('#chkall').bind('click', function(){
			$('.chksel').each(function(){
				$(this).prop('checked', $('#chkall').is(':checked'));
				$(this).attr('checked', $('#chkall').is(':checked'));
			});
		});
		// 批量删除
		$('#multidel').bind('click', function(){
			if (confirm('请谨慎操作！\n删除楼盘会级联删除其下属的所有其它数据！\n确认批量删除选中的楼盘？'))
			{
				$('.chksel').each(function(){
					// 获取选中行的ID并提交删除
					if ($(this).is(':checked') == true)
					{
						var aid = $(this).parent().parent().children().eq(2).children().eq(0).attr('id');
						deleteArea(aid);
					}
				});
			}
		});
		// 查询按钮
		$('#doquery').bind('click', function(){
			// 查询赋值
			search_AreaName = $('#AreaName').val();
			SetAreaListShow();
		});
		// 新增楼盘
		$('#newarea').bind('click', function(){
			$('#mainview').load('./BasicInfo/AddArea.php');
		});
		$(document).ready(function(){
			SetAreaListShow();
		});
	</script>
</html>