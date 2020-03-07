-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2020-03-07 09:16:33
-- 服务器版本： 5.7.9
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `amgr`
--

DELIMITER $$
--
-- 存储过程
--
DROP PROCEDURE IF EXISTS `AddArea`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddArea` (IN `opUserID` INT, IN `Name` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `_ID` INT)  BEGIN
    INSERT INTO area VALUES(null, Name);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增楼盘： ',Name, ' 成功'), 'area', _ID);
    SELECT "新增成功！" INTO Result;
END$$

DROP PROCEDURE IF EXISTS `AddBuilding`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddBuilding` (IN `opUserID` INT, IN `AreaID` INT, IN `_BNo` VARCHAR(1000) CHARSET utf8, IN `_PMCU` DOUBLE, IN `_PRSF` DOUBLE, IN `_TF` DOUBLE, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `_ID` INT)  BEGIN
	DECLARE _Name VARCHAR(1000);
    SELECT CONCAT_WS('-',area.AreaName,_BNo) INTO _Name FROM area WHERE area.ID = AreaID;
	INSERT INTO building VALUES(null, _BNo, AreaID, _PMCU, _PRSF, _TF);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增楼栋： ',_Name, ' 成功'), 'building', _ID);
    SELECT "新增成功！" INTO Result;
END$$

DROP PROCEDURE IF EXISTS `AddCar`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddCar` (IN `opUserID` INT, IN `_AreaID` INT, IN `_CarCode` VARCHAR(1000) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `_ID` INT)  BEGIN
	DECLARE _Name VARCHAR(1000);
    SELECT CONCAT_WS('-',area.AreaName,_CarCode) INTO _Name FROM area WHERE area.ID = _AreaID;
    INSERT INTO car VALUES(null, _AreaID, _CarCode, Name, _TEL);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增车辆： ',_Name, ' 成功'), 'car', _ID);
    SELECT "新增成功！" INTO Result;
END$$

DROP PROCEDURE IF EXISTS `AddCarPayment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddCarPayment` (IN `opUserID` INT, IN `_TicketTime` DATE, IN `_CID` INT, IN `_Fee` DOUBLE, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `_ID` INT)  BEGIN
    DECLARE _AName VARCHAR(1000);
    DECLARE _CarCode VARCHAR(1000);
    DECLARE strDate VARCHAR(1000);
    SELECT _CID INTO _ID;
    SELECT car.CarCode INTO _CarCode FROM car WHERE car.ID = _CID;
    SELECT area.AreaName INTO _AName FROM area,car WHERE area.ID = car.AreaID AND car.ID = _CID;
    INSERT INTO carpayment VALUES(null,_ID,_Fee,_TicketTime);
    SELECT LAST_INSERT_ID() INTO _ID;
    SELECT date_format(_TicketTime,'%Y-%c-%d') INTO strDate;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增无关联车辆缴费： ',_AName,'-',_CarCode,'-',_Fee,'(',strDate, ') 成功'), 'carpayment', _ID);
    SELECT "新增成功！" INTO Result;
END$$

DROP PROCEDURE IF EXISTS `AddCarPaymentMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddCarPaymentMonth` (IN `opUserID` INT, IN `_PaymentId` INT, IN `_Year` INT, IN `_Month` INT, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE _INFO VARCHAR(1000);
    DECLARE _ID INT;
	SELECT CONCAT_WS('.',area.AreaName,car.CarCode) INTO _INFO FROM carpayment,car,area WHERE car.AreaID = area.ID AND carpayment.CarID = car.ID AND carpayment.ID = _PaymentId LIMIT 1;
    INSERT INTO carpaymentmonth VALUES(null, _PaymentId, _Year, _Month);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增无关联车辆缴费月份： ', _INFO, '-', _Year, '年', _Month, '月 成功'), 'carpaymentmonth', _ID);
END$$

DROP PROCEDURE IF EXISTS `AddHouseHold`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddHouseHold` (IN `opUserID` INT, IN `AreaID` INT, IN `_BID` VARCHAR(30) CHARSET utf8, IN `_RoomCode` VARCHAR(500) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `_square` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `_ID` INT)  BEGIN
	DECLARE _Name VARCHAR(1000);
    SELECT CONCAT_WS('-',area.AreaName,building.BNo,_RoomCode) INTO _Name FROM area,building WHERE building.AreaID = area.ID AND building.ID = _BID;
    INSERT INTO household VALUES(null, _BID, _RoomCode, Name, _TEL, _square);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增住户： ',_Name, ' 成功'), 'household', _ID);
    SELECT "新增成功！" INTO Result;
END$$

DROP PROCEDURE IF EXISTS `AddHouseHoldCarPayment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddHouseHoldCarPayment` (IN `opUserID` INT, IN `_HID` VARCHAR(30) CHARSET utf8, IN `_TicketTime` DATE, IN `_CID` INT, IN `_Fee` DOUBLE, IN `_CarCode` VARCHAR(1000) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `_ID` INT)  BEGIN
    DECLARE _AName VARCHAR(1000);
    DECLARE strDate VARCHAR(1000);
    DECLARE _AID INT;
    IF _CID = -1 THEN
    	SELECT area.AreaName, area.ID INTO _AName,_AID FROM area,building,household WHERE area.ID = building.AreaID AND building.ID = household.BuildingID AND household.ID = _HID;
    	INSERT INTO car VALUES(null,_AID,_CarCode,_Name,_TEL);
        SELECT LAST_INSERT_ID() INTO _ID;
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增车辆： ',_AName,'-',_CarCode, ' 成功'), 'car', _ID);
    ELSE
    	SELECT _CID INTO _ID;
    	SELECT area.AreaName INTO _AName FROM area,car WHERE area.ID = car.AreaID AND car.ID = _CID;
    END IF;
    INSERT INTO carpayment VALUES(null,_ID,_Fee,_TicketTime);
    SELECT LAST_INSERT_ID() INTO _ID;
    SELECT date_format(_TicketTime,'%Y-%c-%d') INTO strDate;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增住户车辆缴费： ',_AName,'-',_CarCode,'-',_Fee,'(',strDate, ') 成功'), 'carpayment', _ID);
    SELECT "新增成功！" INTO Result;
END$$

