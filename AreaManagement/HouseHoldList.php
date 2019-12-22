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
		    	住户信息
		    </li>
		</ol>
        <h3>条件筛选</h3>
        <div class="row">
            <div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">楼盘</span>
                    <select id="AreaID" class="form-control">
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
                    <input id="Name" type="text" class="form-control" />
                </div>
            </div>
            <div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">电话号码</span>
                    <input id="TEL" type="text" class="form-control" />
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
        	<?php
        		// 管理员及以上权限可新增住户
        		if ($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员')
				{
        	?>
        	<div class="form-group col-lg-6">
        		<button id="doquery" class="btn btn-success btn-block">查询</button>
        	</div>
        	<div class="form-group col-lg-6">
        		<button id="newhousehold" class="btn btn-primary btn-block">新增住户</button>
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
		                <th class="col-lg-2">楼盘名称</th>
		                <th class="col-lg-2">楼栋号</th>
		                <th class="col-lg-2">门牌号</th>
		                <th class="col-lg-2">住户姓名</th>
		                <th class="col-lg-3">操作</th>
		            </tr>
		        </thead>
		        <tbody id="householdlist" name="householdlist">
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
				 <a href="#"><span id="HouseHoldCount" name="HouseHoldCount" class="badge pull-right"></span>查得住户数量：</a>
			</li>
		</ul>
	</body>
	<script>
		var search_AreaID = '', search_BuildingID = '', search_RoomCode = '',
			search_Name = '', search_TEL = '', search_square = '',
			page = 1;
		// 删除指定ID的住户
		function deleteHouseHold(hid)
		{
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/deleteHouseHold.php',
	         		type : "post",
	         		data : {HID:hid},
	        		async : false,
    			}
    		).responseText;
    		// 删除住户
			if (ret != '')
			{
				// 没有有效删除
				if (ret === '0')
				{
					alert('住户删除失败！');
				}
				else
				{
					alert('住户已删除！');
				}
				SetHouseHoldListShow();
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
		}
		// 获取住户列表并显示
		function SetHouseHoldListShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/GetHouseHoldList.php',
	         		type : "post",
	         		data : {Page:page, aid:search_AreaID, bid:search_BuildingID, roomcode:search_RoomCode, name:search_Name, tel:search_TEL, square:search_square},
	        		async : false,
    			}
    		).responseText;
    		// 获取传回的json，根据json设置住户列表与翻页显示
			if (ret != '')
			{
				var obj_ret = JSON.parse(ret);
				$('#householdlist').html('');
				for (var i = 0; i < obj_ret['Res'].length; i++)
				{
					$('#householdlist').html($('#householdlist').html() + "<tr><td><input type='checkbox' class='chksel' /></td>" + 
											'<td>' + obj_ret['Res'][i][1] + '</td>' + 
											'<td>' + obj_ret['Res'][i][2] + '</td>' + 
											'<td>' + obj_ret['Res'][i][3] + '</td>' + 
											'<td>' + obj_ret['Res'][i][4] + '</td>' + 
											'<td><div id=' + obj_ret['Res'][i][0] + " class='btn-group'>" + 
											"<a href='#' class='btn btn-primary mdf'>" + <?php echo (($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员') ? "'查看/修改'" : "'查看'"); ?> + "</a>" + 
											<?php echo (($_SESSION['Type'] === '超级管理员' || $_SESSION['Type'] === '管理员') ? "\"<a href='#' class='btn btn-danger del'>删除</a>\"" : "\"\""); ?> +  
                    						"</div></td></tr>");
				}
				$('#HouseHoldCount').text(obj_ret['HouseHoldCount']);
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
					SetHouseHoldListShow();
				});
			});
			// 为每个删除按钮添加事件
			$('.del').each(function(){
				$(this).bind('click', function(){
					if (confirm('请谨慎操作！\n删除住户会级联删除其下属的所有其它数据(缴费记录等)！\n确认批量删除选中的住户？'))
					{
						var hid = $(this).parent().attr('id');
						deleteHouseHold(hid);
					}
				});
			});
			// 为每个查看修改按钮添加事件
			$('.mdf').each(function(){
				$(this).bind('click', function(){
					var hid = $(this).parent().attr('id');
					$('#mainview').load('./AreaManagement/ShowHouseHold.php?HID=' + hid);
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
			if (confirm('请谨慎操作！\n删除住户会级联删除其下属的所有其它数据(缴费记录等)！\n确认批量删除选中的住户？'))
			{
				$('.chksel').each(function(){
					// 获取选中行的ID并提交删除
					if ($(this).is(':checked') == true)
					{
						var hid = $(this).parent().parent().children().eq(5).children().eq(0).attr('id');
						deleteHouseHold(hid);
					}
				});
			}
		});
		// 查询按钮
		$('#doquery').bind('click', function(){
			// 查询赋值
			search_AreaID = $('#AreaID').val();
			search_BuildingID = $('#BID').val();
			search_RoomCode = $('#RoomCode').val();
			search_Name = $('#Name').val();
			search_TEL = $('#TEL').val();
			search_square = $('#square').val();
			SetHouseHoldListShow();
		});
		// 新增住户
		$('#newhousehold').bind('click', function(){
			$('#mainview').load('./AreaManagement/AddHouseHold.php');
		});
		// 改变楼盘选中
		$('#AreaID').bind('change', function(){
			var aid = $('#AreaID').val();
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
			SetHouseHoldListShow();
		});
	</script>
</html>