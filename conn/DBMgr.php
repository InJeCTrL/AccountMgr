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
	// 删除商铺
	function DeleteShop($link, $opUserID, $targetShopID)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL DeleteShop(?, ?, ?, @Result)");
		$stmt->bind_param("sss", $opUserID, $targetShopID, $remoteIP);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 删除车辆
	function DeleteCar($link, $opUserID, $targetCarID)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL DeleteCar(?, ?, ?, @Result)");
		$stmt->bind_param("sss", $opUserID, $targetCarID, $remoteIP);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 删除账目
	function DeletePayment($link, $opUserID, $PID, $Type, $Cascade)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL DeletePayment(?, ?, ?, ?, ?, @Result)");
		$stmt->bind_param("sssss", $opUserID, $PID, $Type, $Cascade, $remoteIP);
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
	// 尝试新增商铺
	function AddShop($link, $opUserID, $AreaID, $ShopName, $Name, $TEL, $PMCU, $ELU, $TF, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL AddShop(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @Result, @ID)");
		$stmt->bind_param("isssssssss", $opUserID, $AreaID, $ShopName, $Name, $TEL, $PMCU, $ELU, $TF, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @ID');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 尝试新增车辆
	function AddCar($link, $opUserID, $AreaID, $CarCode, $Name, $TEL, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL AddCar(?, ?, ?, ?, ?, ?, ?, @Result, @ID)");
		$stmt->bind_param("issssss", $opUserID, $AreaID, $CarCode, $Name, $TEL, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @ID');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 新增住户关联车辆的缴费记录
	function AddHouseHoldCarPayment($link, $opUserID, $HID, $CarMonth, $Name, $TEL, $TicketTime, $CID, $CarCode, $CFee, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL AddHouseHoldCarPayment(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @Result, @ID)");
		$stmt->bind_param("isssssssss", $opUserID, $HID, $TicketTime, $CID, $CFee, $CarCode, $Name, $TEL, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @ID');
		$result = $res->fetch_assoc();
		$ID = $result['@ID'];
		if ($CarMonth != null)
		{
			foreach ($CarMonth as $Line)
			{
				$YearMonth = explode(".", $Line);
				$Year = $YearMonth[0];
				$Month = $YearMonth[1];
				$stmt = $link->prepare("CALL AddHouseHoldCarPaymentMonth(?, ?, ?, ?, ?, ?)");
				$stmt->bind_param("isssss", $opUserID, $ID, $Year, $Month, $remoteIP, $ModName);
				$stmt->execute();
			}
		}
		return $result;
	}
	// 新增住户缴费记录
	function AddHouseHoldPayment($link, $opUserID, $HID, $Month, $Name, $TEL, $PMC, $PRSF, $TF, $CPId, $TicketTime, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL AddHouseHoldPayment(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @Result, @ID)");
		$stmt->bind_param("issssssssss", $opUserID, $HID, $TicketTime, $CPId, $PMC, $PRSF, $TF, $Name, $TEL, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @ID');
		$result = $res->fetch_assoc();
		$ID = $result['@ID'];
		if ($Month != null)
		{
			foreach ($Month as $Line)
			{
				$YearMonth = explode(".", $Line);
				$Year = $YearMonth[0];
				$Month = $YearMonth[1];
				$stmt = $link->prepare("CALL AddHouseHoldPaymentMonth(?, ?, ?, ?, ?, ?)");
				$stmt->bind_param("isssss", $opUserID, $ID, $Year, $Month, $remoteIP, $ModName);
				$stmt->execute();
			}
		}
		return $result;
	}
	// 新增商铺关联车辆的缴费记录
	function AddShopCarPayment($link, $opUserID, $SID, $CarMonth, $Name, $TEL, $TicketTime, $CID, $CarCode, $CFee, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL AddShopCarPayment(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @Result, @ID)");
		$stmt->bind_param("isssssssss", $opUserID, $SID, $TicketTime, $CID, $CFee, $CarCode, $Name, $TEL, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @ID');
		$result = $res->fetch_assoc();
		$ID = $result['@ID'];
		if ($CarMonth != null)
		{
			foreach ($CarMonth as $Line)
			{
				$YearMonth = explode(".", $Line);
				$Year = $YearMonth[0];
				$Month = $YearMonth[1];
				$stmt = $link->prepare("CALL AddShopCarPaymentMonth(?, ?, ?, ?, ?, ?)");
				$stmt->bind_param("isssss", $opUserID, $ID, $Year, $Month, $remoteIP, $ModName);
				$stmt->execute();
			}
		}
		return $result;
	}
	// 新增商铺缴费记录
	function AddShopPayment($link, $opUserID, $SID, $Month, $Name, $TEL, $PMC, $ELE, $TF, $CPId, $TicketTime, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL AddShopPayment(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @Result, @ID)");
		$stmt->bind_param("issssssssss", $opUserID, $SID, $TicketTime, $CPId, $PMC, $ELE, $TF, $Name, $TEL, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @ID');
		$result = $res->fetch_assoc();
		$ID = $result['@ID'];
		if ($Month != null)
		{
			foreach ($Month as $Line)
			{
				$YearMonth = explode(".", $Line);
				$Year = $YearMonth[0];
				$Month = $YearMonth[1];
				$stmt = $link->prepare("CALL AddShopPaymentMonth(?, ?, ?, ?, ?, ?)");
				$stmt->bind_param("isssss", $opUserID, $ID, $Year, $Month, $remoteIP, $ModName);
				$stmt->execute();
			}
		}
		return $result;
	}
	// 新增车辆缴费记录
	function AddCarPayment($link, $opUserID, $CID, $Month, $Name, $TEL, $TicketTime, $CFee, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL AddCarPayment(?, ?, ?, ?, ?, ?, ?, ?, @Result, @ID)");
		$stmt->bind_param("isssssss", $opUserID, $TicketTime, $CID, $CFee, $Name, $TEL, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result, @ID');
		$result = $res->fetch_assoc();
		$ID = $result['@ID'];
		if ($Month != null)
		{
			foreach ($Month as $Line)
			{
				$YearMonth = explode(".", $Line);
				$Year = $YearMonth[0];
				$Month = $YearMonth[1];
				$stmt = $link->prepare("CALL AddCarPaymentMonth(?, ?, ?, ?, ?, ?)");
				$stmt->bind_param("isssss", $opUserID, $ID, $Year, $Month, $remoteIP, $ModName);
				$stmt->execute();
			}
		}
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
	// 设置商铺信息
	function SetShopInfo($link, $opUserID, $targetShopID, $AreaID, $ShopName, $Name, $TEL, $PMCU, $ELU, $TF, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL SetShopInfo(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @Result)");
		$stmt->bind_param("sssssssssss", $opUserID, $targetShopID, $AreaID, $ShopName, $Name, $TEL, $PMCU, $ELU, $TF, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 设置车辆信息
	function SetCarInfo($link, $opUserID, $targetCarID, $AreaID, $CarCode, $Name, $TEL, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		$stmt = $link->prepare("CALL SetCarInfo(?, ?, ?, ?, ?, ?, ?, ?, @Result)");
		$stmt->bind_param("ssssssss", $opUserID, $targetCarID, $AreaID, $CarCode, $Name, $TEL, $remoteIP, $ModName);
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
	// 获取商铺信息
	function GetShop($link, $SID)
	{
		$stmt = $link->prepare("CALL GetShop(?, @AreaID, @ShopName, @Name, @TEL, @PMCU, @ELU, @TF)");
		$stmt->bind_param("s", $SID);
		$stmt->execute();
		$res = $link->query('SELECT @AreaID, @ShopName, @Name, @TEL, @PMCU, @ELU, @TF');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取车辆信息
	function GetCar($link, $CID)
	{
		$stmt = $link->prepare("CALL GetCar(?, @AreaID, @CarCode, @Name, @TEL)");
		$stmt->bind_param("s", $CID);
		$stmt->execute();
		$res = $link->query('SELECT @AreaID, @CarCode, @Name, @TEL');
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
	// 检查用户对商铺的访问是否合法
	function IsLegalShop($link, $UserID, $SID)
	{
		$stmt = $link->prepare("CALL IsLegalShop(?, ?, @Result)");
		$stmt->bind_param("ss", $UserID, $SID);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 检查用户对车辆的访问是否合法
	function IsLegalCar($link, $UserID, $CID)
	{
		$stmt = $link->prepare("CALL IsLegalCar(?, ?, @Result)");
		$stmt->bind_param("ss", $UserID, $CID);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 检查指定住户指定月份是否已缴费
	function IsHouseHoldPaidMonth($link, $HID, $Year, $Month)
	{
		$stmt = $link->prepare("CALL IsHouseHoldPaidMonth(?, ?, ?, @Result)");
		$stmt->bind_param("sss", $HID, $Year, $Month);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 检查指定商铺指定月份是否已缴费
	function IsShopPaidMonth($link, $SID, $Year, $Month)
	{
		$stmt = $link->prepare("CALL IsShopPaidMonth(?, ?, ?, @Result)");
		$stmt->bind_param("sss", $SID, $Year, $Month);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 检查指定车辆指定月份是否已缴费
	function IsCarPaidMonth($link, $CID, $Year, $Month)
	{
		$stmt = $link->prepare("CALL IsCarPaidMonth(?, ?, ?, @Result)");
		$stmt->bind_param("sss", $CID, $Year, $Month);
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
	function GetBuildingCount($link, $UserID, $AID, $BNo)
	{
		$stmt = $link->prepare("CALL GetBuildingCount(@Result, ?, ?, ?)");
		$stmt->bind_param("sss", $UserID, $AID, $BNo);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取住户数量
	function GetHouseHoldCount($link, $UserID, $AreaID, $BuildingID, $RoomCode, $Name, $TEL, $square)
	{
		$stmt = $link->prepare("CALL GetHouseHoldCount(@Result, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssssss", $UserID, $AreaID, $BuildingID, $RoomCode, $Name, $TEL, $square);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取商铺缴费月份数量
	function GetShopCountByPaymentMonth($link, $UserID, $AreaID, $ShopName, $Name, $TEL, $Year, $Month, $ShowPaid)
	{
		$stmt = $link->prepare("CALL GetShopCountByPaymentMonth(@Result, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssssss", $UserID, $AreaID, $ShopName, $Name, $TEL, $Year, $Month, $ShowPaid);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取车辆缴费月份数量
	function GetCarCountByPaymentMonth($link, $UserID, $AreaID, $CarCode, $Name, $TEL, $Year, $Month, $ShowPaid)
	{
		$stmt = $link->prepare("CALL GetCarCountByPaymentMonth(@Result, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssssss", $UserID, $AreaID, $CarCode, $Name, $TEL, $Year, $Month, $ShowPaid);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取住户缴费月份数量
	function GetHouseHoldCountByPaymentMonth($link, $UserID, $AreaID, $BuildingID, $RoomCode, $Name, $TEL, $square, $Year, $Month, $ShowPaid)
	{
		$stmt = $link->prepare("CALL GetHouseHoldCountByPaymentMonth(@Result, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("ssssssssss", $UserID, $AreaID, $BuildingID, $RoomCode, $Name, $TEL, $square, $Year, $Month, $ShowPaid);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取商铺数量
	function GetShopCount($link, $UserID, $AreaID, $ShopName, $Name, $TEL)
	{
		$stmt = $link->prepare("CALL GetShopCount(@Result, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssss", $UserID, $AreaID, $ShopName, $Name, $TEL);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 获取车辆数量
	function GetCarCount($link, $UserID, $AreaID, $CarCode, $Name, $TEL)
	{
		$stmt = $link->prepare("CALL GetCarCount(@Result, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssss", $UserID, $AreaID, $CarCode, $Name, $TEL);
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
	// 获取住户预计缴费金额
	function GetHouseHoldPay($link, $HID, $Count)
	{
		$stmt = $link->prepare("CALL GetHouseHoldPay(@PMC, @PRSF, @TF, ?, ?)");
		$stmt->bind_param("ss", $HID, $Count);
		$stmt->execute();
		$res = $link->query('SELECT @PMC, @PRSF, @TF');
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
	// 获取商铺预计缴费金额
	function GetShopPay($link, $SID, $Count)
	{
		$stmt = $link->prepare("CALL GetShopPay(@PMC, @ELE, @TF, ?, ?)");
		$stmt->bind_param("ss", $SID, $Count);
		$stmt->execute();
		$res = $link->query('SELECT @PMC, @ELE, @TF');
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
	// 获取账目数量
	function GetAccountCount($link, $UserID, $Year, $Month, $Day, $Type, $Name, $Tel, $AID, $AddTarget)
	{
		$stmt = $link->prepare("CALL GetAccountCount(@Result, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("sssssssss", $UserID, $Year, $Month, $Day, $Type, $Name, $Tel, $AID, $AddTarget);
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
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2, $R3, $R4, $R5, $R6);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2, $R3, $R4, $R5, $R6];
				$i++;
			}
		}
		return $Result;
	}
	// 获取申请注册账号列表
	function GetRegList($link, $Offset, $UID, $TEL, $Name)
	{
		$stmt = $link->prepare("CALL GetRegList(?, ?, ?, ?)");
		$stmt->bind_param("isss", $Offset, $UID, $TEL, $Name);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2, $R3, $R4);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2, $R3, $R4];
				$i++;
			}
		}
		return $Result;
	}
	// 获取日志列表
	function GetLogList($link, $Offset, $Time, $IP, $OpName, $UID, $ModName, $tblName, $Action)
	{
		$stmt = $link->prepare("CALL GetLogList(?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("isssssss", $Offset, $Time, $IP, $OpName, $UID, $ModName, $tblName, $Action);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2, $R3, $R4, $R5, $R6, $R7, $R8, $R9);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2, $R3, $R4, $R5, $R6, $R7, $R8, $R9];
				$i++;
			}
		}
		return $Result;
	}
	// 获取正式用户身份列表
	function GetUserTypeList($link)
	{
		$stmt = $link->prepare("CALL GetUserTypeList()");
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2];
				$i++;
			}
		}
		return $Result;
	}
	// 获取模块名称列表
	function GetModNameList($link)
	{
		$stmt = $link->prepare("CALL GetModNameList()");
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1];
				$i++;
			}
		}
		return $Result;
	}
	// 获取数据库表名列表
	function GetTblList($link)
	{
		$stmt = $link->prepare("CALL GetTblList()");
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2, $R3, $R4);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2, $R3, $R4];
				$i++;
			}
		}
		return $Result;
	}
	// 获取管辖范围(楼盘)列表
	function GetAreaList($link, $Offset = 0, $Num = 0, $Name = '')
	{
		$stmt = $link->prepare("CALL GetAreaList(?, ?, ?)");
		$stmt->bind_param("iis", $Offset, $Num, $Name);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2];
				$i++;
			}
		}
		return $Result;
	}
	// 获取楼栋列表
	function GetBuildingList($link, $Offset = 0, $Num = 0, $UserID, $AID, $BNo)
	{
		$stmt = $link->prepare("CALL GetBuildingList(?, ?, ?, ?, ?)");
		$stmt->bind_param("iisss", $Offset, $Num, $UserID, $AID, $BNo);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2, $R3);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2, $R3];
				$i++;
			}
		}
		return $Result;
	}
	// 获取住户列表
	function GetHouseHoldList($link, $Offset = 0, $Num = 0, $UserID, $AID, $BID, $RoomCode, $Name, $TEL, $square)
	{
		$stmt = $link->prepare("CALL GetHouseHoldList(?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("iisssssss", $Offset, $Num, $UserID, $AID, $BID, $RoomCode, $Name, $TEL, $square);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2, $R3, $R4, $R5);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2, $R3, $R4, $R5];
				$i++;
			}
		}
		return $Result;
	}
	// 获取住户缴费月份列表
	function GetHouseHoldListByPaymentMonth($link, $Offset = 0, $Num = 0, $UserID, $AID, $BID, $RoomCode, $Name, $TEL, $square, $Year, $Month, $ShowPaid)
	{
		$stmt = $link->prepare("CALL GetHouseHoldListByPaymentMonth(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("iissssssssss", $Offset, $Num, $UserID, $AID, $BID, $RoomCode, $Name, $TEL, $square, $Year, $Month, $ShowPaid);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2, $R3, $R4, $R5, $R6, $R7);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2, $R3, $R4, $R5, $R6, $R7];
				$i++;
			}
		}
		return $Result;
	}
	// 获取商铺缴费月份列表
	function GetShopListByPaymentMonth($link, $Offset = 0, $Num = 0, $UserID, $AID, $ShopName, $Name, $TEL, $Year, $Month, $ShowPaid)
	{
		$stmt = $link->prepare("CALL GetShopListByPaymentMonth(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("iissssssss", $Offset, $Num, $UserID, $AID, $ShopName, $Name, $TEL, $Year, $Month, $ShowPaid);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2, $R3, $R4, $R5, $R6);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2, $R3, $R4, $R5, $R6];
				$i++;
			}
		}
		return $Result;
	}
	// 获取车辆缴费月份列表
	function GetCarListByPaymentMonth($link, $Offset = 0, $Num = 0, $UserID, $AID, $CarCode, $Name, $TEL, $Year, $Month, $ShowPaid)
	{
		$stmt = $link->prepare("CALL GetCarListByPaymentMonth(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("iissssssss", $Offset, $Num, $UserID, $AID, $CarCode, $Name, $TEL, $Year, $Month, $ShowPaid);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2, $R3, $R4, $R5, $R6);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2, $R3, $R4, $R5, $R6];
				$i++;
			}
		}
		return $Result;
	}
	// 获取商铺列表
	function GetShopList($link, $Offset = 0, $Num = 0, $UserID, $AID, $ShopName, $Name, $TEL)
	{
		$stmt = $link->prepare("CALL GetShopList(?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("iisssss", $Offset, $Num, $UserID, $AID, $ShopName, $Name, $TEL);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2, $R3, $R4, $R5);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2, $R3, $R4, $R5];
				$i++;
			}
		}
		return $Result;
	}
	// 获取车辆列表
	function GetCarList($link, $Offset = 0, $Num = 0, $UserID, $AID, $CarCode, $Name, $TEL)
	{
		$stmt = $link->prepare("CALL GetCarList(?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param("iisssss", $Offset, $Num, $UserID, $AID, $CarCode, $Name, $TEL);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2, $R3, $R4, $R5);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2, $R3, $R4, $R5];
				$i++;
			}
		}
		return $Result;
	}
	// 获取账目清单列表
	function GetAccountList($link, $Offset = 0, $Num = 0, $UserID, $Year, $Month, $Day, $Type, $Name, $Tel, $AID, $AddTarget)
	{
		// 待返回的数据集合
		$Result = [];
		// 获取所有类型
		if ($Type === '')
		{
			for ($i = 0; $i < 5; $i++)
			{
				$Result = array_merge($Result, GetAccountList($link, 0, 0, $UserID, $Year, $Month, $Day, $i, $Name, $Tel, $AID, $AddTarget));
			}
			if ($Num != 0)
			{
				$Result = array_slice($Result, $Offset, $Num);
			}
		}
		else
		{
			$stmt = $link->prepare("CALL GetAccountList(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			$stmt->bind_param("iisssssssss", $Offset, $Num, $UserID, $Year, $Month, $Day, $Type, $Name, $Tel, $AID, $AddTarget);
			$stmt->execute();
			$stmt->store_result();
			if ($stmt->num_rows != 0)
			{
				$stmt->bind_result($R1, $R2, $R3, $R4, $R5, $R6, $R7);
				// 数据行下标
				$i = 0;
				// 循环获取数据
				while ($res = $stmt->fetch())
				{
					$Result[$i] = [$R1, $R2, $R3, $R4, $R5, $R6, $R7];
					$i++;
				}
			}
		}
		return $Result;
	}
	// 获取用户所属管辖范围(楼盘)列表
	function GetUserAreaList($link, $UserID)
	{
		$stmt = $link->prepare("CALL GetUserAreaList(?)");
		$stmt->bind_param("i", $UserID);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2];
				$i++;
			}
		}
		return $Result;
	}
	// 获取用户所属账目年份列表
	function GetUserAccountYearList($link, $UserID)
	{
		$stmt = $link->prepare("CALL GetUserAccountYearList(?)");
		$stmt->bind_param("i", $UserID);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1];
				$i++;
			}
		}
		return $Result;
	}
	// 获取用户可管理缴费月份列表
	function GetYearMonth($link, $UserID, $target)
	{
		$stmt = $link->prepare("CALL GetYearMonth(?, ?)");
		$stmt->bind_param("ii", $UserID, $target);
		$stmt->execute();
		$stmt->store_result();
		// 待返回的数据集合
		$Result = [];
		if ($stmt->num_rows != 0)
		{
			$stmt->bind_result($R1, $R2);
			// 数据行下标
			$i = 0;
			// 循环获取数据
			while ($res = $stmt->fetch())
			{
				$Result[$i] = [$R1, $R2];
				$i++;
			}
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
	// 备份指定编号的数据表, 返回数据占比
	function BakTable($link, $opUserID, $i_tbl, $FolderPath, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		// 备份表到csv文件
		$stmt = $link->prepare("CALL BakTable(?, ?, ?, ?, ?, @Result)");
		$stmt->bind_param("sssss", $opUserID, $i_tbl, $FolderPath, $remoteIP, $ModName);
		$stmt->execute();
		$res = $link->query('SELECT @Result');
		$result = $res->fetch_assoc();
		return $result;
	}
	// 恢复csv中的数据到指定名称的数据表, 返回导入的数据条数
	function RecTable($link, $opUserID, $FileName, $FilePath, $ModName)
	{
		$remoteIP = $_SERVER['REMOTE_ADDR'];
		// 关闭外键约束
		$link->query("SET FOREIGN_KEY_CHECKS=0");
		// 恢复csv到数据表
		$sql = "LOAD DATA INFILE '" . $FilePath . "' REPLACE INTO TABLE " . $FileName . " fields terminated by ',' optionally enclosed by '\"' lines terminated by '\r\n';";
		$res = $link->query($sql);
		$res = $link->query('SELECT ROW_COUNT() AS Result');
		$result = $res->fetch_assoc();
		// 启用外键约束
		$link->query("SET FOREIGN_KEY_CHECKS=1");
		// 记录日志
		$stmt = $link->prepare("INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), ?, ?, ?, '恢复数据表', ?, -1)");
		$stmt->bind_param("ssss", $remoteIP, $opUserID, $ModName, $FileName);
		$stmt->execute();
		return $result;
	}
?>