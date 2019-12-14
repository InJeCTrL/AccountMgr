<?php
	// 连接
	function Connect()
	{
		$link = mysqli_connect("127.0.0.1", "root", "123");
		$link->set_charset("utf8");
		$link->select_db('amgr');
		return $link;
	}
	// 断开连接
	function DisConnect($link)
	{
		mysqli_close($link);
	}
	// 验证用户账号密码并登录
	function VerifyUser($link, $UIDTEL, $Password, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL VerifyUser(?, ?, ?, ?, @Result, @outID)");
		$stmt->bind_param("ssss", $UIDTEL, $Password, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @outID');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 验证用户账号信息
	function VerifyUserInfo($link, $UID, $TEL, $UserName, $Sec, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL VerifyUserInfo(?, ?, ?, ?, ?, ?, @Result, @outID)");
		$stmt->bind_param("ssssss", $UID, $TEL, $UserName, $Sec, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @outID');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 注销用户账号的登录状态
	function SignOut($link, $opUserID, $targetUserID, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL SignOut(?, ?, ?, ?)");
		$stmt->bind_param("iiss", $opUserID, $targetUserID, $remoteIP, $ModName);
		$stmt->execute();
	}
	// 删除用户账号
	function DeleteUser($link, $opUserID, $targetUserID)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL DeleteUser(?, ?, ?, @Result)");
		$stmt->bind_param("iis", $opUserID, $targetUserID, $remoteIP);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 删除楼盘
	function DeleteArea($link, $opUserID, $targetAreaID)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL DeleteArea(?, ?, ?, @Result)");
		$stmt->bind_param("sss", $opUserID, $targetAreaID, $remoteIP);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 删除楼栋
	function DeleteBuilding($link, $opUserID, $targetBuildingID)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL DeleteBuilding(?, ?, ?, @Result)");
		$stmt->bind_param("sss", $opUserID, $targetBuildingID, $remoteIP);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 删除住户
	function DeleteHouseHold($link, $opUserID, $targetHouseHoldID)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL DeleteHouseHold(?, ?, ?, @Result)");
		$stmt->bind_param("sss", $opUserID, $targetHouseHoldID, $remoteIP);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 修改用户账户密码
	function SetUserPassword($link, $opUserID, $targetUserID, $Password, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL SetUserPassword(?, ?, ?, ?, ?)");
		$stmt->bind_param("iisss", $opUserID, $targetUserID, $Password, $remoteIP, $ModName);
		$stmt->execute();
	}
	// 修改用户账户安全字符串
	function SetUserSec($link, $opUserID, $targetUserID, $Sec, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL SetUserSec(?, ?, ?, ?, ?)");
		$stmt->bind_param("iisss", $opUserID, $targetUserID, $Sec, $remoteIP, $ModName);
		$stmt->execute();
	}
	// 提交用户账号申请请求
	function SubmitUserApply($link, $opUserID, $UID, $TEL, $Name, $pwd1, $Sec, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL SubmitUserApply(?, ?, ?, ?, ?, ?, ?, ?, @Result)");
		$stmt->bind_param("isssssss", $opUserID, $UID, $TEL, $Name, $pwd1, $Sec, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 尝试新增账号
	function AddUser($link, $opUserID, $UID, $TEL, $Name, $Type, $pwd1, $Sec, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL AddUser(?, ?, ?, ?, ?, ?, ?, ?, ?, @Result, @ID)");
		$stmt->bind_param("issssssss", $opUserID, $UID, $TEL, $Name, $Type, $pwd1, $Sec, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @ID');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 尝试新增楼盘
	function AddArea($link, $opUserID, $Name, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL AddArea(?, ?, ?, ?, @Result, @ID)");
		$stmt->bind_param("isss", $opUserID, $Name, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @ID');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 尝试新增楼栋
	function AddBuilding($link, $opUserID, $AreaID, $BNo, $PMCU, $PRSF, $TF, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL AddBuilding(?, ?, ?, ?, ?, ?, ?, ?, @Result, @ID)");
		$stmt->bind_param("isssssss", $opUserID, $AreaID, $BNo, $PMCU, $PRSF, $TF, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @ID');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 尝试新增住户
	function AddHouseHold($link, $opUserID, $AreaID, $BID, $RoomCode, $Name, $TEL, $square, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL AddHouseHold(?, ?, ?, ?, ?, ?, ?, ?, ?, @Result, @ID)");
		$stmt->bind_param("issssssss", $opUserID, $AreaID, $BID, $RoomCode, $Name, $TEL, $square, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @ID');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 设置用户账号的个人信息
	function SetPersonalInfo($link, $opUserID, $targetUserID, $Name, $TEL, $UID, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL SetPersonalInfo(?, ?, ?, ?, ?, ?, ?, @Result)");
		$stmt->bind_param("iisssss", $opUserID, $targetUserID, $Name, $TEL, $UID, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 设置用户账号信息
	function SetUserInfo($link, $opUserID, $targetUserID, $Name, $TEL, $UID, $Type, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL SetUserInfo(?, ?, ?, ?, ?, ?, ?, ?, @Result)");
		$stmt->bind_param("ssssssss", $opUserID, $targetUserID, $Name, $TEL, $UID, $Type, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 设置楼盘信息
	function SetAreaInfo($link, $opUserID, $targetAreaID, $Name, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL SetAreaInfo(?, ?, ?, ?, ?, @Result)");
		$stmt->bind_param("sssss", $opUserID, $targetAreaID, $Name, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 设置楼栋信息
	function SetBuildingInfo($link, $opUserID, $targetBuildingID, $AreaID, $BNo, $PMCU, $PRSF, $TF, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL SetBuildingInfo(?, ?, ?, ?, ?, ?, ?, ?, ?, @Result)");
		$stmt->bind_param("sssssssss", $opUserID, $targetBuildingID, $AreaID, $BNo, $PMCU, $PRSF, $TF, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 设置住户信息
	function SetHouseHoldInfo($link, $opUserID, $targetHouseHoldID, $AreaID, $BID, $RoomCode, $Name, $TEL, $square, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL SetHouseHoldInfo(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @Result)");
		$stmt->bind_param("ssssssssss", $opUserID, $targetHouseHoldID, $AreaID, $BID, $RoomCode, $Name, $TEL, $square, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取用户账号信息
	function GetUser($link, $userID)
	{
		$stmt = $link->prepare("CALL GetUser(?, @UID, @TEL, @UserName, @strType, @Online, @intType)");
		$stmt->bind_param("s", $userID);
		$stmt->execute();
		$res = $link->query('SELECT @UID, @TEL, @UserName, @strType, @Online, @intType');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取楼盘信息
	function GetArea($link, $areaID)
	{
		$stmt = $link->prepare("CALL GetArea(?, @AreaName)");
		$stmt->bind_param("s", $areaID);
		$stmt->execute();
		$res = $link->query('SELECT @AreaName');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取楼栋信息
	function GetBuilding($link, $BID)
	{
		$stmt = $link->prepare("CALL GetBuilding(?, @AreaID, @BNo, @PMCU, @PRSF, @TF)");
		$stmt->bind_param("s", $BID);
		$stmt->execute();
		$res = $link->query('SELECT @AreaID, @BNo, @PMCU, @PRSF, @TF');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取住户信息
	function GetHouseHold($link, $HID)
	{
		$stmt = $link->prepare("CALL GetHouseHold(?, @AreaID, @BID, @RoomCode, @Name, @TEL, @square)");
		$stmt->bind_param("s", $HID);
		$stmt->execute();
		$res = $link->query('SELECT @AreaID, @BID, @RoomCode, @Name, @TEL, @square');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 检查用户对楼盘的访问是否合法
	function IsLegalArea($link, $UserID, $AreaID)
	{
		$stmt = $link->prepare("CALL IsLegalArea(?, ?, @Result)");
		$stmt->bind_param("ss", $UserID, $AreaID);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 检查用户对楼栋的访问是否合法
	function IsLegalBuilding($link, $UserID, $BID)
	{
		$stmt = $link->prepare("CALL IsLegalBuilding(?, ?, @Result)");
		$stmt->bind_param("ss", $UserID, $BID);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 检查用户对住户的访问是否合法
	function IsLegalHouseHold($link, $UserID, $HID)
	{
		$stmt = $link->prepare("CALL IsLegalHouseHold(?, ?, @Result)");
		$stmt->bind_param("ss", $UserID, $HID);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取正式用户账号数量
	function GetNormalUserCount($link, $UID, $TEL, $Name, $Type, $Online, $Area)
	{
		$stmt = $link->prepare("CALL GetNormalUserCount(@Result, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssss", $UID, $TEL, $Name, $Type, $Online, $Area);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取申请注册用户数量
	function GetRegCount($link, $UID, $TEL, $Name)
	{
		$stmt = $link->prepare("CALL GetRegCount(@Result, ?, ?, ?)");
		$stmt->bind_param("sss", $UID, $TEL, $Name);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取楼盘数量
	function GetAreaCount($link, $Name)
	{
		$stmt = $link->prepare("CALL GetAreaCount(@Result, ?)");
		$stmt->bind_param("s", $Name);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取楼栋数量
	function GetBuildingCount($link, $AID, $BNo)
	{
		$stmt = $link->prepare("CALL GetBuildingCount(@Result, ?, ?)");
		$stmt->bind_param("ss", $AID, $BNo);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取住户数量
	function GetHouseHoldCount($link, $AreaID, $BuildingID, $RoomCode, $Name, $TEL, $square)
	{
		$stmt = $link->prepare("CALL GetHouseHoldCount(@Result, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssss", $AreaID, $BuildingID, $RoomCode, $Name, $TEL, $square);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取商铺数量
	function GetShopCount($link, $AreaID, $ShopName, $Name, $TEL)
	{
		$stmt = $link->prepare("CALL GetShopCount(@Result, ?, ?, ?, ?)");
		$stmt->bind_param("ssss", $AreaID, $ShopName, $Name, $TEL);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取车辆数量
	function GetCarCount($link, $AreaID, $CarCode, $Name, $TEL)
	{
		$stmt = $link->prepare("CALL GetCarCount(@Result, ?, ?, ?, ?)");
		$stmt->bind_param("ssss", $AreaID, $CarCode, $Name, $TEL);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取住户一段时间的缴费金额
	function GetHouseHoldSumFee($link, $AreaID, $BuildingID, $RoomCode, $Name, $TEL, $square, $startDate, $endDate)
	{
		$stmt = $link->prepare("CALL GetHouseHoldSumFee(@Result, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssssss", $AreaID, $BuildingID, $RoomCode, $Name, $TEL, $square, $startDate, $endDate);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取住户一段时间的未缴费的交易数
	function GetHouseHoldNotCount($link, $AreaID, $BuildingID, $RoomCode, $Name, $TEL, $square, $startDate, $endDate)
	{
		$stmt = $link->prepare("CALL GetHouseHoldNotCount(@Result, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssssss", $AreaID, $BuildingID, $RoomCode, $Name, $TEL, $square, $startDate, $endDate);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取商铺一段时间的未缴费的交易数
	function GetShopNotCount($link, $AreaID, $ShopName, $Name, $TEL, $startDate, $endDate)
	{
		$stmt = $link->prepare("CALL GetShopNotCount(@Result, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssss", $AreaID, $ShopName, $Name, $TEL, $startDate, $endDate);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取商铺一段时间的缴费金额
	function GetShopSumFee($link, $AreaID, $ShopName, $Name, $TEL, $startDate, $endDate)
	{
		$stmt = $link->prepare("CALL GetShopSumFee(@Result, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssss", $AreaID, $ShopName, $Name, $TEL, $startDate, $endDate);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取车辆一段时间的缴费金额
	function GetCarSumFee($link, $AreaID, $CarCode, $Name, $TEL, $startDate, $endDate)
	{
		$stmt = $link->prepare("CALL GetCarSumFee(@Result, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssss", $AreaID, $CarCode, $Name, $TEL, $startDate, $endDate);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取日志数量
	function GetLogCount($link, $Time, $IP, $OpName, $UID, $ModName, $tblName, $Action)
	{
		$stmt = $link->prepare("CALL GetLogCount(@Result, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssssss", $Time, $IP, $OpName, $UID, $ModName, $tblName, $Action);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取正式用户账号列表
	function GetUserList($link, $Offset, $UID, $TEL, $Name, $Type, $Online, $Area)
	{
		$stmt = $link->prepare("CALL GetUserList(?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("issssss", $Offset, $UID, $TEL, $Name, $Type, $Online, $Area);
		$stmt->execute();
		$stmt->bind_result($R1, $R2, $R3, $R4, $R5, $R6);
		// 数据行下标
		$i = 0;
		// 待返回的数据集合
		$Result = [];
		// 循环获取数据
		while ($res = $stmt->fetch())
		{
			$Result[$i] = [$R1, $R2, $R3, $R4, $R5, $R6];
			$i++;
		}
		return $Result;
	}
	// 获取申请注册账号列表
	function GetRegList($link, $Offset, $UID, $TEL, $Name)
	{
		$stmt = $link->prepare("CALL GetRegList(?, ?, ?, ?)");
		$stmt->bind_param("isss", $Offset, $UID, $TEL, $Name);
		$stmt->execute();
		$stmt->bind_result($R1, $R2, $R3, $R4);
		// 数据行下标
		$i = 0;
		// 待返回的数据集合
		$Result = [];
		// 循环获取数据
		while ($res = $stmt->fetch())
		{
			$Result[$i] = [$R1, $R2, $R3, $R4];
			$i++;
		}
		return $Result;
	}
	// 获取日志列表
	function GetLogList($link, $Offset, $Time, $IP, $OpName, $UID, $ModName, $tblName, $Action)
	{
		$stmt = $link->prepare("CALL GetLogList(?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("isssssss", $Offset, $Time, $IP, $OpName, $UID, $ModName, $tblName, $Action);
		$stmt->execute();
		$stmt->bind_result($R1, $R2, $R3, $R4, $R5, $R6, $R7, $R8, $R9);
		// 数据行下标
		$i = 0;
		// 待返回的数据集合
		$Result = [];
		// 循环获取数据
		while ($res = $stmt->fetch())
		{
			$Result[$i] = [$R1, $R2, $R3, $R4, $R5, $R6, $R7, $R8, $R9];
			$i++;
		}
		return $Result;
	}
	// 获取正式用户身份列表
	function GetUserTypeList($link)
	{
		$stmt = $link->prepare("CALL GetUserTypeList()");
		$stmt->execute();
		$stmt->bind_result($R1, $R2);
		// 数据行下标
		$i = 0;
		// 待返回的数据集合
		$Result = [];
		// 循环获取数据
		while ($res = $stmt->fetch())
		{
			$Result[$i] = [$R1, $R2];
			$i++;
		}
		return $Result;
	}
	// 获取模块名称列表
	function GetModNameList($link)
	{
		$stmt = $link->prepare("CALL GetModNameList()");
		$stmt->execute();
		$stmt->bind_result($R1);
		// 数据行下标
		$i = 0;
		// 待返回的数据集合
		$Result = [];
		// 循环获取数据
		while ($res = $stmt->fetch())
		{
			$Result[$i] = [$R1];
			$i++;
		}
		return $Result;
	}
	// 获取数据库表名列表
	function GetTblList($link)
	{
		$stmt = $link->prepare("CALL GetTblList()");
		$stmt->execute();
		$stmt->bind_result($R1);
		// 数据行下标
		$i = 0;
		// 待返回的数据集合
		$Result = [];
		// 循环获取数据
		while ($res = $stmt->fetch())
		{
			$Result[$i] = [$R1];
			$i++;
		}
		return $Result;
	}
	// 获取管辖范围(楼盘)列表
	function GetAreaList($link, $Offset = 0, $Num = 0, $Name = '')
	{
		$stmt = $link->prepare("CALL GetAreaList(?, ?, ?)");
		$stmt->bind_param("iis", $Offset, $Num, $Name);
		$stmt->execute();
		$stmt->bind_result($R1, $R2);
		// 数据行下标
		$i = 0;
		// 待返回的数据集合
		$Result = [];
		// 循环获取数据
		while ($res = $stmt->fetch())
		{
			$Result[$i] = [$R1, $R2];
			$i++;
		}
		return $Result;
	}
	// 获取楼栋列表
	function GetBuildingList($link, $Offset = 0, $Num = 0, $AID, $BNo)
	{
		$stmt = $link->prepare("CALL GetBuildingList(?, ?, ?, ?)");
		$stmt->bind_param("iiss", $Offset, $Num, $AID, $BNo);
		$stmt->execute();
		$stmt->bind_result($R1, $R2, $R3);
		// 数据行下标
		$i = 0;
		// 待返回的数据集合
		$Result = [];
		// 循环获取数据
		while ($res = $stmt->fetch())
		{
			$Result[$i] = [$R1, $R2, $R3];
			$i++;
		}
		return $Result;
	}
	// 获取住户列表
	function GetHouseHoldList($link, $Offset = 0, $Num = 0, $AID, $BID, $RoomCode, $Name, $TEL, $square)
	{
		$stmt = $link->prepare("CALL GetHouseHoldList(?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("iissssss", $Offset, $Num, $AID, $BID, $RoomCode, $Name, $TEL, $square);
		$stmt->execute();
		$stmt->bind_result($R1, $R2, $R3, $R4, $R5);
		// 数据行下标
		$i = 0;
		// 待返回的数据集合
		$Result = [];
		// 循环获取数据
		while ($res = $stmt->fetch())
		{
			$Result[$i] = [$R1, $R2, $R3, $R4, $R5];
			$i++;
		}
		return $Result;
	}
	// 获取用户所属管辖范围(楼盘)列表
	function GetUserAreaList($link, $UserID)
	{
		$stmt = $link->prepare("CALL GetUserAreaList(?)");
		$stmt->bind_param("i", $UserID);
		$stmt->execute();
		$stmt->bind_result($R1, $R2);
		// 数据行下标
		$i = 0;
		// 待返回的数据集合
		$Result = [];
		// 循环获取数据
		while ($res = $stmt->fetch())
		{
			$Result[$i] = [$R1, $R2];
			$i++;
		}
		return $Result;
	}
	// 设置用户所属管辖范围(楼盘)列表
	function SetUserAreaList($link, $opUserID, $targetUserID, $Area, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		// 先删除用户所属管辖范围
		$stmt = $link->prepare("CALL DeleteUserArea(?, ?, ?, ?)");
		$stmt->bind_param("ssss", $opUserID, $targetUserID, $remoteIP, $ModName);
		$stmt->execute();
		// 插入用户所属管辖范围
		for ($i = 0; $i < count($Area); $i++)
		{
			$stmt = $link->prepare("CALL AddUserArea(?, ?, ?, ?, ?)");
			$stmt->bind_param("sssss", $opUserID, $targetUserID, $Area[$i], $remoteIP, $ModName);
			$stmt->execute();
		}
	}
?>