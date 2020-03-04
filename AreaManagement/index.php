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
		<script type="text/javascript" src="js/echarts/echarts.min.js" ></script>
	</head>
	<body>
		<ol class="breadcrumb">
		    <li class="active">
		    	楼盘管辖
		    </li>
		    <li class="active">
		    	数据概览
		    </li>
		</ol>
		<div class="container" style="width: 100%;">
			<div class="row clearfix">
				<div class="col-md-12 column">
					<div class="jumbotron well">
						<h2>
							当前共 <label id="AreaCount"></label> 个楼盘，<label id="UserCount"></label> 名正式用户
						</h2>
						<p>
							包含 <label id="HouseHoldCount"></label> 名住户、 <label id="ShopCount"></label> 个商铺、 <label id="CarCount"></label> 台车辆
						</p>
					</div>
				</div>
			</div>
			<div class="row">
				<div id="Nodes" class="col-lg-6 jumbotron well" style="height: 500px;">
				</div>
				<div class="col-lg-6 jumbotron well" style="height: 500px;">
					<select id="UserAreaList" class="form-control">
					</select>
					<br />
					<div id="EachArea" style="height: 420px;">
					</div>
				</div>
			</div>
			<div id="User" class="row jumbotron well" style="height: 320px;">
			</div>
			<div id="EachMonthGet" class="row jumbotron well" style="height: 500px;">
			</div>
			<div id="EachMonthNot" class="row jumbotron well" style="height: 500px;">
			</div>
        </div>
	</body>
	<script>
		var obj_ret = null;
		// 设置最近六个月各楼盘未缴费个体数
		function SetMonthNotShow()
		{
			var dom_MonthNot = document.getElementById("EachMonthNot");
			var Chart_MonthNot = echarts.init(dom_MonthNot);
			var app = {};
			var option_MonthNot = null;
			setTimeout(function () {
			    option_MonthNot = {
			    	title : {
				        text: '最近六个月各楼盘未缴费个体数',
				        subtext: '未缴费个体数(住户+商铺)及占比',
				        x:'left'
				    },
			        legend: {},
			        tooltip: {
			            trigger: 'axis',
			            showContent: false
			        },
			        dataset: {
			            source: obj_ret['MonthNotSource']
			        },
			        xAxis: {type: 'category'},
			        yAxis: {gridIndex: 0},
			        grid: {top: '55%'},
			        series:
			        	obj_ret['MonthNotSeriesLines']
			    };
			
			    Chart_MonthNot.on('updateAxisPointer', function (event) {
			        var xAxisInfo = event.axesInfo[0];
			        if (xAxisInfo) {
			            var dimension = xAxisInfo.value + 1;
			            Chart_MonthNot.setOption({
			                series: {
			                    id: 'pie',
			                    label: {
			                        formatter: '{b}: {@[' + dimension + ']} ({d}%)'
			                    },
			                    encode: {
			                        value: dimension,
			                        tooltip: dimension
			                    }
			                }
			            });
			        }
			    });
			
			    Chart_MonthNot.setOption(option_MonthNot);
			
			});;
			if (option_MonthNot && typeof option_MonthNot === "object") {
			    Chart_MonthNot.setOption(option_MonthNot, true);
			}
		}
		// 设置最近六个月各楼盘收费金额
		function SetMonthGetShow()
		{
			var dom_MonthGet = document.getElementById("EachMonthGet");
			var Chart_MonthGet = echarts.init(dom_MonthGet);
			var app = {};
			var option_MonthGet = null;
			setTimeout(function () {
			    option_MonthGet = {
			    	title : {
				        text: '最近六个月各楼盘收费金额',
				        subtext: '收得金额及占比',
				        x:'left'
				    },
			        legend: {},
			        tooltip: {
			            trigger: 'axis',
			            showContent: false
			        },
			        dataset: {
			        	source: obj_ret['MonthGetSource']
			        },
			        xAxis: {type: 'category'},
			        yAxis: {gridIndex: 0},
			        grid: {top: '55%'},
			        series: obj_ret['MonthGetSeriesLines']
			    };
			
			    Chart_MonthGet.on('updateAxisPointer', function (event) {
			        var xAxisInfo = event.axesInfo[0];
			        if (xAxisInfo) {
			            var dimension = xAxisInfo.value + 1;
			            Chart_MonthGet.setOption({
			                series: {
			                    id: 'pie',
			                    label: {
			                        formatter: '{b}: {@[' + dimension + ']} ({d}%)'
			                    },
			                    encode: {
			                        value: dimension,
			                        tooltip: dimension
			                    }
			                }
			            });
			        }
			    });
			    Chart_MonthGet.setOption(option_MonthGet);
			});;
			if (option_MonthGet && typeof option_MonthGet === "object") {
			    Chart_MonthGet.setOption(option_MonthGet, true);
			}
		}
		// 设置用户数量饼状图显示
		function SetUserShow()
		{
			var dom_User = document.getElementById("User");
			var Chart_User = echarts.init(dom_User);
			var app = {};
			option_User = {
			    title : {
			        text: '用户类型',
			        x:'center'
			    },
			    tooltip : {
			        trigger: 'item',
			        formatter: "{a} <br/>{b} : {c} ({d}%)"
			    },
			    legend: {
			        orient: 'vertical',
			        left: 'left',
			        data: ['正式用户','未审核']
			    },
			    series : [
			        {
			            name: '数量',
						type: 'pie',
			            radius : '70%',
			            center: ['50%', '60%'],
			            data:[
			            	{value:obj_ret['NormalUserCount'], name:'正式用户'},
			                {value:obj_ret['RegCount'], name:'未审核'}
			            ],
			            itemStyle: {
			                emphasis: {
			                    shadowBlur: 10,
			                 	shadowOffsetX: 0,
			                    shadowColor: 'rgba(0, 0, 0, 0.5)'
			                }
		        		}
			        }
			    ]
			};
			if (option_User && typeof option_User === "object") {
			    Chart_User.setOption(option_User, true);
			}
		}
		// 设置每个楼盘缴费个体数量环状图显示
		function SetEachAreaShow()
		{
			var dom_EachArea = document.getElementById("EachArea");
			var Chart_EachArea = echarts.init(dom_EachArea);
			var app = {};
			var option_EachArea = {
				title : {
					        text: obj_ret['EachArea_Name'] + ' 的缴费个体分布情况',
					        x:'center'
					    },
			    tooltip: {
			        trigger: 'item',
			        formatter: "{a} <br/>{b}: {c} ({d}%)"
			    },
			    legend: {
			        orient: 'vertical',
			        x: 'left',
			        data:['住户','商铺','车辆']
			    },
			    series: [
			        {
			            name:'个体数',
			            type:'pie',
			            radius: ['35%', '40%'],
			            avoidLabelOverlap: false,
			            label: {
			                normal: {
			                    show: true,
			                    position: 'left',
			                    textStyle: {
			                        fontSize: '15',
			                        fontWeight: 'bold'
			                    }
			                },
			                emphasis: {
			                    show: true,
			                    textStyle: {
			                        fontSize: '20',
			                        fontWeight: 'bold'
			                    }
			                }
			            },
			            data:
			            	obj_ret['EachArea_Rate']
			            ,
			            labelLine: {
			                normal: {
			                    show: true
			                }
			            }
			        }
			    ]
			};
			if (option_EachArea && typeof option_EachArea === "object") {
			    Chart_EachArea.setOption(option_EachArea, true);
			}
		}
		// 设置各楼盘的(缴费个体)比例饼状图显示
		function SetNodesShow()
		{
			var dom_Nodes = document.getElementById("Nodes");
			var Chart_Nodes = echarts.init(dom_Nodes);
			var app = {};
			var option_Nodes = {
			    title : {
			        text: '缴费个体分布',
			        subtext: '缴费个体：住户、商铺、车辆',
			        x:'center'
			    },
			    tooltip : {
			        trigger: 'item',
			        formatter: "{a} <br/>{b} : {c} ({d}%)"
			    },
			    legend: {
			        orient: 'vertical',
			        left: 'left',
			        data: obj_ret['AreaLabel']
			    },
			    series : [
			        {
			            name: '个体数',
						type: 'pie',
			            radius : '55%',
			            center: ['50%', '60%'],
			            data: obj_ret['Area_Rate'],
			            itemStyle: {
			                emphasis: {
			                    shadowBlur: 10,
			                	shadowOffsetX: 0,
			                    shadowColor: 'rgba(0, 0, 0, 0.5)'
			                }
			         	}
			        }
			    ]
			};
			if (option_Nodes && typeof option_Nodes === "object") {
			    Chart_Nodes.setOption(option_Nodes, true);
			}
		}
		// 获取信息并显示
		function SetInfoShow()
		{
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/GetDataShow.php',
	         		type : "post",
	         		data : {},
	        		async : false,
    			}
    		).responseText;
    		if (ret != '')
    		{
	    		obj_ret = JSON.parse(ret);
    		}
    		// 返回为空则认为用户下线，刷新页面
    		else
    		{
    			window.location.reload();
    		}
		}
		// 重新选择查看楼盘
		$('#UserAreaList').bind('change', function(){
			var aid = $('#UserAreaList').val();
			var ret = $.ajax
			(
				{
	        		url : './AreaManagement/UpdateEachAreaShow.php',
	         		type : "post",
	         		data : {AreaID:aid},
	        		async : false,
    			}
    		).responseText;
    		if (ret != '')
    		{
	    		obj_ret_EachArea = JSON.parse(ret);
	    		obj_ret['EachArea_Name'] = obj_ret_EachArea['EachArea_Name'];
	    		obj_ret['EachArea_Rate'] = obj_ret_EachArea['EachArea_Rate'];
	    		SetEachAreaShow();
    		}
    		// 返回为空则认为用户下线，刷新页面
    		else
    		{
    			window.location.reload();
    		}
		});
		// 打开数据概览页面时获取信息并显示
		$(document).ready(function(){
			SetInfoShow();
			$('#UserAreaList').html(obj_ret['UserAreaList']);
			$('#AreaCount').text(obj_ret['AreaCount']);
			$('#UserCount').text(obj_ret['NormalUserCount'] + obj_ret['RegCount']);
			$('#HouseHoldCount').text(obj_ret['HouseHoldCount']);
			$('#ShopCount').text(obj_ret['ShopCount']);
			$('#CarCount').text(obj_ret['CarCount']);
			SetNodesShow();
			SetEachAreaShow();
			SetUserShow();
			SetMonthGetShow();
			SetMonthNotShow();
		});
	</script>
</html>