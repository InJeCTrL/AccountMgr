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
	// 未登录
	if (!isset($_SESSION['Online']) || $_SESSION['Online'] == 0)
	{	
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
		    	账目清单
		    </li>
		</ol>
		<h3>条件筛选</h3>
        <div class="row">
        	<div class="form-group col-lg-2">
                <div class="input-group">
                    <span class="input-group-addon">缴费年份</span>
                    <select id="Year" class="form-control">
                    	<option value="">任意</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-lg-2">
                <div class="input-group">
                    <span class="input-group-addon">缴费月份</span>
                    <select id="Month" class="form-control">
                    	<option value="">任意</option>
                    <?php
                    	for ($i = 1; $i <= 12; $i++)
						{
							echo '<option value="' . $i . '">' . $i . '月</option>';
						}
                    ?>
                    </select>
                </div>
            </div>
            <div class="form-group col-lg-2">
                <div class="input-group">
                    <span class="input-group-addon">缴费日期</span>
                    <select id="Day" class="form-control">
                    	<option value="">任意</option>
                    <?php
                    	for ($i = 1; $i <= 31; $i++)
						{
							echo '<option value="' . $i . '">' . $i . '日</option>';
						}
                    ?>
                    </select>
                </div>
            </div>
            <div class="form-group col-lg-3">
                <div class="input-group">
                    <span class="input-group-addon">缴费类型</span>
                    <select id="Type" class="form-control">
                    	<option value="">任意</option>
                    	<option value="0">住户</option>
                    	<option value="1">住户-车辆混合</option>
                    	<option value="2">商铺</option>
                    	<option value="3">商铺-车辆混合</option>
                    	<option value="4">单独车辆</option>
                    </select>
                </div>
            </div>
            <div class="form-group col-lg-3">
                <div class="input-group">
                    <span class="input-group-addon">缴费人姓名</span>
                    <input id="Name" type="text" class="form-control" />
                </div>
            </div>
        </div>
        <div class="row">
        	<div class="form-group col-lg-4">
                <div class="input-group">
                    <span class="input-group-addon">缴费人电话号码</span>
                    <input id="Tel" type="text" class="form-control" />
                </div>
            </div>
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
                    <span class="input-group-addon">缴费目标补充</span>
                    <input id="AddTarget" type="text" class="form-control" />
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
		            	<th class="col-lg-1">缴费时间</th>
		                <th class="col-lg-1">缴费类型</th>
		                <th class="col-lg-1">缴费人姓名</th>
		                <th class="col-lg-2">缴费人电话号码</th>
		                <th class="col-lg-4">缴费目标</th>
		                <th class="col-lg-2">缴费内容</th>
		                <th class="col-lg-1">操作</th>
		            </tr>
		        </thead>
		        <tbody id="accountlist" name="accountlist">
		        </tbody>
		    </table>
		</div>
		<ul id="pagelimit" name="pagelimit" class="pagination page" style="float: right;">
		</ul>
		<ul class="nav nav-pills pagination" style="float: left;">
			<li class="active">
				 <a href="#"><span id="AccountCount" name="AccountCount" class="badge pull-right"></span>查得账目数量：</a>
			</li>
		</ul>
	</body>
	<script>
		var search_Year = '', search_Month = '', search_Day = '',
			search_Type = '', search_Name = '', 
			search_TEL = '', search_AreaID = '', search_AddTarget = '',
			page = 1;
		// 删除指定ID的账目
		function deletePayment(pid, type, cascade)
		{
			var ret = $.ajax
			(
				{
	        		url : './Payment/deletePayment.php',
	         		type : "post",
	         		data : {PID:pid, Type:type, Cascade:cascade},
	        		async : false,
    			}
    		).responseText;
    		// 删除账目
			if (ret != '')
			{
				// 没有有效删除
				if (ret === '0')
				{
					alert('账目删除失败！');
				}
				else
				{
					alert('账目已删除！');
				}
				SetAccountListShow();
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
		}
		// 获取账目清单列表并显示
		function SetAccountListShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './Payment/GetAccountList.php',
	         		type : "post",
	         		data : {Page:page, year:search_Year, month:search_Month, day:search_Day, type:search_Type, name:search_Name, tel:search_TEL, aid:search_AreaID, addtarget:search_AddTarget},
	        		async : false,
    			}
    		).responseText;
    		// 获取传回的json，根据json设置账目清单列表与翻页显示
			if (ret != '')
			{
				var obj_ret = JSON.parse(ret);
				$('#accountlist').html('');
				for (var i = 0; i < obj_ret['Res'].length; i++)
				{
					var strType = "";
					switch (obj_ret['Res'][i][2])
					{
						case 0:
							strType = "住户";
							break;
						case 1:
							strType = "住户-车辆混合";
							break;
						case 2:
							strType = "商铺";
							break;
						case 3:
							strType = "商铺-车辆混合";
							break;
						case 4:
							strType = "单独收费车辆";
							break;
					}
					$('#accountlist').html($('#accountlist').html() + '<tr><td>' + obj_ret['Res'][i][1] + '</td>' + 
											'<td>' + strType + '</td>' + 
											'<td>' + obj_ret['Res'][i][3] + '</td>' + 
											'<td>' + obj_ret['Res'][i][4] + '</td>' + 
											'<td>' + obj_ret['Res'][i][5] + '</td>' + 
											'<td>' + obj_ret['Res'][i][6] + '</td>' + 
											'<td><div value="' + obj_ret['Res'][i][2] + '" id=' + obj_ret['Res'][i][0] + " class='btn-group'>" +  
											<?php echo (($_SESSION['Type'] === '超级管理员') ? "\"<a href='#' class='btn btn-danger del'>删除</a>\"" : "\"\""); ?> +  
                    						"</div></td></tr>");
				}
				$('#AccountCount').text(obj_ret['AccountCount']);
				$('#pagelimit').html(obj_ret['PageLimit']);
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
			// 为每个翻页按钮绑定事件
			$('.page li').each(function(){
				// 单击按钮时获取按钮字符串并传入翻页页面
				$(this).bind('click', function(){
					page = $(this).children()[0].innerText;
					SetAccountListShow();
				});
			});
			// 为每个删除按钮添加事件
			$('.del').each(function(){
				$(this).bind('click', function(){
					if (confirm('请谨慎操作！\n确认是否删除？\n'))
					{
						var type = $(this).parent().attr('value');
						var pid = $(this).parent().attr('id');
						if (type == 1 || type == 3)
						{
							if (confirm('请谨慎操作！\n是否级联删除混合账目中的车辆账目？\n'))
							{
								deletePayment(pid, type, 1);
							}
							else
							{
								deletePayment(pid, type, 0);
							}
						}
						else
						{
							deletePayment(pid, type, 0);
						}
					}
				});
			});
		}
		// 账目清单列表查询按钮
		$('#doquery').bind('click', function(){
			// 查询赋值
			search_Year = $('#Year').val();
			search_Month = $('#Month').val();
			search_Day = $('#Day').val();
			search_Type = $('#Type').val();
			search_Name = $('#Name').val();
			search_TEL = $('#Tel').val();
			search_AreaID = $('#AreaID').val();
			search_AddTarget = $('#AddTarget').val();
			SetAccountListShow();
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
    		var ret_yearlist = $.ajax
			(
				{
	        		url : './Payment/GetUserAccountYear.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		$('#Year').html($('#Year').html() + JSON.parse(ret_yearlist));
			SetAccountListShow();
		});
	</script>
</html>