DROP PROCEDURE IF EXISTS `AddHouseHoldCarPaymentMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddHouseHoldCarPaymentMonth` (IN `opUserID` INT, IN `_PaymentId` INT, IN `_Year` INT, IN `_Month` INT, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE _INFO VARCHAR(1000);
    DECLARE _ID INT;
	SELECT CONCAT_WS('.',area.AreaName,building.BNo,household.RoomCode,car.CarCode) INTO _INFO FROM carpayment,car,householdpayment,household,area,building WHERE car.ID = carpayment.CarID AND area.ID = building.AreaID AND building.ID = household.BuildingID AND  carpayment.ID = householdpayment.CarPaymentID AND household.ID = householdpayment.HouseHoldID AND carpayment.ID = _PaymentId LIMIT 1;
    INSERT INTO carpaymentmonth VALUES(null, _PaymentId, _Year, _Month);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增住户车辆缴费月份： ', _INFO, '-', _Year, '年', _Month, '月 成功'), 'carpaymentmonth', _ID);
END$$

DROP PROCEDURE IF EXISTS `AddHouseHoldPayment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddHouseHoldPayment` (IN `opUserID` INT, IN `_HID` VARCHAR(30) CHARSET utf8, IN `_TicketTime` DATE, IN `_CPID` INT, IN `_PMC` DOUBLE, IN `_PRSF` DOUBLE, IN `_TF` DOUBLE, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `_ID` INT)  BEGIN
	DECLARE _INFO VARCHAR(1000);
	SELECT CONCAT_WS('-',area.AreaName,building.BNo,household.RoomCode) INTO _INFO FROM area,building,household WHERE area.ID = building.AreaID AND household.BuildingID = building.ID AND household.ID = _HID;
	INSERT INTO householdpayment VALUES(null, _HID, _Name, _TEL, _PMC, _PRSF, _TF, _CPID, _TicketTime);
    SELECT LAST_INSERT_ID() INTO _ID;
	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增住户缴费： ',_INFO,'(', _TicketTime, ') 成功'), 'householdpayment', _ID);
	SELECT "新增成功！" INTO Result;
END$$

DROP PROCEDURE IF EXISTS `AddHouseHoldPaymentMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddHouseHoldPaymentMonth` (IN `opUserID` INT, IN `_PaymentId` INT, IN `_Year` INT, IN `_Month` INT, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE _INFO VARCHAR(1000);
    DECLARE _ID INT;
	SELECT CONCAT_WS('.',area.AreaName,building.BNo,household.RoomCode) INTO _INFO FROM area,building,householdpayment,household WHERE area.ID = building.AreaID AND building.ID = household.BuildingID AND household.ID = householdpayment.HouseHoldID AND householdpayment.HouseHoldID = _PaymentId LIMIT 1;
    INSERT INTO householdpaymentmonth VALUES(null, _PaymentId, _Year, _Month);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增住户缴费月份： ', _INFO, '-', _Year, '年', _Month, '月 成功'), 'householdpaymentmonth', _ID);
END$$

DROP PROCEDURE IF EXISTS `AddShop`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddShop` (IN `opUserID` INT, IN `_AreaID` INT, IN `_ShopName` VARCHAR(1000) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `_PMCU` VARCHAR(30) CHARSET utf8, IN `_ELU` VARCHAR(30) CHARSET utf8, IN `_TF` VARCHAR(30) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `_ID` INT)  BEGIN
	DECLARE _Name VARCHAR(1000);
    SELECT CONCAT_WS('-',area.AreaName,_ShopName) INTO _Name FROM area WHERE area.ID = _AreaID;
    INSERT INTO shop VALUES(null, _AreaID, _ShopName, Name, _TEL, _PMCU, _ELU, _TF);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增商铺： ',_Name, ' 成功'), 'shop', _ID);
    SELECT "新增成功！" INTO Result;
END$$

DROP PROCEDURE IF EXISTS `AddShopCarPayment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddShopCarPayment` (IN `opUserID` INT, IN `_SID` VARCHAR(30) CHARSET utf8, IN `_TicketTime` DATE, IN `_CID` INT, IN `_Fee` DOUBLE, IN `_CarCode` VARCHAR(1000) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `_ID` INT)  BEGIN
    DECLARE _AName VARCHAR(1000);
    DECLARE strDate VARCHAR(1000);
    DECLARE _AID INT;
    IF _CID = -1 THEN
    	SELECT area.AreaName, area.ID INTO _AName,_AID FROM area,shop WHERE area.ID = shop.AreaID AND shop.ID = _SID;
    	INSERT INTO car VALUES(null,_AID,_CarCode,_Name,_TEL);
        SELECT LAST_INSERT_ID() INTO _ID;
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增车辆： ',_AName,'-',_CarCode, ' 成功'), 'car', _ID);
    ELSE
    	SELECT _CID INTO _ID;
        SELECT area.AreaName INTO _AName FROM area,car WHERE area.ID = car.AreaID AND car.ID = _CID;
    END IF;
    INSERT INTO carpayment VALUES(null,_ID,_Fee,_TicketTime);
    SELECT LAST_INSERT_ID() INTO _ID;
    SELECT date_format(_TicketTime,'%Y-%c-%d') INTO strDate;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增商铺车辆缴费： ',_AName,'-',_CarCode,'-',_Fee,'(',strDate, ') 成功'), 'carpayment', _ID);
    SELECT "新增成功！" INTO Result;
END$$

DROP PROCEDURE IF EXISTS `AddShopCarPaymentMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddShopCarPaymentMonth` (IN `opUserID` INT, IN `_PaymentId` INT, IN `_Year` INT, IN `_Month` INT, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE _INFO VARCHAR(1000);
    DECLARE _ID INT;
	SELECT CONCAT_WS('.',area.AreaName,shop.ShopName,car.CarCode) INTO _INFO FROM carpayment,car,shoppayment,shop,area WHERE car.ID = carpayment.CarID AND area.ID = shop.AreaID AND shop.ID = shoppayment.ShopID AND carpayment.ID = shoppayment.CarPaymentID AND carpayment.ID = _PaymentId LIMIT 1;
    INSERT INTO carpaymentmonth VALUES(null, _PaymentId, _Year, _Month);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增商铺车辆缴费月份： ', _INFO, '-', _Year, '年', _Month, '月 成功'), 'carpaymentmonth', _ID);
END$$

DROP PROCEDURE IF EXISTS `AddShopPayment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddShopPayment` (IN `opUserID` INT, IN `_SID` VARCHAR(30) CHARSET utf8, IN `_SName` VARCHAR(1000) CHARSET utf8, IN `_TicketTime` DATE, IN `_CPID` INT, IN `_PMC` DOUBLE, IN `_ELE` DOUBLE, IN `_TF` DOUBLE, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `_ID` INT)  BEGIN
	DECLARE _INFO VARCHAR(1000);
	SELECT CONCAT_WS('-',area.AreaName,shop.ShopName) INTO _INFO FROM area,shop WHERE area.ID = shop.AreaID AND shop.ID = _SID;
	INSERT INTO shoppayment VALUES(null, _SID, _SName, _Name, _TEL, _PMC, _ELE, _TF, _CPID, _TicketTime);
    SELECT LAST_INSERT_ID() INTO _ID;
	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增商铺缴费： ',_INFO,'(', _TicketTime, ') 成功'), 'shoppayment', _ID);
	SELECT "新增成功！" INTO Result;
END$$

DROP PROCEDURE IF EXISTS `AddShopPaymentMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddShopPaymentMonth` (IN `opUserID` INT, IN `_PaymentId` INT, IN `_Year` INT, IN `_Month` INT, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE _INFO VARCHAR(1000);
    DECLARE _ID INT;
	SELECT CONCAT_WS('.',area.AreaName,shop.ShopName) INTO _INFO FROM area,shop,shoppayment WHERE area.ID = shop.AreaID AND shop.ID = shoppayment.ShopID AND shoppayment.ID = _PaymentId LIMIT 1;
    INSERT INTO shoppaymentmonth VALUES(null, _PaymentId, _Year, _Month);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增商铺缴费月份： ', _INFO, '-', _Year, '年', _Month, '月 成功'), 'shoppaymentmonth', _ID);
END$$

DROP PROCEDURE IF EXISTS `AddUser`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddUser` (IN `opUserID` INT, IN `UID` VARCHAR(30) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `Type` INT, IN `Password` VARCHAR(1000) CHARSET utf8, IN `Sec` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `_ID` INT)  BEGIN
	DECLARE c1 int;
    DECLARE c2 int;
    SELECT COUNT(*) INTO c1 FROM user WHERE user.UID = UID;
    SELECT COUNT(*) INTO c2 FROM user WHERE user.TEL = TEL;
    IF c1 <> 0 then 
    SELECT "身份证号码重复！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','尝试新增账号: ',CONCAT_WS('/',UID,TEL,Name),' 失败:身份证号码重复'), 'user', -1);
    ELSEIF c2 <> 0 then
    SELECT "电话号码重复！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','尝试新增账号: ',CONCAT_WS('/',UID,TEL,Name),' 失败:电话号码重复'), 'user', -1);
    ELSE
    INSERT INTO user VALUES(null, UID, TEL, Name, PASSWORD(Password), PASSWORD(Sec), Type, 0);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增账号： ',CONCAT_WS('/',UID,TEL,Name), ' 成功'), 'user', _ID);
    SELECT "新增成功！" INTO Result;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `AddUserArea`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `AddUserArea` (IN `opUserID` INT, IN `targetUserID` INT, IN `AreaID` INT, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE _ID INT;
	DECLARE _UID VARCHAR(30);
    DECLARE _TEL VARCHAR(30);
    DECLARE _Name VARCHAR(1000);
    DECLARE _strArea VARCHAR(1000);
    SELECT area.AreaName INTO _strArea FROM area WHERE area.ID = AreaID;
	SELECT UID,UserName,TEL INTO _UID,_Name,_TEL FROM user WHERE user.ID = targetUserID;
	INSERT INTO sa VALUES(null,targetUserID,AreaID);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','新增用户  ',CONCAT_WS('/',_UID,_TEL,_Name),' 所属管辖范围(楼盘)：',_strArea), 'sa', _ID);
END$$

DROP PROCEDURE IF EXISTS `BakTable`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `BakTable` (IN `opUserID` INT, IN `i_tbl` INT, IN `FolderPath` VARCHAR(2000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` DOUBLE)  BEGIN
DECLARE tblname VARCHAR(1000);
DECLARE totalRow DOUBLE;
DECLARE dbname VARCHAR(1000);
SELECT DATABASE() INTO dbname;
SELECT SUM(TABLE_ROWS) / 100 INTO totalRow FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = dbname;
SELECT TABLE_NAME,ROUND(TABLE_ROWS / totalRow,2) INTO tblname,Result FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = dbname ORDER BY TABLE_NAME LIMIT i_tbl, 1;
INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, '备份数据表', tblname, -1);
SET @strcon = CONCAT_WS('',"SELECT * FROM ",tblname," INTO OUTFILE '",FolderPath,tblname,".csv' fields terminated by ',' optionally enclosed by '\"' lines terminated by '\r\n'");
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `DeleteArea`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteArea` (IN `opUserID` INT, IN `targetAreaID` INT, IN `IP` VARCHAR(1000) CHARSET utf8, OUT `Result` INT)  BEGIN
    DECLARE _Name VARCHAR(1000);
    DECLARE _OperaMod VARCHAR(1000);
    SELECT area.AreaName INTO _Name FROM area WHERE area.ID = targetAreaID;
    SET _OperaMod = '楼盘列表-删除楼盘(及其它表中的记录)';
    DELETE FROM area WHERE area.ID = targetAreaID;
    SELECT ROW_COUNT() INTO Result;
    IF Result = 0 THEN
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除楼盘 ',_Name,' 失败'), 'area', -1);
    ELSE
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除楼盘 ',_Name, ' 成功'), 'area', targetAreaID);
    END IF;
END$$

DROP PROCEDURE IF EXISTS `DeleteBuilding`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteBuilding` (IN `opUserID` INT, IN `targetBuildingID` INT, IN `IP` VARCHAR(1000) CHARSET utf8, OUT `Result` INT)  BEGIN
    DECLARE _Name VARCHAR(1000);
    DECLARE _OperaMod VARCHAR(1000);
    SELECT CONCAT_WS('-',area.AreaName,building.BNo) INTO _Name FROM area,building WHERE area.ID = building.AreaID AND building.ID = targetBuildingID;
    SET _OperaMod = '楼栋列表-删除楼栋(及其它表中的记录)';
    DELETE FROM building WHERE building.ID = targetBuildingID;
    SELECT ROW_COUNT() INTO Result;
    IF Result = 0 THEN
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除楼栋 ',_Name,' 失败'), 'building', -1);
    ELSE
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除楼栋 ',_Name, ' 成功'), 'building', targetBuildingID);
    END IF;
END$$

DROP PROCEDURE IF EXISTS `DeleteCar`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteCar` (IN `opUserID` INT, IN `targetCarID` INT, IN `IP` VARCHAR(1000) CHARSET utf8, OUT `Result` INT)  BEGIN
    DECLARE _Name VARCHAR(1000);
    DECLARE _OperaMod VARCHAR(1000);
    SELECT CONCAT_WS('-',area.AreaName,car.CarCode) INTO _Name FROM area,car WHERE area.ID = car.AreaID AND car.ID = targetCarID;
    SET _OperaMod = '车辆信息-删除车辆(及其它表中的记录)';
    DELETE FROM car WHERE car.ID = targetCarID;
    SELECT ROW_COUNT() INTO Result;
    IF Result = 0 THEN
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除车辆 ',_Name,' 失败'), 'car', -1);
    ELSE
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除车辆 ',_Name, ' 成功'), 'car', targetCarID);
    END IF;
END$$

DROP PROCEDURE IF EXISTS `DeleteHouseHold`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteHouseHold` (IN `opUserID` INT, IN `targetHouseHoldID` INT, IN `IP` VARCHAR(1000) CHARSET utf8, OUT `Result` INT)  BEGIN
    DECLARE _Name VARCHAR(1000);
    DECLARE _OperaMod VARCHAR(1000);
    SELECT CONCAT_WS('-',area.AreaName,building.BNo,household.RoomCode) INTO _Name FROM area,building,household WHERE area.ID = building.AreaID AND building.ID = household.BuildingID AND household.ID = targetHouseHoldID;
    SET _OperaMod = '住户信息-删除住户(及其它表中的记录)';
    DELETE FROM household WHERE household.ID = targetHouseHoldID;
    SELECT ROW_COUNT() INTO Result;
    IF Result = 0 THEN
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除住户 ',_Name,' 失败'), 'household', -1);
    ELSE
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除住户 ',_Name, ' 成功'), 'household', targetHouseHoldID);
    END IF;
END$$

DROP PROCEDURE IF EXISTS `DeletePayment`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeletePayment` (IN `opUserID` INT, IN `PID` VARCHAR(30), IN `Type` VARCHAR(30), IN `_Cascade` VARCHAR(30), IN `IP` VARCHAR(1000) CHARSET utf8, OUT `Result` INT)  BEGIN
DECLARE CID INT;
DECLARE _Name VARCHAR(1000);
DECLARE _NameB VARCHAR(1000);
DECLARE _OperaMod VARCHAR(1000);
SET _OperaMod = '账目清单-删除账目';
CASE Type
	WHEN '0' THEN
    	SELECT CONCAT_WS('.',area.AreaName,building.BNo, household.RoomCode, GROUP_CONCAT(DISTINCT CONCAT(householdpaymentmonth.Year,'-',householdpaymentmonth.Month))) INTO _Name FROM household,householdpayment,householdpaymentmonth,area,building WHERE householdpayment.ID = householdpaymentmonth.PaymentID AND household.ID = householdpayment.HouseHoldID AND area.ID = building.AreaID AND building.ID = household.BuildingID AND householdpayment.CarPaymentID AND householdpayment.CarPaymentID = PID IS null GROUP BY householdpayment.ID;
    	DELETE FROM householdpayment WHERE householdpayment.ID = PID;
    	SELECT ROW_COUNT() INTO Result;
    	IF Result = 0 THEN
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除住户账目 ',_Name,' 失败'), 'householdpayment', -1);
    	ELSE
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除住户账目 ',_Name, ' 成功'), 'householdpayment', PID);
    	END IF;
    WHEN '1' THEN
    	SELECT carpayment.ID, CONCAT(CONCAT_WS('.',area.AreaName,building.BNo, household.RoomCode, GROUP_CONCAT(DISTINCT CONCAT(householdpaymentmonth.Year,'-',householdpaymentmonth.Month)))),CONCAT_WS('.',area.AreaName,car.CarCode,GROUP_CONCAT(DISTINCT CONCAT(carpaymentmonth.Year,'-',carpaymentmonth.Month))) INTO CID,_Name,_NameB FROM household,householdpayment,householdpaymentmonth,area,building,carpayment,carpaymentmonth,car WHERE householdpayment.ID = householdpaymentmonth.PaymentID AND household.ID = householdpayment.HouseHoldID AND area.ID = building.AreaID AND building.ID = household.BuildingID AND carpayment.ID = householdpayment.CarPaymentID AND car.ID = carpayment.CarID AND carpaymentmonth.PaymentID = carpayment.ID AND householdpayment.ID = PID GROUP BY householdpayment.ID;
    	DELETE FROM householdpayment WHERE householdpayment.ID = PID;
    	SELECT ROW_COUNT() INTO Result;
    	IF Result = 0 THEN
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除账目 ',_Name,' 失败'), 'householdpayment', -1);
    	ELSE
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除账目 ',_Name, ' 成功'), 'householdpayment', PID);
    	END IF;
        IF _Cascade = 1 THEN
        DELETE FROM carpayment WHERE carpayment.ID = CID;
    	SELECT ROW_COUNT() INTO Result;
    	IF Result = 0 THEN
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除车辆账目 ',_NameB,' 失败'), 'carpayment', -1);
    	ELSE
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除车辆账目 ',_NameB, ' 成功'), 'carpayment', CID);
    	END IF;
        END IF;
    WHEN '2' THEN
    	SELECT CONCAT_WS('.',area.AreaName,shop.ShopName, GROUP_CONCAT(DISTINCT CONCAT(shoppaymentmonth.Year,'-',shoppaymentmonth.Month))) INTO _Name FROM shop,shoppayment,shoppaymentmonth,area WHERE shoppayment.ID = shoppaymentmonth.PaymentID AND shop.ID = shoppayment.ShopID AND area.ID = shop.AreaID AND shoppayment.CarPaymentID IS null AND shoppayment.ID = PID GROUP BY shoppayment.ID;
    	DELETE FROM shoppayment WHERE shoppayment.ID = PID;
    	SELECT ROW_COUNT() INTO Result;
    	IF Result = 0 THEN
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除商铺账目 ',_Name,' 失败'), 'shoppayment', -1);
    	ELSE
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除商铺账目 ',_Name, ' 成功'), 'shoppayment', PID);
    	END IF;
    WHEN '3' THEN
    	SELECT carpayment.ID, CONCAT(CONCAT_WS('.',area.AreaName,shop.ShopName, GROUP_CONCAT(DISTINCT CONCAT(shoppaymentmonth.Year,'-',shoppaymentmonth.Month)))),CONCAT_WS('.',area.AreaName,car.CarCode,GROUP_CONCAT(DISTINCT CONCAT(carpaymentmonth.Year,'-',carpaymentmonth.Month))) INTO CID,_Name,_NameB FROM shop,shoppayment,shoppaymentmonth,area,carpayment,carpaymentmonth,car WHERE shoppayment.ID = shoppaymentmonth.PaymentID AND shop.ID = shoppayment.ShopID AND area.ID = shop.AreaID AND carpayment.ID = shoppayment.CarPaymentID AND car.ID = carpayment.CarID AND carpaymentmonth.PaymentID = carpayment.ID AND shoppayment.ID = PID GROUP BY shoppayment.ID;
    	DELETE FROM shoppayment WHERE shoppayment.ID = PID;
    	SELECT ROW_COUNT() INTO Result;
    	IF Result = 0 THEN
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除商铺账目 ',_Name,' 失败'), 'shoppayment', -1);
    	ELSE
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除商铺账目 ',_Name, ' 成功'), 'shoppayment', PID);
    	END IF;
        IF _Cascade = 1 THEN
        DELETE FROM carpayment WHERE carpayment.ID = CID;
    	SELECT ROW_COUNT() INTO Result;
    	IF Result = 0 THEN
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除车辆账目 ',_NameB,' 失败'), 'carpayment', -1);
    	ELSE
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除车辆账目 ',_NameB, ' 成功'), 'carpayment', CID);
    	END IF;
        END IF;
    WHEN '4' THEN
    	SELECT CONCAT_WS('.',area.AreaName,car.CarCode, GROUP_CONCAT(DISTINCT CONCAT(carpaymentmonth.Year,'-',carpaymentmonth.Month))) INTO _Name FROM car,carpayment,carpaymentmonth,area WHERE carpayment.ID = carpaymentmonth.PaymentID AND car.ID = carpayment.CarID AND area.ID = car.AreaID AND carpayment.ID = PID GROUP BY carpayment.ID;
        DELETE FROM carpayment WHERE carpayment.ID = PID;
    	SELECT ROW_COUNT() INTO Result;
    	IF Result = 0 THEN
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除车辆账目 ',_Name,' 失败'), 'carpayment', -1);
    	ELSE
    	INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除车辆账目 ',_Name, ' 成功'), 'carpayment', PID);
    	END IF;
    END CASE;
END$$

DROP PROCEDURE IF EXISTS `DeleteShop`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteShop` (IN `opUserID` INT, IN `targetShopID` INT, IN `IP` VARCHAR(1000) CHARSET utf8, OUT `Result` INT)  BEGIN
    DECLARE _Name VARCHAR(1000);
    DECLARE _OperaMod VARCHAR(1000);
    SELECT CONCAT_WS('-',area.AreaName,shop.ShopName) INTO _Name FROM area,shop WHERE area.ID = shop.AreaID AND shop.ID = targetShopID;
    SET _OperaMod = '商铺信息-删除商铺(及其它表中的记录)';
    DELETE FROM shop WHERE shop.ID = targetShopID;
    SELECT ROW_COUNT() INTO Result;
    IF Result = 0 THEN
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除商铺 ',_Name,' 失败'), 'shop', -1);
    ELSE
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除商铺 ',_Name, ' 成功'), 'shop', targetShopID);
    END IF;
END$$

DROP PROCEDURE IF EXISTS `DeleteUser`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUser` (IN `opUserID` INT, IN `targetUserID` INT, IN `IP` VARCHAR(1000) CHARSET utf8, OUT `Result` INT)  BEGIN
	DECLARE _UID VARCHAR(30);
    DECLARE _TEL VARCHAR(30);
    DECLARE _Name VARCHAR(1000);
    DECLARE _OperaMod VARCHAR(1000);
    SELECT UID, TEL, UserName INTO _UID,_TEL,_Name FROM user WHERE user.ID = targetUserID;
    SET _OperaMod = '用户管理-删除账号(及其它表中的记录)';
    DELETE FROM user WHERE user.ID = targetUserID;
    SELECT ROW_COUNT() INTO Result;
    IF Result = 0 THEN
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除用户 ',CONCAT_WS('/',_UID,_TEL,_Name),' 失败'), 'user', -1);
    ELSE
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, _OperaMod, CONCAT_WS('','删除用户 ',CONCAT_WS('/',_UID,_TEL,_Name),' 成功'), 'user', targetUserID);
    END IF;
END$$

DROP PROCEDURE IF EXISTS `DeleteUserArea`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `DeleteUserArea` (IN `opUserID` INT, IN `targetUserID` INT, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE _UID VARCHAR(30);
    DECLARE _TEL VARCHAR(30);
    DECLARE _Name VARCHAR(1000);
	SELECT UID,UserName,TEL INTO _UID,_Name,_TEL FROM user WHERE user.ID = targetUserID;
	DELETE FROM sa WHERE sa.UserID = targetUserID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','删除用户  ',CONCAT_WS('/',_UID,_TEL,_Name),' 所属管辖范围(楼盘)'), 'sa', -1);
END$$

DROP PROCEDURE IF EXISTS `GetAccountCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAccountCount` (OUT `AccountCount` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Type` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @p1=_UserID; SET @p2=Year; SET @p3=Month; SET @p4=Day; SET @p5=Name; SET @p6=TEL; SET @p7=AreaID;
CASE Type
	WHEN '' THEN
    	SET @p8=AddTarget; CALL `GetHouseHoldAccountCount`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8); SELECT @p0 INTO @HouseHoldAccountCount;
        SET @p8=AddTarget; CALL `GetHouseHoldCarAccountCount`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8); SELECT @p0 INTO @HouseHoldCarAccountCount;
        SET @p8=AddTarget; CALL `GetShopAccountCount`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8);
        SET @p8=AddTarget; CALL `GetShopCarAccountCount`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8);
        SET @p8=AddTarget; CALL `GetCarAccountCount`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8); SELECT @p0 INTO @CarAccountCount;
	WHEN '0' THEN
		SET @p8=AddTarget; CALL `GetHouseHoldAccountCount`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8); SELECT @p0 INTO @HouseHoldAccountCount;
    WHEN '1' THEN
    	SET @p8=AddTarget; CALL `GetHouseHoldCarAccountCount`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8); SELECT @p0 INTO @HouseHoldCarAccountCount;
    WHEN '2' THEN
    	SET @p8=AddTarget; CALL `GetShopAccountCount`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8); SELECT @p0 INTO @ShopAccountCount;
    WHEN '3' THEN
    	SET @p8=AddTarget; CALL `GetShopCarAccountCount`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8); SELECT @p0 INTO @ShopCarAccountCount;
    WHEN '4' THEN
    	SET @p8=AddTarget; CALL `GetCarAccountCount`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8); SELECT @p0 INTO @CarAccountCount;
    END CASE;
SELECT @CarAccountCount + @ShopCarAccountCount + @ShopAccountCount + @HouseHoldCarAccountCount + @HouseHoldAccountCount INTO AccountCount;
END$$

DROP PROCEDURE IF EXISTS `GetAccountList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAccountList` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Type` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @p0=Offset; SET @p1=Num; SET @p2=_UserID; SET @p3=Year; SET @p4=Month; SET @p5=Day; SET @p6=Name; SET @p7=TEL; SET @p8=AreaID; SET @p9=AddTarget;
CASE Type
	WHEN '0' THEN
		CALL `GetHouseHoldAccountList`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8, @p9);
    WHEN '1' THEN
    	CALL `GetHouseHoldCarAccountList`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8, @p9);
    WHEN '2' THEN
    	CALL `GetShopAccountList`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8, @p9);
    WHEN '3' THEN
    	CALL `GetShopCarAccountList`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8, @p9);
    WHEN '4' THEN
    	CALL `GetCarAccountList`(@p0, @p1, @p2, @p3, @p4, @p5, @p6, @p7, @p8, @p9);
    END CASE;
END$$

DROP PROCEDURE IF EXISTS `GetArea`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetArea` (IN `AreaID` INT, OUT `_AreaName` VARCHAR(1000) CHARSET utf8)  BEGIN
	SELECT area.AreaName INTO _AreaName FROM area WHERE area.ID = AreaID;
END$$

DROP PROCEDURE IF EXISTS `GetAreaCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAreaCount` (OUT `AreaCount` INT, IN `_Name` VARCHAR(1000) CHARSET utf8)  BEGIN
SELECT COUNT(*) into AreaCount FROM area WHERE area.AreaName like CONCAT_WS('%',_Name,'%');
END$$

DROP PROCEDURE IF EXISTS `GetAreaList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAreaList` (IN `Offset` INT, IN `Num` INT, IN `_Name` VARCHAR(1000) CHARSET utf8)  BEGIN
IF Num = 0 THEN
SELECT area.ID,area.AreaName FROM area WHERE area.AreaName LIKE CONCAT_WS('','%',_Name,'%');
ELSE
SELECT area.ID,area.AreaName FROM area WHERE area.AreaName LIKE CONCAT_WS('','%',_Name,'%') LIMIT Offset, Num;
END IF;
END$$

DROP PROCEDURE IF EXISTS `GetBuilding`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBuilding` (IN `BID` INT, OUT `_AreaID` INT, OUT `_BNo` VARCHAR(1000) CHARSET utf8, OUT `_PMCU` DOUBLE, OUT `_PRSF` DOUBLE, OUT `_TF` DOUBLE)  BEGIN
	SELECT building.AreaID,building.BNo,building.PMCU,building.PRSF,building.TF INTO _AreaID,_BNo,_PMCU,_PRSF,_TF FROM building WHERE building.ID = BID;
END$$

DROP PROCEDURE IF EXISTS `GetBuildingCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBuildingCount` (OUT `BuildingCount` INT, IN `_UserID` INT, IN `_AreaID` VARCHAR(30) CHARSET utf8, IN `_BNo` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(*) INTO @BuildingCount FROM area,building WHERE area.ID = building.AreaID AND building.BNo LIKE '%",_BNo,"%'");
IF _AreaID <> '' THEN
SET @strcon = CONCAT_WS('',@strcon, " AND area.ID = ",_AreaID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @BuildingCount INTO BuildingCount;
END$$

DROP PROCEDURE IF EXISTS `GetBuildingList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetBuildingList` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `_AreaID` VARCHAR(30) CHARSET utf8, IN `_BNo` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT building.ID,area.AreaName,building.BNo FROM area,building WHERE area.ID = building.AreaID AND building.BNo LIKE '%",_BNo,"%'");
IF _AreaID <> '' THEN
SET @strcon = CONCAT_WS('',@strcon, " AND area.ID = ",_AreaID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `GetCar`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCar` (IN `CID` INT, OUT `_AreaID` INT, OUT `_CarCode` VARCHAR(20) CHARSET utf8, OUT `_Name` VARCHAR(1000) CHARSET utf8, OUT `_TEL` VARCHAR(1000) CHARSET utf8)  BEGIN
	SELECT car.AreaID,car.CarCode,car.Name,car.Tel INTO _AreaID,_CarCode,_Name,_TEL FROM car WHERE car.ID = CID;
END$$

DROP PROCEDURE IF EXISTS `GetCarAccountCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCarAccountCount` (OUT `CarAccountCount` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(DISTINCT carpaymentmonth.PaymentID) INTO @CarAccountCount FROM carpaymentmonth,car,area,carpayment WHERE carpayment.ID NOT IN (SELECT DISTINCT householdpayment.CarPaymentID FROM householdpayment) AND carpayment.ID NOT IN (SELECT DISTINCT shoppayment.CarPaymentID FROM shoppayment) AND carpaymentmonth.PaymentID = carpayment.ID AND carpayment.CarID = car.ID AND car.AreaID = area.ID AND car.Name like '%",Name,"%' AND car.Tel like '%",TEL,"%' AND CONCAT_WS('-',area.AreaName,car.CarCode) like '%",AddTarget,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND area.ID = ',AreaID);
END IF;
IF Year <> "" THEN
SET @strcon = CONCAT(@strcon,' AND YEAR(carpayment.TimeStamp) = ',Year);
END IF;
IF Month <> "" THEN
SET @strcon = CONCAT(@strcon,' AND MONTH(carpayment.TimeStamp) = ',Month);
END IF;
IF Day <> "" THEN
SET @strcon = CONCAT(@strcon,' AND DAYOFMONTH(carpayment.TimeStamp) = ',Day);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND area.ID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @CarAccountCount INTO CarAccountCount;
END$$

DROP PROCEDURE IF EXISTS `GetCarAccountList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCarAccountList` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT carpayment.ID AS ID,carpayment.TimeStamp AS Time,4 AS Type,car.Name,car.Tel,CONCAT(CONCAT_WS('-',area.AreaName,car.CarCode),CONCAT('(',group_concat(DISTINCT CONCAT(carpaymentmonth.Year,'年',carpaymentmonth.Month,'月'))),')') AS Target,CONCAT('总金额',carpayment.Fee,'元') AS Content FROM carpaymentmonth,car,area,carpayment WHERE carpaymentmonth.PaymentID = carpayment.ID AND car.ID = carpayment.CarID AND car.AreaID = area.ID AND carpayment.ID NOT IN (SELECT DISTINCT householdpayment.CarPaymentID FROM householdpayment) AND carpayment.ID NOT IN (SELECT DISTINCT shoppayment.CarPaymentID FROM shoppayment) AND car.Name like '%",Name,"%' AND car.Tel like '%",TEL,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND car.AreaID = ',AreaID);
END IF;
IF Year <> "" THEN
SET @strcon = CONCAT(@strcon,' AND YEAR(carpayment.TimeStamp) = ',Year);
END IF;
IF Month <> "" THEN
SET @strcon = CONCAT(@strcon,' AND MONTH(carpayment.TimeStamp) = ',Month);
END IF;
IF Day <> "" THEN
SET @strcon = CONCAT(@strcon,' AND DAYOFMONTH(carpayment.TimeStamp) = ',Day);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND car.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
SET @strcon = CONCAT(@strcon," GROUP BY carpaymentmonth.PaymentID HAVING Target LIKE '%", AddTarget, "%'");
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `GetCarCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCarCount` (OUT `CarCount` INT, IN `_UserID` INT, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `_CarCode` VARCHAR(20) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(*) INTO @CarCount FROM car,area WHERE car.AreaID = area.ID AND car.CarCode like '%",_CarCode,"%' AND car.Name like '%",Name,"%' AND car.Tel like '%",TEL,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND car.AreaID = ',AreaID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND car.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @CarCount INTO CarCount;
END$$

DROP PROCEDURE IF EXISTS `GetCarCountByPaymentMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCarCountByPaymentMonth` (OUT `CarCount` INT, IN `_UserID` INT, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `CarCode` VARCHAR(1000) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `_Year` INT, IN `_Month` INT, IN `ShowPaid` INT)  BEGIN
IF _Year <> -1 THEN
SET @strcon = CONCAT_WS('',"SELECT COUNT(*) INTO @CarCount FROM car,area WHERE car.AreaID = area.ID AND car.CarCode like '%",CarCode,"%' AND car.Name like '%",Name,"%' AND car.Tel like '%",TEL,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND car.AreaID = ',AreaID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND car.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
IF ShowPaid <> 0 THEN
SET @strcon = CONCAT(@strcon,' AND car.ID IN (SELECT DISTINCT carpayment.CarID FROM carpayment,carpaymentmonth WHERE carpayment.ID = carpaymentmonth.PaymentID AND carpaymentmonth.Year = ',_Year,' AND carpaymentmonth.Month = ',_Month,')');
ELSE
SET @strcon = CONCAT(@strcon,' AND car.ID NOT IN (SELECT DISTINCT carpayment.CarID FROM carpayment,carpaymentmonth WHERE carpayment.ID = carpaymentmonth.PaymentID AND carpaymentmonth.Year = ',_Year,' AND carpaymentmonth.Month = ',_Month,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @CarCount INTO CarCount;
ELSE
SELECT 0 INTO CarCount;
END IF;
END$$

DROP PROCEDURE IF EXISTS `GetCarList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCarList` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `_AreaID` VARCHAR(30) CHARSET utf8, IN `_CarCode` VARCHAR(1000) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(30) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT car.ID,area.AreaName,car.CarCode,car.Name,car.Tel FROM car,area WHERE car.AreaID = area.ID AND car.CarCode LIKE '%",_CarCode,"%' AND car.Name LIKE'%",_Name,"%' AND car.Tel LIKE '%",_Tel,"%'");
IF _AreaID <> '' THEN
SET @strcon = CONCAT_WS('',@strcon, " AND area.ID = ",_AreaID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND car.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `GetCarListByPaymentMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCarListByPaymentMonth` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `_AreaID` VARCHAR(30) CHARSET utf8, IN `CarCode` VARCHAR(1000) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(30) CHARSET utf8, IN `_Year` INT, IN `_Month` INT, IN `ShowPaid` INT)  BEGIN
IF _Year <> -1 THEN
SET @YearMonth = CONCAT_WS('-',_Year,_Month);
SET @strcon = CONCAT_WS('',"SELECT DISTINCT car.ID,'",@YearMonth,"',area.AreaName,car.CarCode,car.Name,car.Tel FROM car,area WHERE car.AreaID = area.ID AND car.CarCode LIKE '%",CarCode,"%' AND car.Name LIKE'%",_Name,"%' AND car.Tel LIKE '%",_Tel,"%'");
IF _AreaID <> '' THEN
SET @strcon = CONCAT_WS('',@strcon, " AND area.ID = ",_AreaID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND car.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
IF ShowPaid <> 0 THEN
SET @strcon = CONCAT(@strcon,' AND car.ID IN (SELECT DISTINCT carpayment.CarID FROM carpayment,carpaymentmonth WHERE carpayment.ID = carpaymentmonth.PaymentID AND carpaymentmonth.Year = ',_Year,' AND carpaymentmonth.Month = ',_Month,')');
ELSE
SET @strcon = CONCAT(@strcon,' AND car.ID NOT IN (SELECT DISTINCT carpayment.CarID FROM carpayment,carpaymentmonth WHERE carpayment.ID = carpaymentmonth.PaymentID AND carpaymentmonth.Year = ',_Year,' AND carpaymentmonth.Month = ',_Month,')');
END IF;
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END IF;
END$$

DROP PROCEDURE IF EXISTS `GetCarSumFee`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetCarSumFee` (OUT `CarSumFee` DOUBLE, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `_CarCode` VARCHAR(20) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `startDate` DATE, IN `endDate` DATE)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT SUM(carpayment.Fee) INTO @CarSumFee FROM carpayment,car,area WHERE carpayment.CarID = car.ID AND car.AreaID = area.ID AND car.CarCode like '%",_CarCode,"%' AND car.Name like '%",Name,"%' AND car.Tel like '%",TEL,"%' AND datediff('",startDate,"',carpayment.TimeStamp) <= 0 AND datediff('",endDate,"',carpayment.TimeStamp) > 0");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND car.AreaID = ',AreaID);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @CarSumFee INTO CarSumFee;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHold`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHold` (IN `HID` INT, OUT `AreaID` INT, OUT `_BID` VARCHAR(30) CHARSET utf8, OUT `_RoomCode` VARCHAR(500) CHARSET utf8, OUT `Name` VARCHAR(1000) CHARSET utf8, OUT `_TEL` VARCHAR(1000) CHARSET utf8, OUT `_square` VARCHAR(1000) CHARSET utf8)  BEGIN
	SELECT building.AreaID,household.BuildingID,household.RoomCode,household.Name,household.Tel,household.Square INTO AreaID,_BID,_RoomCode,Name,_TEL,_square FROM household,building WHERE household.BuildingID = building.ID AND household.ID = HID;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHoldAccountCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHoldAccountCount` (OUT `HouseHoldAccountCount` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(DISTINCT householdpayment.ID) INTO @HouseHoldAccountCount FROM householdpaymentmonth,household,building,area,householdpayment WHERE householdpaymentmonth.PaymentID = householdpayment.ID AND householdpayment.HouseHoldID = household.ID AND household.BuildingID = building.ID AND building.AreaID = area.ID AND householdpayment.CarPaymentID IS null AND householdpayment.Name like '%",Name,"%' AND householdpayment.Tel like '%",TEL,"%' AND CONCAT_WS('-',area.AreaName,building.BNo,household.RoomCode) like '%",AddTarget,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID = ',AreaID);
END IF;
IF Year <> "" THEN
SET @strcon = CONCAT(@strcon,' AND YEAR(householdpayment.TimeStamp) = ',Year);
END IF;
IF Month <> "" THEN
SET @strcon = CONCAT(@strcon,' AND MONTH(householdpayment.TimeStamp) = ',Month);
END IF;
IF Day <> "" THEN
SET @strcon = CONCAT(@strcon,' AND DAYOFMONTH(householdpayment.TimeStamp) = ',Day);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @HouseHoldAccountCount INTO HouseHoldAccountCount;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHoldAccountList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHoldAccountList` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT("SELECT householdpayment.ID AS ID,householdpayment.TimeStamp AS Time,0 AS Type,householdpayment.Name,householdpayment.Tel,CONCAT(CONCAT_WS('-',area.AreaName,building.BNo,household.RoomCode),CONCAT('(',group_concat(DISTINCT CONCAT(householdpaymentmonth.Year,'年',householdpaymentmonth.Month,'月'))),')') AS Target,CONCAT('总金额',householdpayment.PMC + householdpayment.PRSF + householdpayment.TF,'元') AS Content FROM householdpaymentmonth,household,building,area,householdpayment WHERE householdpaymentmonth.PaymentID = householdpayment.ID AND householdpayment.CarPaymentID IS NULL AND householdpayment.HouseHoldID = household.ID AND household.BuildingID = building.ID AND building.AreaID = area.ID AND householdpayment.Name like '%",Name,"%' AND householdpayment.Tel like '%",TEL,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID = ',AreaID);
END IF;
IF Year <> "" THEN
SET @strcon = CONCAT(@strcon,' AND YEAR(householdpayment.TimeStamp) = ',Year);
END IF;
IF Month <> "" THEN
SET @strcon = CONCAT(@strcon,' AND MONTH(householdpayment.TimeStamp) = ',Month);
END IF;
IF Day <> "" THEN
SET @strcon = CONCAT(@strcon,' AND DAYOFMONTH(householdpayment.TimeStamp) = ',Day);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
SET @strcon = CONCAT(@strcon," GROUP BY householdpaymentmonth.PaymentID HAVING Target like '%", AddTarget, "%'");
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHoldCarAccountCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHoldCarAccountCount` (OUT `HouseHoldCarAccountCount` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(DISTINCT householdpayment.ID) INTO @HouseHoldCarAccountCount FROM householdpaymentmonth,householdpayment,household,building,area,carpayment,car WHERE householdpaymentmonth.PaymentID = householdpayment.ID AND householdpayment.HouseHoldID = household.ID AND household.BuildingID = building.ID AND building.AreaID = area.ID AND householdpayment.CarPaymentID = carpayment.ID AND car.ID = carpayment.CarID AND householdpayment.Name like '%",Name,"%' AND householdpayment.Tel like '%",TEL,"%' AND (CONCAT_WS('-',area.AreaName,building.BNo,household.RoomCode) like '%",AddTarget,"%' OR CONCAT(area.AreaName,'-',car.CarCode) like '%",AddTarget,"%')");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID = ',AreaID);
END IF;
IF Year <> "" THEN
SET @strcon = CONCAT(@strcon,' AND YEAR(householdpayment.TimeStamp) = ',Year);
END IF;
IF Month <> "" THEN
SET @strcon = CONCAT(@strcon,' AND MONTH(householdpayment.TimeStamp) = ',Month);
END IF;
IF Day <> "" THEN
SET @strcon = CONCAT(@strcon,' AND DAYOFMONTH(householdpayment.TimeStamp) = ',Day);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @HouseHoldCarAccountCount INTO HouseHoldCarAccountCount;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHoldCarAccountList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHoldCarAccountList` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT("SELECT householdpayment.ID AS ID,householdpayment.TimeStamp AS Time,1 AS Type,householdpayment.Name,householdpayment.Tel,CONCAT(CONCAT_WS('-',area.AreaName,building.BNo,household.RoomCode),CONCAT('(',group_concat(DISTINCT CONCAT(householdpaymentmonth.Year,'年',householdpaymentmonth.Month,'月'))),')、',CONCAT_WS('-',area.AreaName,car.CarCode),'(',group_concat(DISTINCT CONCAT(carpaymentmonth.Year,'年',carpaymentmonth.Month,'月')),')') AS Target,CONCAT('总金额',householdpayment.PMC + householdpayment.PRSF + householdpayment.TF + carpayment.Fee,'元','(其中车费',carpayment.Fee,'元)') AS Content FROM householdpaymentmonth,household,building,area,householdpayment,car,carpayment,carpaymentmonth WHERE householdpaymentmonth.PaymentID = householdpayment.ID AND householdpayment.CarPaymentID = carpayment.ID AND carpayment.ID = carpaymentmonth.PaymentID AND car.ID = carpayment.CarID AND householdpayment.HouseHoldID = household.ID AND household.BuildingID = building.ID AND building.AreaID = area.ID AND householdpayment.Name like '%",Name,"%' AND householdpayment.Tel like '%",TEL,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID = ',AreaID);
END IF;
IF Year <> "" THEN
SET @strcon = CONCAT(@strcon,' AND YEAR(householdpayment.TimeStamp) = ',Year);
END IF;
IF Month <> "" THEN
SET @strcon = CONCAT(@strcon,' AND MONTH(householdpayment.TimeStamp) = ',Month);
END IF;
IF Day <> "" THEN
SET @strcon = CONCAT(@strcon,' AND DAYOFMONTH(householdpayment.TimeStamp) = ',Day);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
SET @strcon = CONCAT(@strcon," GROUP BY householdpaymentmonth.PaymentID,carpaymentmonth.PaymentID HAVING Target like '%", AddTarget, "%'");
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHoldCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHoldCount` (OUT `HouseHoldCount` INT, IN `_UserID` INT, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `BuildingID` VARCHAR(30) CHARSET utf8, IN `RoomCode` VARCHAR(500) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `square` VARCHAR(30) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(*) INTO @HouseHoldCount FROM household,building,area WHERE household.BuildingID = building.ID AND building.AreaID = area.ID AND household.Name like '%",Name,"%' AND household.RoomCode like '%",RoomCode,"%' AND household.Tel like '%",TEL,"%' AND household.Square like '%",square,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID = ',AreaID);
END IF;
IF BuildingID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND building.ID = ',BuildingID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @HouseHoldCount INTO HouseHoldCount;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHoldCountByPaymentMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHoldCountByPaymentMonth` (OUT `HouseHoldCount` INT, IN `_UserID` INT, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `BuildingID` VARCHAR(30) CHARSET utf8, IN `RoomCode` VARCHAR(500) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `square` VARCHAR(30) CHARSET utf8, IN `_Year` INT, IN `_Month` INT, IN `ShowPaid` INT)  BEGIN
IF _Year <> -1 THEN
SET @strcon = CONCAT_WS('',"SELECT COUNT(*) INTO @HouseHoldCount FROM household,building,area WHERE household.BuildingID = building.ID AND building.AreaID = area.ID AND household.Name like '%",Name,"%' AND household.RoomCode like '%",RoomCode,"%' AND household.Tel like '%",TEL,"%' AND household.Square like '%",square,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID = ',AreaID);
END IF;
IF BuildingID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND building.ID = ',BuildingID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
IF ShowPaid <> 0 THEN
SET @strcon = CONCAT(@strcon,' AND household.ID IN (SELECT DISTINCT householdpayment.HouseHoldID FROM householdpayment,householdpaymentmonth WHERE householdpayment.ID = householdpaymentmonth.PaymentID AND householdpaymentmonth.Year = ',_Year,' AND householdpaymentmonth.Month = ',_Month,')');
ELSE
SET @strcon = CONCAT(@strcon,' AND household.ID NOT IN (SELECT DISTINCT householdpayment.HouseHoldID FROM householdpayment,householdpaymentmonth WHERE householdpayment.ID = householdpaymentmonth.PaymentID AND householdpaymentmonth.Year = ',_Year,' AND householdpaymentmonth.Month = ',_Month,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @HouseHoldCount INTO HouseHoldCount;
ELSE
SELECT 0 INTO HouseHoldCount;
END IF;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHoldList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHoldList` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `_AreaID` VARCHAR(30) CHARSET utf8, IN `_BID` VARCHAR(30) CHARSET utf8, IN `_RoomCode` VARCHAR(500) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `_square` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT household.ID,area.AreaName,building.BNo,household.RoomCode,household.Name FROM area,building,household WHERE area.ID = building.AreaID AND building.ID = household.BuildingID AND household.RoomCode LIKE '%",_RoomCode,"%' AND household.Name LIKE'%",_Name,"%' AND household.Tel LIKE '%",_Tel,"%' AND household.square LIKE '%",_square,"%'");
IF _AreaID <> '' THEN
SET @strcon = CONCAT_WS('',@strcon, " AND area.ID = ",_AreaID);
END IF;
IF _BID <> '' THEN
SET @strcon = CONCAT_WS('',@strcon, " AND building.ID = ",_BID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHoldListByPaymentMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHoldListByPaymentMonth` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `_AreaID` VARCHAR(30) CHARSET utf8, IN `_BID` VARCHAR(30) CHARSET utf8, IN `_RoomCode` VARCHAR(500) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `_square` VARCHAR(1000) CHARSET utf8, IN `_Year` INT, IN `_Month` INT, IN `ShowPaid` INT)  BEGIN
IF _Year <> -1 THEN
SET @YearMonth = CONCAT_WS('-',_Year,_Month);
SET @strcon = CONCAT_WS('',"SELECT DISTINCT household.ID,'",@YearMonth,"',area.AreaName,building.BNo,household.RoomCode,household.Name,household.Tel FROM area,building,household WHERE area.ID = building.AreaID AND building.ID = household.BuildingID AND household.RoomCode LIKE '%",_RoomCode,"%' AND household.Name LIKE'%",_Name,"%' AND household.Tel LIKE '%",_Tel,"%' AND household.square LIKE '%",_square,"%'");
IF _AreaID <> '' THEN
SET @strcon = CONCAT_WS('',@strcon, " AND area.ID = ",_AreaID);
END IF;
IF _BID <> '' THEN
SET @strcon = CONCAT_WS('',@strcon, " AND building.ID = ",_BID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
IF ShowPaid <> 0 THEN
SET @strcon = CONCAT(@strcon,' AND household.ID IN (SELECT DISTINCT householdpayment.HouseHoldID FROM householdpayment,householdpaymentmonth WHERE householdpayment.ID = householdpaymentmonth.PaymentID AND householdpaymentmonth.Year = ',_Year,' AND householdpaymentmonth.Month = ',_Month,')');
ELSE
SET @strcon = CONCAT(@strcon,' AND household.ID NOT IN (SELECT DISTINCT householdpayment.HouseHoldID FROM householdpayment,householdpaymentmonth WHERE householdpayment.ID = householdpaymentmonth.PaymentID AND householdpaymentmonth.Year = ',_Year,' AND householdpaymentmonth.Month = ',_Month,')');
END IF;
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END IF;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHoldNotCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHoldNotCount` (OUT `HouseHoldNotCount` INT, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `BuildingID` VARCHAR(30) CHARSET utf8, IN `RoomCode` VARCHAR(500) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `square` VARCHAR(30) CHARSET utf8, IN `startDate` DATE, IN `endDate` DATE)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(*) INTO @HouseHoldNotCount FROM area AS B,building AS C,household AS A WHERE B.ID = C.AreaID AND C.ID = A.BuildingID AND A.Name like '%",Name,"%' AND A.RoomCode like '%",RoomCode,"%' AND A.Tel like '%",TEL,"%' AND A.Square like '%",square,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT_WS('',@strcon,' AND B.ID = ',AreaID);
END IF;
IF BuildingID <> "" THEN
SET @strcon = CONCAT_WS('',@strcon,' AND C.ID = ',BuildingID);
END IF;
SET @strcon = CONCAT_WS('',@strcon, " AND A.ID NOT IN (SELECT DISTINCT D.ID FROM ((household AS D LEFT JOIN householdpayment ON D.ID = householdpayment.HouseHoldID) LEFT JOIN householdpaymentmonth ON householdpayment.ID = householdpaymentmonth.PaymentID) WHERE  datediff('",startDate,"',DATE_FORMAT(CONCAT(householdpaymentmonth.Year,'-',householdpaymentmonth.Month,'-',1),'%Y-%m-%d')) <= 0 AND datediff('",endDate,"',DATE_FORMAT(CONCAT(householdpaymentmonth.Year,'-',householdpaymentmonth.Month,'-',1),'%Y-%m-%d')) > 0)");
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @HouseHoldNotCount INTO HouseHoldNotCount;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHoldPay`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHoldPay` (OUT `PMC` DOUBLE, OUT `PRSF` DOUBLE, OUT `TF` DOUBLE, IN `_HID` VARCHAR(30) CHARSET utf8, IN `_Count` INT)  BEGIN
SELECT building.PMCU * _Count, building.PRSF, building.TF INTO PMC,PRSF,TF FROM household,building WHERE building.ID = household.BuildingID AND household.ID = _HID;
END$$

DROP PROCEDURE IF EXISTS `GetHouseHoldSumFee`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetHouseHoldSumFee` (OUT `HouseHoldSumFee` DOUBLE, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `BuildingID` VARCHAR(30) CHARSET utf8, IN `RoomCode` VARCHAR(500) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `square` VARCHAR(30) CHARSET utf8, IN `startDate` DATE, IN `endDate` DATE)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT SUM(householdpayment.PMC+householdpayment.PRSF+householdpayment.TF) INTO @HouseHoldSumFee FROM householdpayment,household,building,area WHERE householdpayment.HouseHoldID = household.ID AND household.BuildingID = building.ID AND building.AreaID = area.ID AND householdpayment.Name like '%",Name,"%' AND household.RoomCode like '%",RoomCode,"%' AND householdpayment.Tel like '%",TEL,"%' AND household.Square like '%",square,"%' AND datediff('",startDate,"',householdpayment.TimeStamp) <= 0 AND datediff('",endDate,"',householdpayment.TimeStamp) > 0");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND building.AreaID = ',AreaID);
END IF;
IF BuildingID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND building.ID = ',BuildingID);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @HouseHoldSumFee INTO HouseHoldSumFee;
END$$

DROP PROCEDURE IF EXISTS `GetLogCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetLogCount` (OUT `LogCount` INT, IN `_Time` VARCHAR(30) CHARSET utf8, IN `_IP` VARCHAR(1000) CHARSET utf8, IN `_OpName` VARCHAR(1000) CHARSET utf8, IN `_UID` VARCHAR(30) CHARSET utf8, IN `_ModName` VARCHAR(1000) CHARSET utf8, IN `_tblName` VARCHAR(200) CHARSET utf8, IN `_Action` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(*) INTO @LogCount FROM operationlog WHERE operationlog.IP LIKE '%",_IP,"%' AND operationlog.OperatorID IN (SELECT user.ID FROM user WHERE user.UserName LIKE '%",_OpName,"%') AND operationlog.OperatorID IN (SELECT user.ID FROM user WHERE user.UID LIKE '%",_UID,"%') AND operationlog.OperaMod LIKE '%",_ModName,"%'  AND operationlog.Action LIKE '%",_Action,"%' AND operationlog.RefTbl LIKE '%",_tblName,"%'");
IF _Time = "0" THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 1');
ELSEIF _Time = '1' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 3');
ELSEIF _Time = '2' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 7');
ELSEIF _Time = '3' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 30');
ELSEIF _Time = '4' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 90');
ELSEIF _Time = '5' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 180');
ELSEIF _Time = '6' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 365');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @LogCount INTO LogCount;
END$$

DROP PROCEDURE IF EXISTS `GetLogList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetLogList` (IN `Offset` INT, IN `_Time` VARCHAR(30) CHARSET utf8, IN `_IP` VARCHAR(1000) CHARSET utf8, IN `_OpName` VARCHAR(1000) CHARSET utf8, IN `_UID` VARCHAR(30) CHARSET utf8, IN `_ModName` VARCHAR(1000) CHARSET utf8, IN `_tblName` VARCHAR(200) CHARSET utf8, IN `_Action` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT operationlog.ID,operationlog.TimeStamp,operationlog.IP,user.UserName,user.UID,operationlog.OperaMod,Action,RefTbl,operationlog.RefID FROM operationlog,user WHERE operationlog.OperatorID = user.ID AND operationlog.IP LIKE '%",_IP,"%' AND operationlog.OperatorID IN (SELECT user.ID FROM user WHERE user.UserName LIKE '%",_OpName,"%') AND operationlog.OperatorID IN (SELECT user.ID FROM user WHERE user.UID LIKE '%",_UID,"%') AND operationlog.OperaMod LIKE '%",_ModName,"%'  AND operationlog.Action LIKE '%",_Action,"%' AND operationlog.RefTbl LIKE '%",_tblName,"%'");
IF _Time = "0" THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 1');
ELSEIF _Time = '1' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 3');
ELSEIF _Time = '2' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 7');
ELSEIF _Time = '3' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 30');
ELSEIF _Time = '4' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 90');
ELSEIF _Time = '5' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 180');
ELSEIF _Time = '6' THEN
SET @strcon = CONCAT(@strcon,' AND datediff(CURRENT_TIMESTAMP(),operationlog.TimeStamp) < 365');
END IF;
SET @strcon = CONCAT(@strcon, ' ORDER BY operationlog.TimeStamp DESC LIMIT ',Offset,',10');
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `GetModNameList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetModNameList` ()  BEGIN
SELECT DISTINCT operationlog.OperaMod FROM operationlog;
END$$

DROP PROCEDURE IF EXISTS `GetNormalUserCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetNormalUserCount` (OUT `UserCount` INT, IN `_UID` VARCHAR(30) CHARSET utf8, IN `_TEL` VARCHAR(30) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_Type` VARCHAR(30) CHARSET utf8, IN `_Online` VARCHAR(30) CHARSET utf8, IN `_Area` VARCHAR(30) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(*) into @UserCount FROM user  WHERE user.ID <> -1 AND user.Type <> 5 AND user.UID like '%",_UID,"%' AND user.TEL LIKE '%",_TEL,"%' AND user.UserName LIKE '%",_Name,"%'");
IF _Type <> '' THEN
SET @strcon = CONCAT(@strcon,' AND user.Type = ',_Type);
END IF;
IF _Online <> '' THEN
SET @strcon = CONCAT(@strcon, ' AND user.Online = ',_Online);
END IF;
IF _Area <> '' THEN
SET @strcon = CONCAT(@strcon, ' AND ',_Area,' in (select Area.ID from Area,sa where Area.ID = sa.AreaID and sa.UserID = user.ID)');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @UserCount INTO UserCount;
END$$

DROP PROCEDURE IF EXISTS `GetRegCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetRegCount` (OUT `UserCount` INT, IN `_UID` VARCHAR(30) CHARSET utf8, IN `_TEL` VARCHAR(30) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8)  BEGIN
SELECT COUNT(*) into UserCount FROM user WHERE user.ID <> -1 AND user.Type = 5 AND user.UID like CONCAT_WS('%',_UID,'%') AND user.TEL LIKE CONCAT_WS('%',_TEL,'%') AND user.UserName LIKE CONCAT_WS('%',_Name,'%');
END$$

DROP PROCEDURE IF EXISTS `GetRegList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetRegList` (IN `Offset` INT, IN `_UID` VARCHAR(30) CHARSET utf8, IN `_TEL` VARCHAR(30) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8)  BEGIN
SELECT user.ID, UID, TEL, UserName FROM user WHERE user.ID <> -1 AND user.Type = 5 AND user.UID like CONCAT_WS('','%',_UID,'%') AND user.TEL LIKE CONCAT_WS('','%',_TEL,'%') AND user.UserName LIKE CONCAT_WS('','%',_Name,'%') ORDER BY user.ID LIMIT Offset, 10;
SET @strcon = CONCAT(@strcon, ' ORDER BY user.ID LIMIT ',Offset,',10');
END$$

DROP PROCEDURE IF EXISTS `GetShop`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShop` (IN `SID` INT, OUT `_AreaID` INT, OUT `_ShopName` VARCHAR(1000) CHARSET utf8, OUT `_Name` VARCHAR(1000) CHARSET utf8, OUT `_TEL` VARCHAR(1000) CHARSET utf8, OUT `_PMCU` VARCHAR(30) CHARSET utf8, OUT `_ELU` VARCHAR(30) CHARSET utf8, OUT `_TF` VARCHAR(30) CHARSET utf8)  BEGIN
	SELECT shop.AreaID,shop.ShopName,shop.Name,shop.Tel,shop.PMCU,shop.ELU,shop.TF INTO _AreaID,_ShopName,_Name,_TEL,_PMCU,_ELU,_TF FROM shop WHERE shop.ID = SID;
END$$

DROP PROCEDURE IF EXISTS `GetShopAccountCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShopAccountCount` (OUT `ShopAccountCount` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(DISTINCT shoppayment.ID) INTO @ShopAccountCount FROM shoppaymentmonth,shop,area,shoppayment WHERE shoppaymentmonth.PaymentID = shoppayment.ID AND shoppayment.ShopID = shop.ID AND shop.AreaID = area.ID AND shoppayment.CarPaymentID IS null AND shoppayment.Name like '%",Name,"%' AND shoppayment.Tel like '%",TEL,"%' AND CONCAT_WS('-',area.AreaName,shoppayment.ShopName) like '%",AddTarget,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND area.ID = ',AreaID);
END IF;
IF Year <> "" THEN
SET @strcon = CONCAT(@strcon,' AND YEAR(shoppayment.TimeStamp) = ',Year);
END IF;
IF Month <> "" THEN
SET @strcon = CONCAT(@strcon,' AND MONTH(shoppayment.TimeStamp) = ',Month);
END IF;
IF Day <> "" THEN
SET @strcon = CONCAT(@strcon,' AND DAYOFMONTH(shoppayment.TimeStamp) = ',Day);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND area.ID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @ShopAccountCount INTO ShopAccountCount;
END$$

DROP PROCEDURE IF EXISTS `GetShopAccountList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShopAccountList` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT shoppayment.ID AS ID,shoppayment.TimeStamp AS Time,2 AS Type,shoppayment.Name,shoppayment.Tel,CONCAT(CONCAT_WS('-',area.AreaName,shop.ShopName),CONCAT('(',group_concat(DISTINCT CONCAT(shoppaymentmonth.Year,'年',shoppaymentmonth.Month,'月'))),')') AS Target,CONCAT('总金额',shoppayment.PMC + shoppayment.ELE + shoppayment.TF,'元') AS Content FROM shoppaymentmonth,shop,area,shoppayment WHERE shoppaymentmonth.PaymentID = shoppayment.ID AND shoppayment.CarPaymentID IS NULL AND shoppayment.ShopID = shop.ID AND shop.AreaID = area.ID AND shoppayment.Name like '%",Name,"%' AND shoppayment.Tel like '%",TEL,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND shop.AreaID = ',AreaID);
END IF;
IF Year <> "" THEN
SET @strcon = CONCAT(@strcon,' AND YEAR(shoppayment.TimeStamp) = ',Year);
END IF;
IF Month <> "" THEN
SET @strcon = CONCAT(@strcon,' AND MONTH(shoppayment.TimeStamp) = ',Month);
END IF;
IF Day <> "" THEN
SET @strcon = CONCAT(@strcon,' AND DAYOFMONTH(shoppayment.TimeStamp) = ',Day);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND shop.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
SET @strcon = CONCAT(@strcon," GROUP BY shoppaymentmonth.PaymentID HAVING Target LIKE '%", AddTarget, "%'");
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `GetShopCarAccountCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShopCarAccountCount` (OUT `ShopCarAccountCount` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(DISTINCT shoppayment.ID) INTO @ShopCarAccountCount FROM shoppaymentmonth,shop,area,shoppayment,carpayment,car WHERE shoppaymentmonth.PaymentID = shoppayment.ID AND shoppayment.ShopID = shop.ID AND shop.AreaID = area.ID AND shoppayment.CarPaymentID = carpayment.ID AND car.ID = carpayment.CarID AND shoppayment.Name like '%",Name,"%' AND shoppayment.Tel like '%",TEL,"%' AND (CONCAT_WS('-',area.AreaName,shoppayment.ShopName) like '%",AddTarget,"%' OR CONCAT(area.AreaName,'-',car.CarCode) like '%",AddTarget,"%')");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND area.ID = ',AreaID);
END IF;
IF Year <> "" THEN
SET @strcon = CONCAT(@strcon,' AND YEAR(shoppayment.TimeStamp) = ',Year);
END IF;
IF Month <> "" THEN
SET @strcon = CONCAT(@strcon,' AND MONTH(shoppayment.TimeStamp) = ',Month);
END IF;
IF Day <> "" THEN
SET @strcon = CONCAT(@strcon,' AND DAYOFMONTH(shoppayment.TimeStamp) = ',Day);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND area.ID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @ShopCarAccountCount INTO ShopCarAccountCount;
END$$

DROP PROCEDURE IF EXISTS `GetShopCarAccountList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShopCarAccountList` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `Year` VARCHAR(30) CHARSET utf8, IN `Month` VARCHAR(30) CHARSET utf8, IN `Day` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `AddTarget` VARCHAR(1000) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT shoppayment.ID AS ID,shoppayment.TimeStamp AS Time,3 AS Type,shoppayment.Name,shoppayment.Tel,CONCAT(CONCAT_WS('-',area.AreaName,shop.ShopName),CONCAT('(',group_concat(DISTINCT CONCAT(shoppaymentmonth.Year,'年',shoppaymentmonth.Month,'月'))),')、',CONCAT_WS('-',area.AreaName,car.CarCode),'(',group_concat(DISTINCT CONCAT(carpaymentmonth.Year,'年',carpaymentmonth.Month,'月')),')') AS Target,CONCAT('总金额',shoppayment.PMC + shoppayment.ELE + shoppayment.TF + carpayment.Fee,'元','(其中车费',carpayment.Fee,'元)') AS Content FROM shoppaymentmonth,shop,area,shoppayment,car,carpayment,carpaymentmonth WHERE shoppaymentmonth.PaymentID = shoppayment.ID AND shoppayment.CarPaymentID = carpayment.ID AND carpayment.ID = carpaymentmonth.PaymentID AND car.ID = carpayment.CarID AND shoppayment.ShopID = shop.ID AND shop.AreaID = area.ID AND shoppayment.Name like '%",Name,"%' AND shoppayment.Tel like '%",TEL,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND shop.AreaID = ',AreaID);
END IF;
IF Year <> "" THEN
SET @strcon = CONCAT(@strcon,' AND YEAR(shoppayment.TimeStamp) = ',Year);
END IF;
IF Month <> "" THEN
SET @strcon = CONCAT(@strcon,' AND MONTH(shoppayment.TimeStamp) = ',Month);
END IF;
IF Day <> "" THEN
SET @strcon = CONCAT(@strcon,' AND DAYOFMONTH(shoppayment.TimeStamp) = ',Day);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND shop.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
SET @strcon = CONCAT(@strcon," GROUP BY shoppaymentmonth.PaymentID,carpaymentmonth.PaymentID HAVING Target LIKE '%", AddTarget, "%'");
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `GetShopCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShopCount` (OUT `ShopCount` INT, IN `_UserID` INT, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `ShopName` VARCHAR(1000) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(*) INTO @ShopCount FROM shop,area WHERE shop.AreaID = area.ID AND shop.ShopName like '%",ShopName,"%' AND shop.Name like '%",Name,"%' AND shop.Tel like '%",TEL,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND shop.AreaID = ',AreaID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND shop.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @ShopCount INTO ShopCount;
END$$

DROP PROCEDURE IF EXISTS `GetShopCountByPaymentMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShopCountByPaymentMonth` (OUT `ShopCount` INT, IN `_UserID` INT, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `ShopName` VARCHAR(1000) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `_Year` INT, IN `_Month` INT, IN `ShowPaid` INT)  BEGIN
IF _Year <> -1 THEN
SET @strcon = CONCAT_WS('',"SELECT COUNT(*) INTO @ShopCount FROM shop,area WHERE shop.AreaID = area.ID AND shop.ShopName like '%",ShopName,"%' AND shop.Name like '%",Name,"%' AND shop.Tel like '%",TEL,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND shop.AreaID = ',AreaID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND shop.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
IF ShowPaid <> 0 THEN
SET @strcon = CONCAT(@strcon,' AND shop.ID IN (SELECT DISTINCT shoppayment.ShopID FROM shoppayment,shoppaymentmonth WHERE shoppayment.ID = shoppaymentmonth.PaymentID AND shoppaymentmonth.Year = ',_Year,' AND shoppaymentmonth.Month = ',_Month,')');
ELSE
SET @strcon = CONCAT(@strcon,' AND shop.ID NOT IN (SELECT DISTINCT shoppayment.ShopID FROM shoppayment,shoppaymentmonth WHERE shoppayment.ID = shoppaymentmonth.PaymentID AND shoppaymentmonth.Year = ',_Year,' AND shoppaymentmonth.Month = ',_Month,')');
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @ShopCount INTO ShopCount;
ELSE
SELECT 0 INTO ShopCount;
END IF;
END$$

DROP PROCEDURE IF EXISTS `GetShopList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShopList` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `_AreaID` VARCHAR(30) CHARSET utf8, IN `_ShopName` VARCHAR(1000) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(30) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT shop.ID,area.AreaName,shop.ShopName,shop.Name,shop.Tel FROM shop,area WHERE shop.AreaID = area.ID AND shop.ShopName LIKE '%",_ShopName,"%' AND shop.Name LIKE'%",_Name,"%' AND shop.Tel LIKE '%",_Tel,"%'");
IF _AreaID <> '' THEN
SET @strcon = CONCAT_WS('',@strcon, " AND area.ID = ",_AreaID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND shop.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `GetShopListByPaymentMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShopListByPaymentMonth` (IN `Offset` INT, IN `Num` INT, IN `_UserID` INT, IN `_AreaID` VARCHAR(30) CHARSET utf8, IN `_ShopName` VARCHAR(1000) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(30) CHARSET utf8, IN `_Year` INT, IN `_Month` INT, IN `ShowPaid` INT)  BEGIN
IF _Year <> -1 THEN
SET @YearMonth = CONCAT_WS('-',_Year,_Month);
SET @strcon = CONCAT_WS('',"SELECT DISTINCT shop.ID,'",@YearMonth,"',area.AreaName,shop.ShopName,shop.Name,shop.Tel FROM shop,area WHERE shop.AreaID = area.ID AND shop.ShopName LIKE '%",_ShopName,"%' AND shop.Name LIKE'%",_Name,"%' AND shop.Tel LIKE '%",_Tel,"%'");
IF _AreaID <> '' THEN
SET @strcon = CONCAT_WS('',@strcon, " AND area.ID = ",_AreaID);
END IF;
IF _UserID <> -1 THEN
SET @strcon = CONCAT(@strcon,' AND shop.AreaID IN (SELECT sa.AreaID FROM sa WHERE sa.UserID = ',_UserID,')');
END IF;
IF ShowPaid <> 0 THEN
SET @strcon = CONCAT(@strcon,' AND shop.ID IN (SELECT DISTINCT shoppayment.ShopID FROM shoppayment,shoppaymentmonth WHERE shoppayment.ID = shoppaymentmonth.PaymentID AND shoppaymentmonth.Year = ',_Year,' AND shoppaymentmonth.Month = ',_Month,')');
ELSE
SET @strcon = CONCAT(@strcon,' AND shop.ID NOT IN (SELECT DISTINCT shoppayment.ShopID FROM shoppayment,shoppaymentmonth WHERE shoppayment.ID = shoppaymentmonth.PaymentID AND shoppaymentmonth.Year = ',_Year,' AND shoppaymentmonth.Month = ',_Month,')');
END IF;
IF Num <> 0 THEN
SET @strcon = CONCAT_WS('',@strcon, " LIMIT ",Offset, ",",Num);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
END IF;
END$$

DROP PROCEDURE IF EXISTS `GetShopNotCount`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShopNotCount` (OUT `ShopNotCount` INT, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `ShopName` VARCHAR(1000) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `startDate` DATE, IN `endDate` DATE)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT COUNT(*) INTO @ShopNotCount FROM area AS B,shop AS A WHERE B.ID = A.AreaID AND A.ShopName like '%",ShopName,"%' AND A.Name like '%",Name,"%' AND A.Tel like '%",TEL,"%'");
IF AreaID <> "" THEN
SET @strcon = CONCAT_WS('',@strcon,' AND B.ID = ',AreaID);
END IF;
SET @strcon = CONCAT_WS('',@strcon, " AND A.ID NOT IN (SELECT DISTINCT D.ID FROM ((shop AS D LEFT JOIN shoppayment ON D.ID = shoppayment.ShopID) LEFT JOIN shoppaymentmonth ON shoppayment.ID = shoppaymentmonth.PaymentID) WHERE  datediff(",startDate,",DATE_FORMAT(CONCAT(shoppaymentmonth.Year,'-',shoppaymentmonth.Month,'-',1),'%Y-%m-%d')) <= 0 AND datediff(",endDate,",DATE_FORMAT(CONCAT(shoppaymentmonth.Year,'-',shoppaymentmonth.Month,'-',1),'%Y-%m-%d')) > 0)");
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @ShopNotCount INTO ShopNotCount;
END$$

DROP PROCEDURE IF EXISTS `GetShopPay`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShopPay` (OUT `PMC` DOUBLE, OUT `ELE` DOUBLE, OUT `TF` DOUBLE, IN `_SID` VARCHAR(30) CHARSET utf8, IN `_Count` INT)  BEGIN
SELECT shop.PMCU * _Count, shop.ELU * _Count, shop.TF INTO PMC,ELE,TF FROM shop WHERE shop.ID = _SID;
END$$

DROP PROCEDURE IF EXISTS `GetShopSumFee`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetShopSumFee` (OUT `ShopSumFee` DOUBLE, IN `AreaID` VARCHAR(30) CHARSET utf8, IN `ShopName` VARCHAR(1000) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `startDate` DATE, IN `endDate` DATE)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT SUM(shoppayment.PMC+shoppayment.ELE+shoppayment.TF) INTO @ShopSumFee FROM shoppayment,shop,area WHERE shoppayment.ShopID = shop.ID AND shop.AreaID = area.ID AND shoppayment.ShopName like '%",ShopName,"%' AND shoppayment.Name like '%",Name,"%' AND shoppayment.Tel like '%",TEL,"%' AND datediff('",startDate,"',shoppayment.TimeStamp) <= 0 AND datediff('",endDate,"',shoppayment.TimeStamp) > 0");
IF AreaID <> "" THEN
SET @strcon = CONCAT(@strcon,' AND shop.AreaID = ',AreaID);
END IF;
PREPARE stmt from @strcon;
EXECUTE stmt;
SELECT @ShopSumFee INTO ShopSumFee;
END$$

DROP PROCEDURE IF EXISTS `GetTblList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTblList` ()  BEGIN
DECLARE totalRow DOUBLE;
DECLARE dbname VARCHAR(1000);
SELECT DATABASE() INTO dbname;
SELECT SUM(TABLE_ROWS) / 100 INTO totalRow FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = dbname;
SELECT TABLE_NAME,TABLE_COMMENT,TABLE_ROWS,ROUND(TABLE_ROWS / totalRow,2) AS TABLE_RATES FROM INFORMATION_SCHEMA.TABLES WHERE table_schema = dbname ORDER BY TABLE_NAME;
END$$

DROP PROCEDURE IF EXISTS `GetUser`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUser` (IN `UserID` INT, OUT `_UID` VARCHAR(30) CHARSET utf8, OUT `_TEL` VARCHAR(30) CHARSET utf8, OUT `_UserName` VARCHAR(1000) CHARSET utf8, OUT `_strType` VARCHAR(1000) CHARSET utf8, OUT `_Online` INT, OUT `_intType` INT)  BEGIN
    SELECT user.UID,user.TEL,user.UserName,TypeName,user.Online,user.Type INTO _UID,_TEL,_UserName,_strType,_Online,_intType FROM user,usertype WHERE user.ID = UserID AND user.Type = usertype.Type;
END$$

DROP PROCEDURE IF EXISTS `GetUserAccountYearList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserAccountYearList` (IN `_UserID` INT)  BEGIN
SELECT DISTINCT Year FROM (SELECT DISTINCT YEAR(householdpayment.TimeStamp) AS Year FROM sa,building,household,householdpayment WHERE sa.UserID = _UserID AND building.AreaID = sa.AreaID AND household.BuildingID = building.ID AND householdpayment.HouseHoldID = household.ID UNION ALL SELECT DISTINCT YEAR(shoppayment.TimeStamp) AS Year FROM sa,shop,shoppayment WHERE sa.UserID = 1 AND shop.AreaID = sa.AreaID AND shoppayment.ShopID = shop.ID UNION ALL SELECT DISTINCT YEAR(carpayment.TimeStamp) AS Year FROM sa,car,carpayment WHERE sa.UserID = 1 AND car.AreaID = sa.AreaID AND carpayment.CarID = car.ID) AS tmp;
END$$

DROP PROCEDURE IF EXISTS `GetUserAreaList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserAreaList` (IN `_UserID` INT)  BEGIN
SELECT area.ID,area.AreaName FROM area,sa WHERE sa.AreaID = area.ID AND sa.UserID = _UserID;
END$$

DROP PROCEDURE IF EXISTS `GetUserList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserList` (IN `Offset` INT, IN `_UID` VARCHAR(30) CHARSET utf8, IN `_TEL` VARCHAR(30) CHARSET utf8, IN `_Name` VARCHAR(1000) CHARSET utf8, IN `_Type` VARCHAR(30) CHARSET utf8, IN `_Online` VARCHAR(30) CHARSET utf8, IN `_Area` VARCHAR(30) CHARSET utf8)  BEGIN
SET @strcon = CONCAT_WS('',"SELECT user.ID, UID, TEL, UserName, usertype.TypeName, user.Online FROM user,usertype WHERE user.Type = usertype.Type AND user.ID <> -1 AND user.Type <> 5 AND user.UID like '%",_UID,"%' AND user.TEL LIKE '%",_TEL,"%' AND user.UserName LIKE '%",_Name,"%'");
IF _Type <> '' THEN
SET @strcon = CONCAT(@strcon,' AND user.Type = ',_Type);
END IF;
IF _Online <> '' THEN
SET @strcon = CONCAT(@strcon, ' AND user.Online = ',_Online);
END IF;
IF _Area <> '' THEN
SET @strcon = CONCAT(@strcon, ' AND ',_Area,' in (select Area.ID from Area,sa where Area.ID = sa.AreaID and sa.UserID = user.ID)');
END IF;
SET @strcon = CONCAT(@strcon, ' ORDER BY user.ID LIMIT ',Offset,',10');
PREPARE stmt from @strcon;
EXECUTE stmt;
END$$

DROP PROCEDURE IF EXISTS `GetUserTypeList`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserTypeList` ()  BEGIN
SELECT usertype.Type,usertype.TypeName FROM usertype WHERE usertype.Type <> -1 AND usertype.Type <> 5;
END$$

DROP PROCEDURE IF EXISTS `GetYearMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetYearMonth` (IN `_UserID` INT, IN `Target` INT)  BEGIN
IF Target = 0 THEN
SELECT DISTINCT householdpaymentmonth.Year,householdpaymentmonth.Month FROM sa,building,household,householdpayment,householdpaymentmonth WHERE sa.UserID = _UserID AND sa.AreaID = building.AreaID AND building.ID = household.BuildingID AND householdpayment.HouseHoldID = household.ID AND householdpaymentmonth.PaymentID = householdpayment.ID;
ELSEIF Target = 1 THEN
SELECT DISTINCT shoppaymentmonth.Year,shoppaymentmonth.Month FROM sa,shop,shoppayment,shoppaymentmonth WHERE sa.UserID = _UserID AND sa.AreaID = shop.AreaID AND shoppayment.ShopID = shop.ID AND shoppaymentmonth.PaymentID = shoppayment.ID;
ELSE
SELECT DISTINCT carpaymentmonth.Year,carpaymentmonth.Month FROM sa,car,carpayment,carpaymentmonth WHERE sa.UserID = _UserID AND sa.AreaID = car.AreaID AND carpayment.CarID = car.ID AND carpaymentmonth.PaymentID = carpayment.ID;
END IF;
END$$

DROP PROCEDURE IF EXISTS `IsCarPaidMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `IsCarPaidMonth` (IN `_CID` INT, IN `_Year` INT, IN `_Month` INT, OUT `Result` INT)  NO SQL
    COMMENT '判断车辆某月份是否已缴费'
BEGIN
SELECT IF(COUNT(*) = 0, 0, 1) INTO Result FROM carpaymentmonth,carpayment WHERE carpaymentmonth.PaymentID = carpayment.ID AND carpayment.CarID = _CID AND carpaymentmonth.Year = _Year AND carpaymentmonth.Month = _Month;
END$$

DROP PROCEDURE IF EXISTS `IsHouseHoldPaidMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `IsHouseHoldPaidMonth` (IN `_HID` INT, IN `_Year` INT, IN `_Month` INT, OUT `Result` INT)  NO SQL
    COMMENT '判断住户某月份是否已缴费'
BEGIN
SELECT IF(COUNT(*) = 0, 0, 1) INTO Result FROM householdpaymentmonth,householdpayment WHERE householdpaymentmonth.PaymentID = householdpayment.ID AND householdpayment.HouseHoldID = _HID AND householdpaymentmonth.Year = _Year AND householdpaymentmonth.Month = _Month;
END$$

DROP PROCEDURE IF EXISTS `IsLegalArea`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `IsLegalArea` (IN `UserID` INT, IN `AreaID` INT, OUT `Result` INT)  NO SQL
    COMMENT '判断用户对楼盘的访问是否合法'
BEGIN
SELECT IF(COUNT(*) = 0, 0, 1) INTO Result FROM sa WHERE sa.UserID = UserID AND sa.AreaID = AreaID;
END$$

DROP PROCEDURE IF EXISTS `IsLegalBuilding`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `IsLegalBuilding` (IN `UserID` INT, IN `BID` INT, OUT `Result` INT)  NO SQL
    COMMENT '判断用户对楼栋的访问是否合法'
BEGIN
SELECT IF(COUNT(*) = 0, 0, 1) INTO Result FROM sa,building WHERE sa.AreaID = building.AreaID AND sa.UserID = UserID AND building.ID = BID;
END$$

DROP PROCEDURE IF EXISTS `IsLegalCar`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `IsLegalCar` (IN `UserID` INT, IN `CID` INT, OUT `Result` INT)  NO SQL
    COMMENT '判断用户对车辆的访问是否合法'
BEGIN
SELECT IF(COUNT(*) = 0, 0, 1) INTO Result FROM sa,car WHERE sa.AreaID = car.AreaID AND sa.UserID = UserID AND car.ID = CID;
END$$

DROP PROCEDURE IF EXISTS `IsLegalHouseHold`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `IsLegalHouseHold` (IN `UserID` INT, IN `HID` INT, OUT `Result` INT)  NO SQL
    COMMENT '判断用户对住户的访问是否合法'
BEGIN
SELECT IF(COUNT(*) = 0, 0, 1) INTO Result FROM sa,building,household WHERE sa.AreaID = building.AreaID AND sa.UserID = UserID AND building.ID = household.BuildingID AND household.ID = HID;
END$$

DROP PROCEDURE IF EXISTS `IsLegalShop`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `IsLegalShop` (IN `UserID` INT, IN `SID` INT, OUT `Result` INT)  NO SQL
    COMMENT '判断用户对商铺的访问是否合法'
BEGIN
SELECT IF(COUNT(*) = 0, 0, 1) INTO Result FROM sa,shop WHERE sa.AreaID = shop.AreaID AND sa.UserID = UserID AND shop.ID = SID;
END$$

DROP PROCEDURE IF EXISTS `IsShopPaidMonth`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `IsShopPaidMonth` (IN `_SID` INT, IN `_Year` INT, IN `_Month` INT, OUT `Result` INT)  NO SQL
    COMMENT '判断商铺某月份是否已缴费'
BEGIN
SELECT IF(COUNT(*) = 0, 0, 1) INTO Result FROM shoppaymentmonth,shoppayment WHERE shoppaymentmonth.PaymentID = shoppayment.ID AND shoppayment.ShopID = _SID AND shoppaymentmonth.Year = _Year AND shoppaymentmonth.Month = _Month;
END$$

DROP PROCEDURE IF EXISTS `SetAreaInfo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SetAreaInfo` (IN `opUserID` INT, IN `targetAreaID` INT, IN `Name` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE c1 INT;
    DECLARE _Name VARCHAR(1000);
    SELECT area.AreaName INTO _Name FROM area WHERE area.ID = targetAreaID;
    SELECT COUNT(*) INTO c1 FROM area WHERE area.ID = targetAreaID;
    IF c1 = 0 THEN
    SELECT "楼盘不存在！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','楼盘 ',_Name,' 修改信息为： ',Name,' 失败:楼盘不存在'), 'area', -1);
    ELSE
    UPDATE area SET area.AreaName = Name WHERE area.ID = targetAreaID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','楼盘 ',_Name,' 修改信息为： ',Name,' 成功'), 'area', targetAreaID);
    SELECT "修改成功！" INTO Result;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `SetBuildingInfo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SetBuildingInfo` (IN `opUserID` INT, IN `targetBuildingID` INT, IN `AreaID` INT, IN `_BNo` VARCHAR(1000) CHARSET utf8, IN `_PMCU` DOUBLE, IN `_PRSF` DOUBLE, IN `_TF` DOUBLE, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE c1 INT;
    DECLARE c2 INT;
    DECLARE _Name VARCHAR(2000);
    DECLARE Name VARCHAR(2000);
    SELECT CONCAT_WS('/',area.AreaName,building.BNo) INTO _Name FROM area,building WHERE area.ID = building.AreaID AND building.ID = targetBuildingID;
    SELECT CONCAT_WS('/',area.AreaName,_BNo) INTO Name FROM area WHERE area.ID = AreaID;
    SELECT COUNT(*) INTO c1 FROM building WHERE building.ID = targetBuildingID;
    SELECT COUNT(*) INTO c2 FROM area WHERE area.ID = AreaID;
    IF c1 = 0 THEN
    SELECT "楼栋不存在！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','楼栋 ',_Name,' 修改信息为： ',Name,' 失败:楼栋不存在'), 'building', -1);
    ELSE
    IF c2 = 0 THEN
    SELECT "楼盘不存在！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','楼栋 ',_Name,' 修改信息为： ',Name,' 失败:楼盘不存在'), 'building', -1);
    ELSE
    UPDATE building SET building.BNo = _BNo,building.AreaID = AreaID,building.PMCU = _PMCU,building.PRSF = _PRSF,building.TF = _TF WHERE building.ID = targetBuildingID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','楼栋 ',_Name,' 修改信息为： ',Name,' 成功'), 'building', targetBuildingID);
    SELECT "修改成功！" INTO Result;
    END IF;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `SetCarInfo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SetCarInfo` (IN `opUserID` INT, IN `targetCarID` INT, IN `_AreaID` INT, IN `_CarCode` VARCHAR(20) CHARSET utf8, IN `CName` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE c1 INT;
    DECLARE c2 INT;
    DECLARE _Name VARCHAR(2000);
    DECLARE Name VARCHAR(2000);
    SELECT CONCAT_WS('/',area.AreaName,car.CarCode) INTO _Name FROM area,car WHERE area.ID = car.AreaID AND car.ID = targetCarID;
    SELECT CONCAT_WS('/',area.AreaName,_CarCode) INTO Name FROM area WHERE area.ID = _AreaID;
    SELECT COUNT(*) INTO c1 FROM car WHERE car.ID = targetCarID;
    SELECT COUNT(*) INTO c2 FROM area WHERE area.ID = _AreaID;
    IF c1 = 0 THEN
    SELECT "车辆不存在！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','车辆 ',_Name,' 修改信息为： ',Name,' 失败:车辆不存在'), 'car', -1);
    ELSE
    IF c2 = 0 THEN
    SELECT "楼盘不存在！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','车辆 ',_Name,' 修改信息为： ',Name,' 失败:楼盘不存在'), 'car', -1);
    ELSE
    UPDATE car SET car.AreaID = _AreaID,car.CarCode = _CarCode,car.Name = CName,car.Tel = _TEL WHERE car.ID = targetCarID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','车辆 ',_Name,' 修改信息为： ',Name,' 成功'), 'car', targetCarID);
    SELECT "修改成功！" INTO Result;
    END IF;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `SetHouseHoldInfo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SetHouseHoldInfo` (IN `opUserID` INT, IN `targetHouseHoldID` INT, IN `AreaID` INT, IN `_BID` VARCHAR(30) CHARSET utf8, IN `_RoomCode` VARCHAR(500) CHARSET utf8, IN `HName` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `_square` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE c1 INT;
    DECLARE c2 INT;
    DECLARE c3 INT;
    DECLARE _Name VARCHAR(2000);
    DECLARE Name VARCHAR(2000);
    SELECT CONCAT_WS('/',area.AreaName,building.BNo,household.RoomCode) INTO _Name FROM area,building,household WHERE area.ID = building.AreaID AND building.ID = household.BuildingID AND household.ID = targetHouseHoldID;
    SELECT CONCAT_WS('/',area.AreaName,building.BNo,_RoomCode) INTO Name FROM area,building WHERE area.ID = building.AreaID AND building.ID = _BID;
    SELECT COUNT(*) INTO c1 FROM building WHERE building.ID = _BID;
    SELECT COUNT(*) INTO c2 FROM area WHERE area.ID = AreaID;
    SELECT COUNT(*) INTO c3 FROM household WHERE household.ID = targetHouseHoldID;
    IF c1 = 0 THEN
    SELECT "楼栋不存在！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','住户 ',_Name,' 修改信息为： ',Name,' 失败:楼栋不存在'), 'household', -1);
    ELSE
    IF c2 = 0 THEN
    SELECT "楼盘不存在！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','住户 ',_Name,' 修改信息为： ',Name,' 失败:楼盘不存在'), 'household', -1);
    ELSE
    IF c3 = 0 THEN
    SELECT "住户不存在！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','住户 ',_Name,' 修改信息为： ',Name,' 失败:住户不存在'), 'household', -1);
    ELSE
    UPDATE household SET household.BuildingID = _BID,household.RoomCode = _RoomCode,household.Name = HName,household.Tel = _TEL,household.Square = _square WHERE household.ID = targetHouseHoldID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','住户 ',_Name,' 修改信息为： ',Name,' 成功'), 'household', targetHouseHoldID);
    SELECT "修改成功！" INTO Result;
    END IF;
    END IF;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `SetPersonalInfo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SetPersonalInfo` (IN `opUserID` INT, IN `targetUserID` INT, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `UID` VARCHAR(30) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE c1 int;
    DECLARE c2 int;
	DECLARE _UID VARCHAR(30);
    DECLARE _TEL VARCHAR(30);
    DECLARE _Name VARCHAR(30);
    SELECT UID, TEL, UserName INTO _UID,_TEL,_Name FROM user WHERE user.ID = targetUserID;
    SELECT COUNT(*) INTO c1 FROM user WHERE user.UID = UID AND user.ID <> targetUserID;
    SELECT COUNT(*) INTO c2 FROM user WHERE user.TEL = TEL AND user.ID <> targetUserID;
    IF c1 <> 0 then 
    SELECT "身份证号码重复！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','用户 ',CONCAT_WS('/',_UID,_TEL,_Name),' 修改个人信息为： ',CONCAT_WS('/',UID,TEL,Name),' 失败:身份证号码重复'), 'user', -1);
    ELSEIF c2 <> 0 then
    SELECT "电话号码重复！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod,  CONCAT_WS('','用户 ',CONCAT_WS('/',_UID,_TEL,_Name),' 修改个人信息为： ',CONCAT_WS('/',UID,TEL,Name),' 失败:电话号码重复'), 'user', -1);
    ELSE
    UPDATE user SET user.UID = UID, user.TEL = TEL, user.UserName = Name WHERE user.ID = targetUserID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','用户 ',CONCAT_WS('/',_UID,_TEL,_Name),' 修改个人信息为： ',CONCAT_WS('/',UID,TEL,Name),' 成功'), 'user', targetUserID);
    SELECT "修改成功！" INTO Result;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `SetShopInfo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SetShopInfo` (IN `opUserID` INT, IN `targetShopID` INT, IN `_AreaID` INT, IN `_ShopName` VARCHAR(1000) CHARSET utf8, IN `SName` VARCHAR(1000) CHARSET utf8, IN `_TEL` VARCHAR(1000) CHARSET utf8, IN `_PMCU` VARCHAR(30) CHARSET utf8, IN `_ELU` VARCHAR(30) CHARSET utf8, IN `_TF` VARCHAR(30) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE c1 INT;
    DECLARE c2 INT;
    DECLARE _Name VARCHAR(2000);
    DECLARE Name VARCHAR(2000);
    SELECT CONCAT_WS('/',area.AreaName,shop.ShopName) INTO _Name FROM area,shop WHERE area.ID = shop.AreaID AND shop.ID = targetShopID;
    SELECT CONCAT_WS('/',area.AreaName,_ShopName) INTO Name FROM area WHERE area.ID = _AreaID;
    SELECT COUNT(*) INTO c1 FROM shop WHERE shop.ID = targetShopID;
    SELECT COUNT(*) INTO c2 FROM area WHERE area.ID = _AreaID;
    IF c1 = 0 THEN
    SELECT "商铺不存在！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','商铺 ',_Name,' 修改信息为： ',Name,' 失败:商铺不存在'), 'shop', -1);
    ELSE
    IF c2 = 0 THEN
    SELECT "楼盘不存在！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','商铺 ',_Name,' 修改信息为： ',Name,' 失败:楼盘不存在'), 'shop', -1);
    ELSE
    UPDATE shop SET shop.AreaID = _AreaID,shop.ShopName = _ShopName,shop.Name = SName,shop.Tel = _TEL,shop.PMCU = _PMCU,shop.ELU = _ELU,shop.TF = _TF WHERE shop.ID = targetShopID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','商铺 ',_Name,' 修改信息为： ',Name,' 成功'), 'shop', targetShopID);
    SELECT "修改成功！" INTO Result;
    END IF;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `SetUserInfo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SetUserInfo` (IN `opUserID` INT, IN `targetUserID` INT, IN `Name` VARCHAR(1000) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `UID` VARCHAR(30) CHARSET utf8, IN `Type` VARCHAR(30) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE c1 int;
    DECLARE c2 int;
	DECLARE _UID VARCHAR(30);
    DECLARE _TEL VARCHAR(30);
    DECLARE _Name VARCHAR(30);
    DECLARE _strType VARCHAR(1000);
    DECLARE _nowstrType VARCHAR(1000);
    SELECT COUNT(*) INTO c1 FROM user WHERE user.ID = targetUserID;
    IF c1 = 0 THEN
    SELECT "用户不存在！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','用户 ',CONCAT_WS('/',_UID,_TEL,_Name,_nowstrType),' 修改个人信息为： ',CONCAT_WS('/',UID,TEL,Name,_strType),' 失败:用户不存在'), 'user', -1);
    ELSE
    SELECT usertype.TypeName INTO _nowstrType FROM usertype,user WHERE user.Type = usertype.Type AND user.ID = targetUserID;
    SELECT usertype.TypeName INTO _strType FROM usertype WHERE usertype.Type = Type;
    SELECT UID, TEL, UserName INTO _UID,_TEL,_Name FROM user WHERE user.ID = targetUserID;
    SELECT COUNT(*) INTO c1 FROM user WHERE user.UID = UID AND user.ID <> targetUserID;
    SELECT COUNT(*) INTO c2 FROM user WHERE user.TEL = TEL AND user.ID <> targetUserID;
    IF c1 <> 0 then 
    SELECT "身份证号码重复！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','用户 ',CONCAT_WS('/',_UID,_TEL,_Name,_nowstrType),' 修改个人信息为： ',CONCAT_WS('/',UID,TEL,Name,_strType),' 失败:身份证号码重复'), 'user', -1);
    ELSEIF c2 <> 0 then
    SELECT "电话号码重复！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod,  CONCAT_WS('','用户 ',CONCAT_WS('/',_UID,_TEL,_Name,_nowstrType),' 修改个人信息为： ',CONCAT_WS('/',UID,TEL,Name,_strType),' 失败:电话号码重复'), 'user', -1);
    ELSE
    UPDATE user SET user.UID = UID, user.TEL = TEL, user.UserName = Name, user.Type = Type WHERE user.ID = targetUserID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','用户 ',CONCAT_WS('/',_UID,_TEL,_Name,_nowstrType),' 修改个人信息为： ',CONCAT_WS('/',UID,TEL,Name,_strType),' 成功'), 'user', targetUserID);
    SELECT "修改成功！" INTO Result;
    END IF;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `SetUserPassword`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SetUserPassword` (IN `opUserID` INT, IN `targetUserID` INT, IN `Password` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE _UID VARCHAR(30);
    DECLARE _TEL VARCHAR(30);
    DECLARE _Name VARCHAR(30);
    SELECT UID, TEL, UserName INTO _UID,_TEL,_Name FROM user WHERE user.ID = targetUserID;
    UPDATE user SET user._Password = PASSWORD(Password) WHERE user.ID = targetUserID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','用户 ',CONCAT_WS('/',_UID,_TEL,_Name),' 的密码被修改'), 'user', targetUserID);
END$$

DROP PROCEDURE IF EXISTS `SetUserSec`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SetUserSec` (IN `opUserID` INT, IN `targetUserID` INT, IN `Sec` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE _UID VARCHAR(30);
    DECLARE _TEL VARCHAR(30);
    DECLARE _Name VARCHAR(30);
    SELECT UID, TEL, UserName INTO _UID,_TEL,_Name FROM user WHERE user.ID = targetUserID;
    UPDATE user SET user.SecStr = PASSWORD(Sec) WHERE user.ID = targetUserID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','用户 ',CONCAT_WS('/',_UID,_TEL,_Name),' 的安全字符串被修改'), 'user', targetUserID);
END$$

DROP PROCEDURE IF EXISTS `SignOut`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SignOut` (IN `opUserID` INT, IN `targetUserID` INT, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE _UID VARCHAR(30);
    DECLARE _TEL VARCHAR(30);
    DECLARE _Name VARCHAR(30);
    SELECT UID, TEL, UserName INTO _UID,_TEL,_Name FROM user WHERE user.ID = targetUserID;
    UPDATE user SET user.Online = 0 WHERE user.ID = targetUserID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','用户 ',CONCAT_WS('/',_UID,_TEL,_Name),' 注销'), 'user', targetUserID);
END$$

DROP PROCEDURE IF EXISTS `SubmitUserApply`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `SubmitUserApply` (IN `opUserID` INT, IN `UID` VARCHAR(30) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `Password` VARCHAR(1000) CHARSET utf8, IN `Sec` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8)  BEGIN
	DECLARE c1 int;
    DECLARE c2 int;
    DECLARE _ID int;
    SELECT COUNT(*) INTO c1 FROM user WHERE user.UID = UID;
    SELECT COUNT(*) INTO c2 FROM user WHERE user.TEL = TEL;
    IF c1 <> 0 then 
    SELECT "身份证号码重复！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','尝试申请账号: ',CONCAT_WS('/',UID,TEL,Name),' 失败:身份证号码重复'), 'user', -1);
    ELSEIF c2 <> 0 then
    SELECT "电话号码重复！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','尝试新增账号: ',CONCAT_WS('/',UID,TEL,Name),' 失败:电话号码重复'), 'user', -1);
    ELSE
    INSERT INTO user VALUES(null, UID, TEL, Name, PASSWORD(Password), PASSWORD(Sec), 5, 0);
    SELECT LAST_INSERT_ID() INTO _ID;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, opUserID, OperaMod, CONCAT_WS('','申请账号： ',CONCAT_WS('/',UID,TEL,Name), ' 成功'), 'user', _ID);
    SELECT "申请完成，请等待审核通过！" INTO Result;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `VerifyUser`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `VerifyUser` (IN `UIDTEL` VARCHAR(30) CHARSET utf8, IN `_Password` VARCHAR(30) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `outUserID` INT)  BEGIN
    DECLARE c int;
    DECLARE _ID int;
    SELECT COUNT(*) INTO c FROM user WHERE (user.UID = UIDTEL OR user.TEL = UIDTEL) AND PASSWORD(_Password) = user._Password AND user.Online = 0 AND 0 = locate('禁用',(SELECT TypeName FROM user,usertype WHERE user.Type = usertype.Type AND (user.UID = UIDTEL OR user.TEL = UIDTEL))) AND user.ID <> -1 AND user.Type <> 5;
    IF c = 0 then 
    SELECT "用户名或密码错误！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, -1, OperaMod, CONCAT_WS('','尝试登录: ',UIDTEL,' 失败'), 'user', -1);
    ELSE
    SELECT "登录成功！" INTO Result;
    SELECT ID INTO _ID FROM user WHERE (user.UID = UIDTEL OR user.TEL = UIDTEL);
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, -1, OperaMod, CONCAT_WS('','尝试登录: ',UIDTEL,' 成功'), 'user', _ID);
    UPDATE user SET user.Online = 1 WHERE user.ID = _ID;
    SELECT _ID INTO outUserID;
    END IF;
END$$

DROP PROCEDURE IF EXISTS `VerifyUserInfo`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `VerifyUserInfo` (IN `UID` VARCHAR(30) CHARSET utf8, IN `TEL` VARCHAR(30) CHARSET utf8, IN `Name` VARCHAR(1000) CHARSET utf8, IN `Sec` VARCHAR(1000) CHARSET utf8, IN `IP` VARCHAR(1000) CHARSET utf8, IN `OperaMod` VARCHAR(1000) CHARSET utf8, OUT `Result` VARCHAR(1000) CHARSET utf8, OUT `outUserID` INT)  BEGIN
    DECLARE c int;
    DECLARE _ID int;
    SELECT COUNT(*) INTO c FROM user WHERE user.UID = UID AND user.TEL = TEL AND user.UserName = Name AND PASSWORD(Sec) = user.SecStr AND 0 = locate('禁用',(SELECT TypeName FROM user,usertype WHERE user.Type = usertype.Type AND user.UID = UID AND user.TEL = TEL)) AND user.ID <> -1;
    IF c = 0 then 
    SELECT "鉴定身份有误！" INTO Result;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, -1, OperaMod, CONCAT_WS('','找回账号-验证身份： ',CONCAT_WS('/',UID,TEL,Name),' 失败'), 'user', -1);
    ELSE
    SELECT "验证通过！" INTO Result;
    SELECT ID INTO _ID FROM user WHERE user.UID = UID AND user.TEL = TEL;
    INSERT INTO operationlog VALUES(null, CURRENT_TIMESTAMP(), IP, -1, OperaMod, CONCAT_WS('','找回账号-验证身份：',CONCAT_WS('/',UID,TEL,Name),' 成功'), 'user', _ID);
    SELECT _ID INTO outUserID;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- 表的结构 `area`
--

DROP TABLE IF EXISTS `area`;
CREATE TABLE IF NOT EXISTS `area` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `AreaName` varchar(1000) NOT NULL COMMENT '楼盘/管辖范围名称',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='楼盘/管辖范围';

-- --------------------------------------------------------

--
-- 表的结构 `building`
--

DROP TABLE IF EXISTS `building`;
CREATE TABLE IF NOT EXISTS `building` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `BNo` varchar(1000) NOT NULL COMMENT '楼栋号/名',
  `AreaID` int(11) NOT NULL COMMENT '所属楼盘编号',
  `PMCU` double NOT NULL COMMENT '物业费单价',
  `PRSF` double NOT NULL COMMENT '公摊费',
  `TF` double NOT NULL COMMENT '垃圾清运费',
  PRIMARY KEY (`ID`),
  KEY `AreaID` (`AreaID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='楼栋';

-- --------------------------------------------------------

--
-- 表的结构 `car`
--

DROP TABLE IF EXISTS `car`;
CREATE TABLE IF NOT EXISTS `car` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `AreaID` int(11) NOT NULL COMMENT '所属楼盘编号',
  `CarCode` varchar(20) NOT NULL COMMENT '车牌号',
  `Name` varchar(1000) DEFAULT NULL COMMENT '车主姓名',
  `Tel` varchar(30) DEFAULT NULL COMMENT '车主电话号码',
  PRIMARY KEY (`ID`),
  KEY `AreaID` (`AreaID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='业/户主、商铺主、外来车辆';

-- --------------------------------------------------------

--
-- 表的结构 `carpayment`
--

DROP TABLE IF EXISTS `carpayment`;
CREATE TABLE IF NOT EXISTS `carpayment` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `CarID` int(11) NOT NULL COMMENT '车辆ID',
  `Fee` double NOT NULL COMMENT '实收车费',
  `TimeStamp` date NOT NULL COMMENT '缴费时间',
  PRIMARY KEY (`ID`),
  KEY `CarID` (`CarID`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='车辆缴费';

-- --------------------------------------------------------

--
-- 表的结构 `carpaymentmonth`
--

DROP TABLE IF EXISTS `carpaymentmonth`;
CREATE TABLE IF NOT EXISTS `carpaymentmonth` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `PaymentID` int(11) NOT NULL COMMENT '缴费记录ID',
  `Year` int(11) NOT NULL COMMENT '年份',
  `Month` int(11) NOT NULL COMMENT '月份',
  PRIMARY KEY (`ID`),
  KEY `PaymentID` (`PaymentID`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='车辆缴费月份';

-- --------------------------------------------------------

--
-- 表的结构 `household`
--

DROP TABLE IF EXISTS `household`;
CREATE TABLE IF NOT EXISTS `household` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `BuildingID` int(11) NOT NULL COMMENT '所属楼栋编号',
  `RoomCode` varchar(500) NOT NULL COMMENT '门牌号/名',
  `Name` varchar(1000) NOT NULL COMMENT '业/户主姓名',
  `Tel` varchar(30) NOT NULL COMMENT '业/户主电话号码',
  `Square` double NOT NULL COMMENT '住房面积',
  PRIMARY KEY (`ID`),
  KEY `BuildingID` (`BuildingID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COMMENT='住户信息';

-- --------------------------------------------------------

--
-- 表的结构 `householdpayment`
--

DROP TABLE IF EXISTS `householdpayment`;
CREATE TABLE IF NOT EXISTS `householdpayment` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `HouseHoldID` int(11) NOT NULL COMMENT '业/户主ID',
  `Name` varchar(1000) NOT NULL COMMENT '业/户主姓名',
  `Tel` varchar(30) NOT NULL COMMENT '业/户主电话号码',
  `PMC` double NOT NULL COMMENT '实收物业费',
  `PRSF` double NOT NULL COMMENT '实收公摊费',
  `TF` double NOT NULL COMMENT '实收垃圾清运费',
  `CarPaymentID` int(11) DEFAULT NULL COMMENT '车辆缴费ID',
  `TimeStamp` date NOT NULL COMMENT '缴费时间',
  PRIMARY KEY (`ID`),
  KEY `HouseHoldID` (`HouseHoldID`),
  KEY `CarPaymentID` (`CarPaymentID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='住户缴费';

-- --------------------------------------------------------

--
-- 表的结构 `householdpaymentmonth`
--

DROP TABLE IF EXISTS `householdpaymentmonth`;
CREATE TABLE IF NOT EXISTS `householdpaymentmonth` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `PaymentID` int(11) NOT NULL COMMENT '缴费记录ID',
  `Year` int(11) NOT NULL COMMENT '年份',
  `Month` int(11) NOT NULL COMMENT '月份',
  PRIMARY KEY (`ID`),
  KEY `PaymentID` (`PaymentID`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='住户缴费月份';

-- --------------------------------------------------------

--
-- 表的结构 `operationlog`
--

DROP TABLE IF EXISTS `operationlog`;
CREATE TABLE IF NOT EXISTS `operationlog` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '时间戳',
  `IP` varchar(1000) NOT NULL COMMENT '远程地址',
  `OperatorID` int(11) NOT NULL COMMENT '操作人员ID',
  `OperaMod` varchar(1000) NOT NULL COMMENT '操作模块/单元',
  `Action` varchar(1000) NOT NULL COMMENT '行为',
  `RefTbl` varchar(200) NOT NULL COMMENT '涉及表名',
  `RefID` int(11) NOT NULL COMMENT '涉及表的行ID',
  PRIMARY KEY (`ID`),
  KEY `OperatorID` (`OperatorID`)
) ENGINE=InnoDB AUTO_INCREMENT=1955 DEFAULT CHARSET=utf8 COMMENT='操作日志';

-- --------------------------------------------------------

--
-- 表的结构 `sa`
--

DROP TABLE IF EXISTS `sa`;
CREATE TABLE IF NOT EXISTS `sa` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `UserID` int(11) NOT NULL COMMENT '用户ID',
  `AreaID` int(11) NOT NULL COMMENT '管辖范围ID',
  PRIMARY KEY (`ID`),
  KEY `AreaID` (`AreaID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='员工（用户）管辖范围';

--
-- 转存表中的数据 `sa`
--

INSERT INTO `sa` (`ID`, `UserID`, `AreaID`) VALUES
(16, 1, 1),
(17, 1, 2),
(18, 3, 1);

-- --------------------------------------------------------

--
-- 表的结构 `shop`
--

DROP TABLE IF EXISTS `shop`;
CREATE TABLE IF NOT EXISTS `shop` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `AreaID` int(11) NOT NULL COMMENT '所属楼盘编号',
  `ShopName` varchar(1000) NOT NULL COMMENT '商铺名称',
  `Name` varchar(1000) NOT NULL COMMENT '店主姓名',
  `Tel` varchar(30) NOT NULL COMMENT '店主电话号码',
  `PMCU` double NOT NULL COMMENT '物业费单价',
  `ELU` double NOT NULL COMMENT '电费单价',
  `TF` double NOT NULL COMMENT '垃圾清运费',
  PRIMARY KEY (`ID`),
  KEY `AreaID` (`AreaID`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='商铺';

-- --------------------------------------------------------

--
-- 表的结构 `shoppayment`
--

DROP TABLE IF EXISTS `shoppayment`;
CREATE TABLE IF NOT EXISTS `shoppayment` (
  `ID` int(11) NOT NULL COMMENT '自增ID',
  `ShopID` int(11) NOT NULL COMMENT '商铺ID',
  `ShopName` varchar(1000) NOT NULL COMMENT '商铺名称',
  `Name` varchar(1000) NOT NULL COMMENT '店主姓名',
  `Tel` varchar(30) NOT NULL COMMENT '店主电话号码',
  `PMC` double NOT NULL COMMENT '实收物业费',
  `ELE` double NOT NULL COMMENT '实收电费',
  `TF` double NOT NULL COMMENT '实收垃圾清运费',
  `CarPaymentID` int(11) DEFAULT NULL COMMENT '车辆缴费ID',
  `TimeStamp` date NOT NULL COMMENT '缴费时间',
  PRIMARY KEY (`ID`),
  KEY `ShopID` (`ShopID`),
  KEY `CarPaymentID` (`CarPaymentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商铺缴费';

-- --------------------------------------------------------

--
-- 表的结构 `shoppaymentmonth`
--

DROP TABLE IF EXISTS `shoppaymentmonth`;
CREATE TABLE IF NOT EXISTS `shoppaymentmonth` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `PaymentID` int(11) NOT NULL COMMENT '缴费记录ID',
  `Year` int(11) NOT NULL COMMENT '年份',
  `Month` int(11) NOT NULL COMMENT '月份',
  PRIMARY KEY (`ID`),
  KEY `PaymentID` (`PaymentID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='商铺缴费月份';

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `UID` varchar(30) NOT NULL COMMENT '身份证号',
  `TEL` varchar(30) NOT NULL COMMENT '电话号码',
  `UserName` varchar(1000) NOT NULL COMMENT '姓名',
  `_Password` varchar(100) NOT NULL COMMENT '加密后的密码',
  `SecStr` varchar(100) NOT NULL COMMENT '加密后的安全字符串',
  `Type` int(11) NOT NULL COMMENT '用户账号类型',
  `Online` int(11) NOT NULL COMMENT '账号在线状态',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UID` (`UID`),
  UNIQUE KEY `TEL` (`TEL`),
  KEY `Type` (`Type`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户';

--
-- 转存表中的数据 `user`
--

INSERT INTO `user` (`ID`, `UID`, `TEL`, `UserName`, `_Password`, `SecStr`, `Type`, `Online`) VALUES
(-1, '访客', '', '访客', '', '', -1, 0),
(1, '111111111111111111', '11111111111', 'InJeCTrL', '*E56A114692FE0DE073F9A1DD68A00EEB9703F3F1', '*E56A114692FE0DE073F9A1DD68A00EEB9703F3F1', 0, 0),
(3, '111111111111111112', '11111111112', 'InJe12345test', '*E56A114692FE0DE073F9A1DD68A00EEB9703F3F1', '*E56A114692FE0DE073F9A1DD68A00EEB9703F3F1', 1, 0);

-- --------------------------------------------------------

--
-- 表的结构 `usertype`
--

DROP TABLE IF EXISTS `usertype`;
CREATE TABLE IF NOT EXISTS `usertype` (
  `ID` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `Type` int(11) NOT NULL COMMENT '员工账号类型',
  `TypeName` varchar(1000) NOT NULL COMMENT '类型名称',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Type` (`Type`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='用户账号类型';

--
-- 转存表中的数据 `usertype`
--

INSERT INTO `usertype` (`ID`, `Type`, `TypeName`) VALUES
(1, 0, '超级管理员'),
(2, 1, '管理员'),
(3, 2, '观察者'),
(4, 3, '被禁用的管理员'),
(5, 4, '被禁用的观察者'),
(6, 5, '未审核'),
(13, -1, '访客');

--
-- 限制导出的表
--

--
-- 限制表 `building`
--
ALTER TABLE `building`
  ADD CONSTRAINT `PK_AreaID_building_ID_area` FOREIGN KEY (`AreaID`) REFERENCES `area` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `car`
--
ALTER TABLE `car`
  ADD CONSTRAINT `PK_AreaID_car_ID_area` FOREIGN KEY (`AreaID`) REFERENCES `area` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `carpayment`
--
ALTER TABLE `carpayment`
  ADD CONSTRAINT `PK_CarID_carpayment_ID_car` FOREIGN KEY (`CarID`) REFERENCES `car` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `carpaymentmonth`
--
ALTER TABLE `carpaymentmonth`
  ADD CONSTRAINT `PK_PaymentID_carpaymentmonth_ID_carpayment` FOREIGN KEY (`PaymentID`) REFERENCES `carpayment` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `household`
--
ALTER TABLE `household`
  ADD CONSTRAINT `PK_BuildingID_household_ID_building` FOREIGN KEY (`BuildingID`) REFERENCES `building` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `householdpayment`
--
ALTER TABLE `householdpayment`
  ADD CONSTRAINT `PK_CarPaymentID_householdpayment_ID_carpayment` FOREIGN KEY (`CarPaymentID`) REFERENCES `carpayment` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `PK_HouseHoldID_householdpayment_ID_household` FOREIGN KEY (`HouseHoldID`) REFERENCES `household` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `householdpaymentmonth`
--
ALTER TABLE `householdpaymentmonth`
  ADD CONSTRAINT `PK_PaymentID_householdpaymentmonth_ID_householdpayment` FOREIGN KEY (`PaymentID`) REFERENCES `householdpayment` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `operationlog`
--
ALTER TABLE `operationlog`
  ADD CONSTRAINT `PK_OperatorID_operationlog_ID_user` FOREIGN KEY (`OperatorID`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `sa`
--
ALTER TABLE `sa`
  ADD CONSTRAINT `PK_AreaID_sa_ID_area` FOREIGN KEY (`AreaID`) REFERENCES `area` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `PK_UserID_sa_ID_user` FOREIGN KEY (`UserID`) REFERENCES `user` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `shop`
--
ALTER TABLE `shop`
  ADD CONSTRAINT `PK_AreaID_shop_ID_area` FOREIGN KEY (`AreaID`) REFERENCES `area` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `shoppayment`
--
ALTER TABLE `shoppayment`
  ADD CONSTRAINT `PK_CarPaymentID_shoppayment_ID_carpayment` FOREIGN KEY (`CarPaymentID`) REFERENCES `carpayment` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `PK_ShopID_shoppayment_ID_shop` FOREIGN KEY (`ShopID`) REFERENCES `shop` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `shoppaymentmonth`
--
ALTER TABLE `shoppaymentmonth`
  ADD CONSTRAINT `PK_PaymentID_shoppaymentmonth_ID_shoppayment` FOREIGN KEY (`PaymentID`) REFERENCES `shoppayment` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- 限制表 `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `PK_Type_user_Type_usertype` FOREIGN KEY (`Type`) REFERENCES `usertype` (`Type`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
