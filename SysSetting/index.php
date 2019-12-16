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
		    	系统维护
		    </li>
		    <li class="active">
		    	数据备份
		    </li>
		</ol>
        <h2>当前数据负载情况</h2>
        <div class="table-responsive">
		    <table class="table table-striped ">
		        <thead>
		            <tr>
		                <th class="col-lg-3">数据表名称</th>
		                <th class="col-lg-3">描述</th>
		                <th class="col-lg-3">数据总行数</th>
		                <th class="col-lg-3">占比</th>
		            </tr>
		        </thead>
		        <tbody id="tablelist" name="tablelist">
		        </tbody>
		    </table>
		</div>
        <h3 style="text-align: center;">所有表中的数据将备份到ZIP压缩文件中的CSV文件内</h3>
        <div class="row">
        	<div class="form-group col-lg-12">
        		<div class="progress progress-striped active">
					<div id="BakProgress" aria-valuemin = "0.00" aria-valuemax = "100.00" aria-valuenow = "0.00" style="width: 0%;" class="progress-bar progress-success" role="progressbar">
						0.00%
					</div>
				</div>
        	</div>
        </div>
        <div class="row">
        	<div class="form-group col-lg-12">
        		<button id="dobak" class="btn btn-success btn-block">备份数据到本地</button>
        	</div>
        </div>
	</body>
	<script>
		// 数据库中数据表的数量
		var num_Table = 0;
		// 获取数据表列表并显示
		function SetTableListShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './SysSetting/GetTableList.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		// 获取传回的json，根据json设置数据表列表
			if (ret != '')
			{
				var obj_ret = JSON.parse(ret);
				$('#tablelist').html(obj_ret['Res']);
				num_Table = obj_ret['num'];
			}
			// 返回为空则认为用户下线，刷新页面
			else
			{
				window.location.reload();
			}
		}
		// 点击备份按钮
		$('#dobak').bind('click', function(){
			// 每个表执行一次（多次生成数据表备份文件）
			for (var i = 0; i < num_Table; i++)
			{
				var ret_progress = $.ajax
				(
					{
		        		url : './SysSetting/doBak.php',
		         		type : "post",
		         		data : {i_tbl:i, count_tbl:num_Table},
		        		async : false,
	    			}
	    		).responseText;
	    		var obj_ret_progress = JSON.parse(ret_progress);
	    		// 尚未备份到最后一个表
	    		if (obj_ret_progress['increment'] != -1)
	    		{
	    			var now = $('#BakProgress').attr('aria-valuenow');
	    			$('#BakProgress').css('width', (now + obj_ret_progress['increment']) + '%').attr('aria-valuenow', (now + obj_ret_progress['increment'])).text((now + obj_ret_progress['increment']) + '%');
	    		}
	    		// 最后一个表备份完成
	    		else
	    		{
	    			$('#BakProgress').css('width', '100%').attr('aria-valuenow', '100.00').text('备份完成');
	    			// obj_ret_progress['link']
	    		}
    		}
		});
		$(document).ready(function(){
			$('#BakProgress').css('width', '0%').attr('aria-valuenow', '0.00').text('0.00%');
			SetTableListShow();
		});
	</script>
</html>