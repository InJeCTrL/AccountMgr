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
	// 不是超级管理员，强制注销
	if ($_SESSION['Type'] != '超级管理员')
	{
		SignOut($conn, $_SESSION['UserID'], $_SESSION['UserID'], '强制注销-低权限访问数据恢复');
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
		    	系统维护
		    </li>
		    <li class="active">
		    	数据恢复
		    </li>
		</ol>
        <h3 style="text-align: center;">这个操作会将ZIP压缩文件中的CSV文件内容恢复到数据库中</h3>
        <h4 style="text-align: center;">与当前数据库重复(冲突)的数据将被更新/覆盖</h4>
        <div class="row">
        	<div class="form-group col-lg-12">
        		<div class="progress progress-striped active">
					<div id="RecProgress" aria-valuemin = "0.00" aria-valuemax = "100.00" aria-valuenow = "0.00" style="width: 0%;" class="progress-bar progress-bar-primary" role="progressbar">
						0.00%
					</div>
				</div>
        	</div>
        	<form id="upform" name="upform" enctype="multipart/form-data" class="form-group col-lg-12">
        		<input id="upfile" name="upfile" type="file" accept="application/x-zip-compressed" />	
        	</form>
        </div>
        <div class="row">
        	<div class="form-group col-lg-12">
        		<button id="uploadrec" class="btn btn-primary btn-block">上传备份文件</button>
        	</div>
        	<div class="form-group col-lg-12">
        		<button id="dorec" class="btn btn-success btn-block">恢复数据到数据库</button>
        	</div>
        </div>
	</body>
	<script>
		// 需要恢复的数据表数量
		var num_Table = 0;
		// 上传备份文件
		$('#uploadrec').bind('click', function(){
			$('#RecProgress').css('width', '0%').attr('class', 'progress-bar progress-bar-primary').attr('aria-valuenow', '0.00').text('0.00%');
			// 实例化FormData对象，获取form表单内的文件对象
			var FD = new FormData($('#upform')[0]);
			$.ajax({
				url:"./SysSetting/UploadRec.php",
				type:"post",
				data:FD,
				processData:false,
				contentType:false,
				xhr:function(){
					var xhr = new XMLHttpRequest();
					xhr.upload.addEventListener('progress', function(e){
						$('#RecProgress').css('width', (e.loaded / e.total * 100) + '%').attr('aria-valuenow', (e.loaded / e.total * 100)).text((e.loaded / e.total * 100) + '%');
					});
					return xhr;
				},
			}).error(function(){
				alert('上传失败！');
			}).success(function(data){
				//alert(data);
			});
		});
		// 点击恢复按钮
		$('#dorec').bind('click', function(){
			$('#RecProgress').css('width', '0%').attr('class', 'progress-bar progress-bar-success').attr('aria-valuenow', '0.00').text('0.00%');
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
	    			var change = parseFloat(now) + obj_ret_progress['increment'];
	    			$('#BakProgress').css('width', change + '%').attr('aria-valuenow', change).text(change + '%');
	    		}
	    		// 最后一个表备份完成
	    		else
	    		{
	    			$('#BakProgress').css('width', '100%').attr('aria-valuenow', '100.00').text('备份完成');
					window.location.href = './SysSetting/' + obj_ret_progress['link'];
	    		}
    		}
		});
	</script>
</html>