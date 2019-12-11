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
		    	楼盘管辖
		    </li>
		    <li class="active">
		    	楼栋列表
		    </li>
		</ol>
        <h3>条件筛选</h3>
        <div class="row">
            <div class="form-group col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon">楼盘</span>
                    <select id="AreaID" class="form-control">
                    	<option value="">任意</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-lg-6">
                <div class="input-group">
                    <span class="input-group-addon">楼栋号</span>
                    <input id="Bno" class="form-control" type="text">
                </div>
            </div>
        </div>
        <div class="row">
        	<?php
        		// 管理员及以上权限可新增楼栋
        		if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
				{
        	?>
        	<div class="form-group col-lg-6">
        		<button id="doquery" class="btn btn-success btn-block">查询</button>
        	</div>
        	<div class="form-group col-lg-6">
        		<button id="newbuilding" class="btn btn-primary btn-block">新增楼栋</button>
        	</div>
        	<?php
        		}
				else
				{
			?>
			<div class="form-group col-lg-12">
        		<button id="doquery" class="btn btn-success btn-block">查询</button>
        	</div>
			<?php
				}
        	?>
        </div>
		<div class="table-responsive">
		    <table class="table table-striped ">
		        <thead>
		            <tr>
		            	<th class="col-lg-1"><input type="checkbox" id="chkall" /></th>
		                <th class="col-lg-4">楼盘名称</th>
		                <th class="col-lg-4">楼栋号</th>
		                <th class="col-lg-3">操作</th>
		            </tr>
		        </thead>
		        <tbody id="buildinglist" name="buildinglist">
		        </tbody>
		    </table>
		</div>
		<?php
    		// 管理员及以上权限可批量操作
    		if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
			{
    	?>
		<div class="row">
        	<div class="form-group col-lg-3">
        		<div class="input-group">
                    <span class="input-group-addon">批量操作：</span>
                    <button id="multidel" class="btn btn-danger btn-block">删除</button>
               	</div>
        	</div>
        </div>
        <?php
			}
        ?>
		<ul id="pagelimit" name="pagelimit" class="pagination" style="float: right;">
		</ul>
		<ul class="nav nav-pills pagination" style="float: left;">
			<li class="active">
				 <a href="#"><span id="BuildingCount" name="BuildingCount" class="badge pull-right"></span>查得楼栋数量：</a>
			</li>
		</ul>
	</body>
	<script>
		var search_AreaID = '', search_BNo = '', page = 1;
		// 删除指定ID的楼栋
		function deleteBuilding(bid)
		{
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/deleteBuilding.php',
	         		type : "post",
	         		data : {BID:bid},
	        		async : false,
    			}
    		).responseText;
    		// 删除楼栋
			if (ret != '')
			{
				// 没有有效删除
				if (ret === '0')
				{
					alert('楼栋删除失败！');
				}
				else
				{
					alert('楼栋已删除！');
				}
				SetBuildingListShow();
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
		}
		// 获取楼栋列表并显示
		function SetBuildingListShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/GetBuildingList.php',
	         		type : "post",
	         		data : {Page:page, aid:search_AreaID, bno:search_BNo},
	        		async : false,
    			}
    		).responseText;
    		// 获取传回的json，根据json设置楼栋列表与翻页显示
			if (ret != '')
			{
				var obj_ret = JSON.parse(ret);
				$('#buildinglist').html(obj_ret['Res']);
				$('#BuildingCount').text(obj_ret['BuildingCount']);
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
					SetBuildingListShow();
				});
			});
			// 为每个删除按钮添加事件
			$('.del').each(function(){
				$(this).bind('click', function(){
					if (confirm('请谨慎操作！\n删除楼栋会级联删除其下属的所有其它数据！\n确认批量删除选中的楼栋？'))
					{
						var bid = $(this).parent().attr('id');
						deleteBuilding(bid);
					}
				});
			});
			// 为每个查看修改按钮添加事件
			$('.mdf').each(function(){
				$(this).bind('click', function(){
					var bid = $(this).parent().attr('id');
					$('#mainview').load('./AreaManagement/ShowBuilding.php?BID=' + bid);
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
			if (confirm('请谨慎操作！\n删除楼栋会级联删除其下属的所有其它数据！\n确认批量删除选中的楼栋？'))
			{
				$('.chksel').each(function(){
					// 获取选中行的ID并提交删除
					if ($(this).is(':checked') == true)
					{
						var bid = $(this).parent().parent().children().eq(3).children().eq(0).attr('id');
						deleteBuilding(bid);
					}
				});
			}
		});
		// 查询按钮
		$('#doquery').bind('click', function(){
			// 查询赋值
			search_AreaID = $('#AreaID').val();
			search_BNo = $('#Bno').val();
			SetBuildingListShow();
		});
		// 新增楼栋
		$('#newbuilding').bind('click', function(){
			$('#mainview').load('./AreaManagement/AddBuilding.php');
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
    		$('#AreaID').html($('#AreaID').html() + JSON.parse(ret_arealist));
			SetBuildingListShow();
		});
	</script>
</html>