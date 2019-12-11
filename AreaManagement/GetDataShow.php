<?php
	// 返回数据概览页面应显示的信息(json)
	session_start();
	include_once('../conn/DBMgr.php');
	$conn = Connect();
	// 当前session显示已登录，更新session以验证
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		$UserInfo = GetUser($conn, $_SESSION['UserID']);
		$_SESSION['UID'] = $UserInfo['@UID'];
		$_SESSION['TEL'] = $UserInfo['@TEL'];
		$_SESSION['UserName'] = $UserInfo['@UserName'];
		$_SESSION['Type'] = $UserInfo['@strType'];
		$_SESSION['Online'] = $UserInfo['@Online'];
	}
	// 已登录
	if (isset($_SESSION['Online']) && $_SESSION['Online'] == 1)
	{
		// 获取当前用户所属楼盘列表
		$UserArea = GetUserAreaList($conn, $_SESSION['UserID']);
		// 获取正式用户数量
		$NormalUserCount = (int)(GetNormalUserCount($conn, '', '', '', '', '', '')['@Result']);
		// 获取待审核用户数量
		$RegCount = (int)(GetRegCount($conn, '', '', '')['@Result']);
		// 获取楼盘数量
		$AreaCount = (int)(GetAreaCount($conn, '')['@Result']);
		// 待返回数组
		$ret = [];
		// 正式用户数量
		$ret['NormalUserCount'] = $NormalUserCount;
		// 待审核用户数量
		$ret['RegCount'] = $RegCount;
		// 楼盘总数
		$ret['AreaCount'] = $AreaCount;
		// 登录账号可查看的楼盘列表
		for ($i = 0; $i < count($UserArea); $i++)
		{
			$ret['AreaLabel'][$i] = $UserArea[$i][1];
			$ret['Area_Rate'][$i]['name'] = $UserArea[$i][1];
			$ret['Area_Rate'][$i]['value'] = (int)(GetHouseHoldCount($conn, $UserArea[0][0], '', '', '', '', '')['@Result']) +
												(int)(GetShopCount($conn, $UserArea[0][0], '', '', '')['@Result']) +
												(int)(GetCarCount($conn, $UserArea[0][0], '', '', '')['@Result']);
		}
		// 当前查看的楼盘名称
		$ret['EachArea_Name'] = $UserArea[0][1];
		// 当前登录账号可查看的楼盘列表(select)
		$ret['UserAreaList'] = '<option value = "' . $UserArea[0][0] . '">' . $UserArea[0][1] . '</option>';
		for ($i = 1; $i < count($UserArea); $i++)
		{
			$ret['UserAreaList'] .= '<option value = "' . $UserArea[$i][0] . '">' . $UserArea[$i][1] . '</option>';
		}
		// 住户总数
		$ret['HouseHoldCount'] = (int)(GetHouseHoldCount($conn, '', '', '', '', '', '')['@Result']);
		// 商铺总数
		$ret['ShopCount'] = (int)(GetShopCount($conn, '', '', '', '')['@Result']);
		// 车辆总数
		$ret['CarCount'] = (int)(GetCarCount($conn, '', '', '', '')['@Result']);
		// 单个楼盘内三种缴费个体的数量列表
		$ret['EachArea_Rate'][0]['value'] = (int)(GetHouseHoldCount($conn, $UserArea[0][0], '', '', '', '', '')['@Result']);
		$ret['EachArea_Rate'][0]['name'] = '住户';
		$ret['EachArea_Rate'][1]['value'] = (int)(GetShopCount($conn, $UserArea[0][0], '', '', '')['@Result']);
		$ret['EachArea_Rate'][1]['name'] = '商铺';
		$ret['EachArea_Rate'][2]['value'] = (int)(GetCarCount($conn, $UserArea[0][0], '', '', '')['@Result']);
		$ret['EachArea_Rate'][2]['name'] = '车辆';
		// 最近六个月日期列表(年-月)
		for ($i = 0; $i < 6; $i++)
		{
			$MonthList[5 - $i] = date('Y-m', strtotime("-" . $i . "months", strtotime(date('Y-m',time()))));
		}
		// 最近六个月各楼盘收费金额列表
		$ret['MonthGetSource'][0][0] = 'month';
		for ($i = 0; $i < 6; $i++)
		{
			$ret['MonthGetSource'][0][$i + 1] = $MonthList[$i];
		}
		for ($i_line = 0; $i_line < count($UserArea); $i_line++)
		{
			$ret['MonthGetSource'][$i_line + 1][0] = $UserArea[$i_line][1];
			for ($i_col = 0; $i_col < 6; $i_col++)
			{
				$ret['MonthGetSource'][$i_line + 1][$i_col + 1] = (double)(GetHouseHoldSumFee($conn, $UserArea[$i_line][0], '', '', '', '', '', $MonthList[$i_line] . '-01', date('Y-m-d', strtotime("+1months", strtotime($MonthList[$i_line]))))['@Result']) + 
																	(double)(GetShopSumFee($conn, $UserArea[$i_line][0], '', '', '', $MonthList[$i_line] . '-01', date('Y-m-d', strtotime("+1months", strtotime($MonthList[$i_line]))))['@Result']) + 
																	(double)(GetCarSumFee($conn, $UserArea[$i_line][0], '', '', '', $MonthList[$i_line] . '-01', date('Y-m-d', strtotime("+1months", strtotime($MonthList[$i_line]))))['@Result']);
			}
		}
		// 最近六个月各楼盘收费金额列表(线)
		$ret['MonthGetSeriesLines'][0] = array('type'=> 'pie', 
			                					'id'=> 'pie',
							                	'radius'=> '30%',
							                	'center'=> ['50%', '25%'],
							                	'label'=> [
							                    'formatter'=> '{b}: {@' . $ret['MonthGetSource'][0][6] . '} ({d}%)'
							                	],
							                	'encode'=> [
							                    'itemName'=> 'month',
							                    'value'=> $ret['MonthGetSource'][0][6],
							                    'tooltip'=> $ret['MonthGetSource'][0][6]
							                	]);
		for ($i = 1; $i <= count($UserArea); $i++)
		{
			$ret['MonthGetSeriesLines'][$i]['type'] = 'line';
			$ret['MonthGetSeriesLines'][$i]['smooth'] = true;
			$ret['MonthGetSeriesLines'][$i]['seriesLayoutBy'] = 'row';
		}
		// 最近六个月各楼盘未缴费交易数量列表
		$ret['MonthNotSource'][0][0] = 'month';
		for ($i = 0; $i < 6; $i++)
		{
			$ret['MonthNotSource'][0][$i + 1] = $MonthList[$i];
		}
		for ($i_line = 0; $i_line < count($UserArea); $i_line++)
		{
			$ret['MonthNotSource'][$i_line + 1][0] = $UserArea[$i_line][1];
			for ($i_col = 0; $i_col < 6; $i_col++)
			{
				$ret['MonthNotSource'][$i_line + 1][$i_col + 1] = (int)(GetHouseHoldNotCount($conn, $UserArea[$i_line][0], '', '', '', '', '', $MonthList[$i_line] . '-01', date('Y-m-d', strtotime("+1months", strtotime($MonthList[$i_line]))))['@Result']) + 
																	(int)(GetShopNotCount($conn, $UserArea[$i_line][0], '', '', '', $MonthList[$i_line] . '-01', date('Y-m-d', strtotime("+1months", strtotime($MonthList[$i_line]))))['@Result']);
			}
		}
		// 最近六个月各楼盘未缴费交易数量列表(线)
		$ret['MonthNotSeriesLines'][0] = array('type'=> 'pie', 
			                					'id'=> 'pie',
							                	'radius'=> '30%',
							                	'center'=> ['50%', '25%'],
							                	'label'=> [
							                    'formatter'=> '{b}: {@' . $ret['MonthNotSource'][0][6] . '} ({d}%)'
							                	],
							                	'encode'=> [
							                    'itemName'=> 'month',
							                    'value'=> $ret['MonthNotSource'][0][6],
							                    'tooltip'=> $ret['MonthNotSource'][0][6]
							                	]);
		for ($i = 1; $i <= count($UserArea); $i++)
		{
			$ret['MonthNotSeriesLines'][$i]['type'] = 'line';
			$ret['MonthNotSeriesLines'][$i]['smooth'] = true;
			$ret['MonthNotSeriesLines'][$i]['seriesLayoutBy'] = 'row';
		}
		
        echo json_encode($ret);
	}
	// 未登录
	else
	{
		exit();
	}
	DisConnect($conn);
?>