using System;
using System.Collections.Generic;
using System.Data;
using System.Data.OracleClient;
using System.IO;
using System.Linq;
using System.Text;
using System.Threading.Tasks;


namespace AccountMgr
{
    /// <summary> 数据库管理（数据库登录、数据增删改查等）
    /// </summary>
    public static class DBMgr
    {
        /// <summary> 用户名
        /// </summary>
        private static String UserCode_SYS;
        /// <summary> 用户密码
        /// </summary>
        private static String Password_SYS;
        /// <summary> 用户类型 0:未登入 1:操作员 2:管理员
        /// </summary>
        private static int UserType = 0;
        /// <summary> 数据库服务器地址
        /// </summary>
        private static String HostIP;
        /// <summary> 数据库端口号
        /// </summary>
        private static String Port;

        //程序内数据处理
        /// <summary> 获取数据库连接字符串
        /// </summary>
        /// <returns>返回数据库连接字符串</returns>
        private static String GetDBConnectStr()
        {
            return "User ID=AccountManager_DB;" +           //操作员用户名
                   "Password=AccountMgrPWD123;" +           //操作员密码
                   "Data Source=(DESCRIPTION = (ADDRESS_LIST= (ADDRESS = " +
                   "(PROTOCOL = TCP)(HOST = " + HostIP + ")" +  //数据库服务器地址
                   "(PORT = " + Port + "))) " +                 //端口
                   "(CONNECT_DATA = (SERVICE_NAME = xe)))";     //数据库SIDXE
        }
        /// <summary> 加载保存的服务器地址、端口号
        /// </summary>
        /// <returns>加载成功返回真并设置HostIP与Port，否则返回假</returns>
        public static Boolean Initialize()
        {
            //根目录存在配置文件
            if (File.Exists(Environment.CurrentDirectory + "/Config.inf"))
            {
                String[] Config_Lines = File.ReadAllLines(Environment.CurrentDirectory + "/Config.inf");//读取配置文件中所有行

                //配置文件有两行，认为是已经初始化
                if (Config_Lines.Length == 2)
                {
                    HostIP = Config_Lines[0];
                    Port = Config_Lines[1];
                    return true;
                }
                //配置文件不等于两行
                else
                {
                    return false;
                }
            }
            //无配置文件直接认为未初始化
            else
            {
                return false;
            }
        }

        /*
        /// <summary> 初始化管理系统的数据库各项表格、存储过程等
        /// </summary>
        /// <returns>初始化成功返回真，否则返回假</returns>
        public static Boolean InitializeDatabase()
        {
            String Connect_Str = GetADConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类
            String[] CommandLines = { @"create or replace package
                                        AM_PK_RefCur as
                                        type p_cursor is ref cursor;
                                        end AM_PK_RefCur;",                     //创建引用游标
                                      @"create table AM_SYSUser_Type
                                        (
                                            UserCode VARCHAR(20) PRIMARY KEY,
                                            IsOP INT NOT NULL
                                        );",                                    //创建管理系统用户类型表
                                      @"insert into AM_SYSUser_Type
                                        values ('" + UserCode_OP + "',1);",     //向管理系统用户类型表插入默认管理员
                                      @"create table AM_Static_Fee
                                        (
                                            ItemID INT PRIMARY KEY,
                                            Fee DOUBLE NOT NULL
                                        );",                                    //创建固定费用表
                                      @"insert into AM_Static_Fee
                                        values (0,0);",                         //向固定费用表插入垃圾转运费条目
                                      @"insert into AM_Static_Fee
                                        values (1,0);",                         //向固定费用表插入店面电费单价条目
                                      @"insert into AM_Static_Fee
                                        values (2,0);",                         //向固定费用表插入店面物业费单价条目
                                      @"create table AM_User1_Info
                                        (
                                            BN INT NOT NULL,
                                            RN INT NOT NULL,
                                            Area DOUBLE,
                                            Tel VARCHAR(20),
                                            Name VARCHAR(20),
                                            PRIMARY KEY ('BN', 'RN')
                                        )",                                     //创建户主信息表
                                      @"create table AM_User1Car_Info
                                        (
                                            CarID VARCHAR(20) PRIMARY KEY,
                                            BN INT NOT NULL,
                                            RN INT NOT NULL
                                        )",                                     //创建户主车辆信息表
                                      @"create table AM_User1UFee_Info
                                        (
                                            BN INT PRIMARY KEY,
                                            UPC DOUBLE NOT NULL,
                                            PUBC DOUBLE NOT NULL
                                        )",                                     //创建楼栋单价表
                                      @"create table AM_User1Fee_Flag
                                        (
                                            BN INT NOT NULL,
                                            RN INT NOT NULL,
                                            Year INT NOT NULL,
                                            Month INT NOT NULL,
                                            PCFlag INT NOT NULL,
                                            PUBCFlag INT NOT NULL,
                                            GTFeeFlag INT NOT NULL,
                                            CarFeeFlag INT NOT NULL,
                                            NeedCarF INT NOT NULL,
                                            PRIMARY KEY ('BN', 'RN', 'Year', 'Month')
                                        )",                                     //创建户主对应月份标志表
                                      @"create table AM_User1PC_Fee
                                        (
                                            BN INT NOT NULL,
                                            RN INT NOT NULL,
                                            Date DATE NOT NULL,
                                            Fee DOUBLE NOT NULL,
                                            Bonus DOUBLE NOT NULL,
                                            PRIMARY KEY ('BN', 'RN', 'Date')
                                        )",                                     //创建户主物业收费表
                                      @"create table AM_User1PUBC_Fee
                                        (
                                            BN INT NOT NULL,
                                            RN INT NOT NULL,
                                            Date DATE NOT NULL,
                                            Fee DOUBLE NOT NULL,
                                            PRIMARY KEY ('BN', 'RN', 'Date')
                                        )",                                     //创建户主公摊收费表
                                      @"create table AM_User1GT_Fee
                                        (
                                            BN INT NOT NULL,
                                            RN INT NOT NULL,
                                            Date DATE NOT NULL,
                                            Fee DOUBLE NOT NULL,
                                            PRIMARY KEY ('BN', 'RN', 'Date')
                                        )",                                     //创建户主垃圾转运收费表
                                      @"create table AM_User2_Info
                                        (
                                            SN INT PRIMARY KEY,
                                            Area DOUBLE,
                                            Tel VARCHAR(20),
                                            SName VARCHAR(20),
                                            Name VARCHAR(20),
                                        )",                                     //创建店面信息表
                                      @"create table AM_User2Fee_Flag
                                        (
                                            SN INT NOT NULL,
                                            Year INT NOT NULL,
                                            Month INT NOT NULL,
                                            PCFlag INT NOT NULL,
                                            ELEFeeFlag INT NOT NULL,
                                            GTFeeFlag INT NOT NULL,
                                            NeedELEF INT NOT NULL,
                                            PRIMARY KEY ('SN', 'Year', 'Month')
                                        )",                                     //创建店面对应月份标志表
                                      @"create table AM_User2PC_Fee
                                        (
                                            SN INT NOT NULL,
                                            Date DATE NOT NULL,
                                            Fee DOUBLE NOT NULL,
                                            PRIMARY KEY ('SN', 'Date')
                                        )",                                     //创建店面物业收费表
                                      @"create table AM_User2ELE_Fee
                                        (
                                            SN INT NOT NULL,
                                            Date DATE NOT NULL,
                                            Fee DOUBLE NOT NULL,
                                            PRIMARY KEY ('SN', 'Date')
                                        )",                                     //创建店面电费收费表
                                      @"create table AM_User2GT_Fee
                                        (
                                            SN INT NOT NULL,
                                            Date DATE NOT NULL,
                                            Fee DOUBLE NOT NULL,
                                            PRIMARY KEY ('SN', 'Date')
                                        )",                                     //创建店面垃圾转运费收费表
                                      @"create table AM_User3Car_Info
                                        (
                                            CarID VARCHAR(20) PRIMARY KEY,
                                            Name VARCHAR(20),
                                            TEL VARCHAR(20)
                                        )",                                     //创建外租车主信息表
                                      @"create table AM_CarFee_Flag
                                        (
                                            CarID VARCHAR(20) NOT NULL,
                                            Year INT NOT NULL,
                                            Month INT NOT NULL,
                                            CarFee_Flag INT NOT NULL,
                                            PRIMARY KEY ('CarID', 'Year', 'Month')
                                        )",                                     //创建车费对应月份标志表
                                      @"create table AM_Car_Fee
                                        (
                                            CarID VARCHAR(20) NOT NULL,
                                            Date DATE NOT NULL,
                                            Fee DOUBLE NOT NULL,
                                            PRIMARY KEY ('CarID', 'Date')
                                        )",                                     //创建车费收费表
                                      @"create or replace PROCEDURE PROC_GETSYSUSER 
                                        (
                                          V_USERCODE IN VARCHAR 
                                        , P_CUR OUT AM_PK_RefCur.P_CURSOR
                                        ) AS 
                                        BEGIN
                                          open P_CUR for select * from Am_Sysuser_Type
                                        where Usercode like '%' || V_USERCODE || '%';
                                        END;",                                  //创建获取管理系统用户存储过程
                                      @"create or replace PROCEDURE proc_AddSYSUser 
                                        (
                                          V_UserCode IN VARCHAR 
                                        , V_OP IN INT
                                        ) IS
                                        BEGIN
                                            Insert into AM_SYSUser_Type
                                            values(V_UserCode, OP);
                                        END;",                                  //创建新增管理系统用户存储过程
                                      @"create or replace PROCEDURE proc_DelSYSUser 
                                        (
                                          V_UserCode IN VARCHAR
                                        ) IS
                                        BEGIN
                                            Delete from AM_SYSUser_Type
                                            where UserCode=V_UserCode;
                                        END;",                                  //创建删除管理系统用户存储过程
                                      @"create or replace PROCEDURE proc_AlterSYSUser 
                                        (
                                          V_UserCode IN VARCHAR
                                        , V_OP IN INT
                                        ) IS
                                        BEGIN
                                            Update AM_SYSUser_Type
                                            set IsOP=V_OP
                                            where UserCode=V_UserCode;
                                        END;",                                  //创建设置管理系统用户存储过程
                                      @"create or replace PROCEDURE proc_SetStaticFee 
                                        (
                                          V_Fee IN NUMBER
                                          V_ItemID IN INT
                                        ) IS
                                        BEGIN
                                            Update AM_Static_Fee
                                            set Fee=V_Fee
                                            where ItemID=V_ItemID;
                                        END;",                                  //创建设置静态费用数据存储过程
                                      @"create or replace PROCEDURE proc_GetStaticFee 
                                        (
                                          V_Fee OUT NUMBER
                                          V_ItemID IN INT
                                        ) IS
                                        BEGIN
                                            select Fee into V_Fee
                                            from AM_Static_Fee
                                            where ItemID=V_ItemID;
                                        END;",                                  //创建获取静态费用数据存储过程
                                      @"create or replace PROCEDURE Proc_GetUser1Info
                                        (
                                          V_BN IN INT
                                        , V_RN IN INT
                                        , V_Tel IN VARCHAR
                                        , V_Name IN VARCHAR
                                        , V_Type IN INT
                                        , P_CUR OUT AM_PK_RefCur.P_CURSOR
                                        ) IS
                                        BEGIN
                                            if V_Type=0 then
                                            open P_CUR for select * from AM_User1_Info
                                            where BN=V_BN;
                                            elsif V_Type=1 then
                                            open P_CUR for select * from AM_User1_Info
                                            where BN=V_BN and RN=V_RN;
                                            elsif V_Type=2 then
                                            open P_CUR for select * from Am_User1_Info
                                            where Tel like '%'||V_Tel||'%';
                                            elsif V_Type=3 then
                                            open P_CUR for select * from Am_User1_Info
                                            where Name like '%'||V_Name||'%';
                                            else
                                            open P_CUR for select * from Am_User1_Info;
                                            end if;
                                        END;",                                  //创建获取户主信息存储过程
                                      };//创建游标、表、存储过程等SQL语句

            try
            {
                Connect.Open();//尝试连接数据库

                //创建各项
                foreach (String p in CommandLines)
                {
                    OracleCommand RunSQL = new OracleCommand(p);
                    RunSQL.Connection = Connect;//指定连接
                    RunSQL.ExecuteNonQuery();//执行新建
                }


                //创建初始化标志文件
                String[] Config_Line = { "Initialized" };//配置行
                File.WriteAllLines(Environment.CurrentDirectory + "/Config.inf", Config_Line, Encoding.UTF8);//向配置文件写入配置行

                return true;//创建成功
            }
            catch (Exception)
            {
                return false;//创建失败则返回false
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 摧毁管理系统的数据库各项表格、存储过程等
        /// </summary>
        /// <returns>摧毁成功则返回真，否则返回假</returns>
        public static Boolean DestroyDatabase()
        {
            String Connect_Str = GetADConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类
            String[] CommandLines = { @"drop package AM_PK_RefCur;",            //删除引用游标
                                      @"drop table AM_Static_Fee purge",        //删除固定费用数据表
                                      @"drop table AM_SYSUser_Type purge",      //删除用户类型表
                                      @"drop table AM_User1_Info purge",        //删除户主信息表
                                      @"drop table AM_User1Car_Info purge",     //删除户主车辆表
                                      @"drop table AM_User1UFee_Info purge",    //删除楼栋单价表
                                      @"drop table AM_User1Fee_Flag purge",     //删除户主对应月份标志表
                                      @"drop table AM_User1PC_Fee purge",       //删除户主物业收费表
                                      @"drop table AM_User1PUBC_Fee purge",     //删除户主公摊收费表
                                      @"drop table AM_User1GT_Fee purge",       //删除户主垃圾转运收费表
                                      @"drop table AM_User2_Info purge",        //删除店面信息表
                                      @"drop table AM_User2Fee_Flag purge",     //删除店对应月份标志表
                                      @"drop table AM_User2PC_Fee purge",       //删除店面物业收费表
                                      @"drop table AM_User2ELE_Fee purge",      //删除店面电费收费表
                                      @"drop table AM_User2GT_Fee purge",       //删除店面垃圾转运费收费表
                                      @"drop table AM_User3Car_Info purge",     //删除外租车主信息
                                      @"drop table AM_CarFee_Flag purge",       //删除车费对应月份标志表
                                      @"drop table AM_Car_Fee purge"};          //删除车费收费表

            try
            {
                Connect.Open();//尝试连接数据库
                foreach (String p in CommandLines)
                {
                    OracleCommand Del = new OracleCommand(p);
                    Del.Connection = Connect;//指定连接
                    Del.ExecuteNonQuery();//执行删除
                }
                return true;//删除成功
            }
            catch (Exception)
            {
                return false;//失败则返回false
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        */
        //管理系统用户处理
        /// <summary> 新增操作员或管理员
        /// </summary>
        /// <param name="UserCode">登入用户名</param>
        /// <param name="Password">登入密码</param>
        /// <param name="UserType">用户类型</param>
        /// <param name="MaxConnect">最大连接用户数</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser(String UserCode, String Password, int UserType, int MaxConnect)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                //插入管理系统用户表
                OracleParameter[] Parm = new OracleParameter[4];//实例化参数列表
                Parm[0] = new OracleParameter("v_UserCode", OracleType.VarChar);//UserCode参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = UserCode;
                Parm[1] = new OracleParameter("v_Password", OracleType.VarChar);//Password参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Password;
                Parm[2] = new OracleParameter("v_UserType", OracleType.Int16);//UserType参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Convert.ToInt16(UserType);
                Parm[3] = new OracleParameter("v_MaxConnect", OracleType.Int16);//MaxConnect参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Convert.ToInt16(MaxConnect);

                OracleCommand AddSYSUser = new OracleCommand("proc_AddSYSUser", Connect);//指定存储过程
                AddSYSUser.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddSYSUser.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddSYSUser.Parameters.Add(tP);
                }
                //调用存储过程
                if (AddSYSUser.ExecuteNonQuery() == 0)
                    return false;//插入失败
                else
                    return true;//插入成功
            }
            catch (Exception)
            {
                return false;//添加用户失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除操作员或管理员
        /// </summary>
        /// <param name="UserCode">登入用户名</param>
        /// <returns>删除成功则返回真，否则返回假</returns>
        public static Boolean DeleteUser(String UserCode)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库
                
                //删除管理系统用户表中的行
                OracleParameter[] Parm = new OracleParameter[1];//实例化参数列表
                Parm[0] = new OracleParameter("v_UserCode", OracleType.VarChar);//UserCode参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = UserCode;

                OracleCommand DelSYSUser = new OracleCommand("proc_DelSYSUser", Connect);//指定存储过程
                DelSYSUser.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelSYSUser.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelSYSUser.Parameters.Add(tP);
                }
                //调用存储过程
                if (DelSYSUser.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除用户失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 设置操作员或管理员信息
        /// </summary>
        /// <param name="UserCode">登入名</param>
        /// <param name="Password">登入密码</param>
        /// <param name="UserType">是否是操作员</param>
        /// <param name="MaxConnect">最大连接用户数</param>
        /// <param name="numConnect">当前连接用户数</param>
        /// <returns>设置成功则返回真，否则返回假</returns>
        public static Boolean SetUser(String UserCode, String Password, int UserType, int MaxConnect, int numConnect)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库
                
                //设置管理系统用户表
                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_UserCode", OracleType.VarChar);//UserCode参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = UserCode;
                Parm[1] = new OracleParameter("v_Password", OracleType.Int16);//Password参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Convert.ToInt16(Password);
                Parm[2] = new OracleParameter("v_UserType", OracleType.Int16);//UserType参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Convert.ToInt16(UserType);
                Parm[3] = new OracleParameter("v_MaxConnect", OracleType.Int16);//MaxConnect参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Convert.ToInt16(MaxConnect);
                Parm[4] = new OracleParameter("v_numConnect", OracleType.Int16);//numConnect参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Convert.ToInt16(numConnect);

                OracleCommand AlterSYSUser = new OracleCommand("proc_AlterSYSUser", Connect);//指定存储过程
                AlterSYSUser.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AlterSYSUser.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AlterSYSUser.Parameters.Add(tP);
                }
                //调用存储过程
                if (AlterSYSUser.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置用户失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 查询操作员或管理员信息
        /// </summary>
        /// <param name="UserCode">登入用户名</param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetUser(String UserCode, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> UserList = new List<String[]>();//待返回的操作员、管理员信息列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[3];//实例化参数列表
                Parm[0] = new OracleParameter("v_UserCode", OracleType.VarChar);//UserCode参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = UserCode;
                Parm[1] = new OracleParameter("v_LikeQuery", OracleType.Int16);//LikeQuery参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Convert.ToInt16(LikeQuery);
                Parm[2] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[2].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand QueryUser = new OracleCommand("proc_GetSYSUser", Connect);//指定存储过程
                QueryUser.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                QueryUser.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    QueryUser.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(QueryUser);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    UserList.Add(new String[] {datatable.Rows[i][0].ToString(),//登入名
                                                datatable.Rows[i][1].ToString(),//密码
                                                datatable.Rows[i][2].ToString(),//用户类型
                                                datatable.Rows[i][3].ToString(),//最大连接数
                                                datatable.Rows[i][4].ToString()});//当前连接数
                }
                return UserList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询用户类型表失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //单独数据处理
        /// <summary> 设置静态费用
        /// </summary>
        /// <param name="Fee">垃圾转运费</param>
        /// <param name="FeeType">费用类型 0：垃圾转运费 1:店面电费单价 2:店面物业费单价</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetStaticFee(Double Fee, int FeeType)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("v_Fee", OracleType.Number);//费用
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = Fee;
                Parm[1] = new OracleParameter("v_FeeType", OracleType.Int16);//费用类型
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = FeeType;

                OracleCommand StaticFee = new OracleCommand("proc_SetStaticFee", Connect);//指定存储过程
                StaticFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                StaticFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    StaticFee.Parameters.Add(tP);
                }
                if (StaticFee.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置静态费用成功则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 获取静态费用
        /// </summary>
        /// <param name="FeeType">费用类型 0：垃圾转运费 1:店面电费单价 2:店面物业费单价</param>
        /// <returns>获取成功返回非负数，否则返回-1</returns>
        public static Double GetStaticFee(int FeeType)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("v_Fee", OracleType.Number);//静态费用
                Parm[0].Direction = ParameterDirection.Output;//输出
                Parm[1] = new OracleParameter("v_FeeType", OracleType.Int16);//静态费用类型
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = FeeType;

                OracleCommand StaticFee = new OracleCommand("proc_GetStaticFee", Connect);//指定存储过程
                StaticFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                StaticFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    StaticFee.Parameters.Add(tP);
                }
                StaticFee.ExecuteNonQuery();//调用存储过程
                return Convert.ToDouble(Parm[0].Value);
            }
            catch (Exception)
            {
                return -1;//获取静态费用失败则返回-1
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //户主信息处理
        /// <summary> 获取户主信息，根据查询类型对相应参数填null
        /// </summary>
        /// <param name="SearchType">查询类型 0：根据栋号 1：根据栋号户号 2：根据电话 3：根据姓名 4：获取所有行</param>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Tel">电话号码</param>
        /// <param name="Name">姓名</param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>表中符合条件的行</returns>
        public static List<String[]> GetUser1Info(int SearchType, int BN, int RN, String Tel, String Name, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User1List = new List<String[]>();//待返回的户主列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[7];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Tel", OracleType.VarChar);//Tel参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Tel;
                Parm[3] = new OracleParameter("v_Name", OracleType.VarChar);//Name参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Name;
                Parm[4] = new OracleParameter("v_Type", OracleType.Int32);//Type参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = SearchType;
                Parm[5] = new OracleParameter("v_LikeQuery", OracleType.Int32);//模糊查找参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Convert.ToInt16(LikeQuery);
                Parm[6] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[6].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand QueryUser1 = new OracleCommand("proc_GetUser1Info", Connect);//指定存储过程
                QueryUser1.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                QueryUser1.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    QueryUser1.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(QueryUser1);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User1List.Add(new String[] {datatable.Rows[i][0].ToString(),//栋号
                                                datatable.Rows[i][1].ToString(),//户号
                                                datatable.Rows[i][2].ToString(),//面积
                                                datatable.Rows[i][3].ToString(),//电话
                                                datatable.Rows[i][4].ToString()});//姓名
                }
                return User1List;//查询成功
            }
            catch (Exception)
            {
                return null;//查询户主信息表失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 根据栋号户号的组合设置户主信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Area">面积</param>
        /// <param name="Tel">电话号码</param>
        /// <param name="Name">姓名</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1Info(int BN, int RN, Double Area, String Tel, String Name)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Area", OracleType.Double);//Area参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Area;
                Parm[3] = new OracleParameter("v_Tel", OracleType.VarChar);//Tel参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Tel;
                Parm[4] = new OracleParameter("v_Name", OracleType.VarChar);//Name参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Name;

                OracleCommand SetUser1 = new OracleCommand("proc_SetUser1Info", Connect);//指定存储过程
                SetUser1.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser1.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser1.Parameters.Add(tP);
                }
                if (SetUser1.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置户主信息表失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 新增户主信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Area">面积</param>
        /// <param name="Tel">电话号码</param>
        /// <param name="Name">姓名</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser1Info(int BN, int RN, Double Area, String Tel, String Name)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Area", OracleType.Double);//Area参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Area;
                Parm[3] = new OracleParameter("v_Tel", OracleType.VarChar);//Tel参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Tel;
                Parm[4] = new OracleParameter("v_Name", OracleType.VarChar);//Name参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Name;

                OracleCommand AddUser1 = new OracleCommand("proc_AddUser1Info", Connect);//指定存储过程
                AddUser1.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser1.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser1.Parameters.Add(tP);
                }
                if (AddUser1.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增户主信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除户主信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser1Info(int BN, int RN)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                //删除户主
                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int16);//栋号参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int16);//户号参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;

                OracleCommand DelUser1 = new OracleCommand("proc_DelUser1", Connect);//指定存储过程
                DelUser1.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser1.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser1.Parameters.Add(tP);
                }
                //调用存储过程
                if (DelUser1.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除户主失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //户主对应月份缴费标志信息处理
        /// <summary> 新增某户某月缴费标志信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="PCFee_Flag">物业费标志</param>
        /// <param name="PUBCFee_Flag">公摊费标志</param>
        /// <param name="GTFee_Flag">垃圾转运费标志</param>
        /// <param name="CarFee_Flag">停车费标志</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser1FeeFlag(int BN, int RN, int Year, int Month, Boolean PCFee_Flag, Boolean PUBCFee_Flag, Boolean GTFee_Flag, Boolean CarFee_Flag)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[8];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_PCFee_Flag", OracleType.Int16);//PCFee_Flag参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Convert.ToInt16(PCFee_Flag);
                Parm[5] = new OracleParameter("v_PUBCFee_Flag", OracleType.Int16);//PUBCFee_Flag参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Convert.ToInt16(PUBCFee_Flag);
                Parm[6] = new OracleParameter("v_GTFee_Flag", OracleType.Int16);//GTFee_Flag参数
                Parm[6].Direction = ParameterDirection.Input;//输入
                Parm[6].Value = Convert.ToInt16(GTFee_Flag);
                Parm[7] = new OracleParameter("v_CarFee_Flag", OracleType.Int16);//CarFee_Flag参数
                Parm[7].Direction = ParameterDirection.Input;//输入
                Parm[7].Value = Convert.ToInt16(CarFee_Flag);

                OracleCommand AddUser1FeeFlag = new OracleCommand("proc_AddUser1FeeFlag", Connect);//指定存储过程
                AddUser1FeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser1FeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser1FeeFlag.Parameters.Add(tP);
                }
                if (AddUser1FeeFlag.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增户主缴费标志信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 按一定条件删除某户的缴费标志信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="DeleteType">删除规则：
        ///     0：按栋号删除整栋户主的所有交费信息
        ///     1：按栋号户号删除某户主的所有交费信息
        ///     2：按栋号户号年份删除某户主某年的所有交费信息
        ///     3：按栋号户号年分月份删除某户主某年某月的交费信息
        ///     4：按年份删除所有户主某年的所有交费信息
        /// </param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser1FeeFlag(int BN, int RN, int Year, int Month, int DeleteType)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_DeleteType", OracleType.Int16);//DeleteType参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = DeleteType;
                
                OracleCommand DelUser1FeeFlag = new OracleCommand("proc_DelUser1FeeFlag", Connect);//指定存储过程
                DelUser1FeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser1FeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser1FeeFlag.Parameters.Add(tP);
                }
                if (DelUser1FeeFlag.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除户主缴费标志信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 设置某户某月的交费标志信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="PCFee_Flag">物业费标志</param>
        /// <param name="PUBCFee_Flag">公摊费标志</param>
        /// <param name="GTFee_Flag">垃圾转运费标志</param>
        /// <param name="CarFee_Flag">停车费标志</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1FeeFlag(int BN, int RN, int Year, int Month, Boolean PCFee_Flag, Boolean PUBCFee_Flag, Boolean GTFee_Flag, Boolean CarFee_Flag)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[8];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_PCFee_Flag", OracleType.Int16);//PCFee_Flag参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Convert.ToInt16(PCFee_Flag);
                Parm[5] = new OracleParameter("v_PUBCFee_Flag", OracleType.Int16);//PUBCFee_Flag参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Convert.ToInt16(PUBCFee_Flag);
                Parm[6] = new OracleParameter("v_GTFee_Flag", OracleType.Int16);//GTFee_Flag参数
                Parm[6].Direction = ParameterDirection.Input;//输入
                Parm[6].Value = Convert.ToInt16(GTFee_Flag);
                Parm[7] = new OracleParameter("v_CarFee_Flag", OracleType.Int16);//CarFee_Flag参数
                Parm[7].Direction = ParameterDirection.Input;//输入
                Parm[7].Value = Convert.ToInt16(CarFee_Flag);

                OracleCommand SetUser1FeeFlag = new OracleCommand("proc_SetUser1FeeFlag", Connect);//指定存储过程
                SetUser1FeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser1FeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser1FeeFlag.Parameters.Add(tP);
                }
                if (SetUser1FeeFlag.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置户主缴费标志信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 按栋号户号年份月份查询户主的缴费标志信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>表中符合条件的行</returns>
        public static List<String[]> GetUser1FeeFlag(int BN, int RN, int Year, int Month, int LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User1FeeFlagList = new List<String[]>();//待返回的户主缴费标志列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[6];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_LikeQuery", OracleType.Int16);//LikeQuery参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = LikeQuery;
                Parm[5] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[5].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand GetUser1FeeFlag = new OracleCommand("proc_GetUser1FeeFlag", Connect);//指定存储过程
                GetUser1FeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetUser1FeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetUser1FeeFlag.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetUser1FeeFlag);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User1FeeFlagList.Add(new String[] {datatable.Rows[i][0].ToString(),//栋号
                                                        datatable.Rows[i][1].ToString(),//户号
                                                        datatable.Rows[i][2].ToString(),//年份
                                                        datatable.Rows[i][3].ToString(),//月份
                                                        datatable.Rows[i][4].ToString(),//物业费标志
                                                        datatable.Rows[i][5].ToString(),//公摊费标志
                                                        datatable.Rows[i][6].ToString(),//垃圾转运费标志
                                                        datatable.Rows[i][7].ToString()});//停车费标志
                }
                return User1FeeFlagList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询户主缴费标志信息失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //户主物业费收费细则处理
        /// <summary> 新增户主物业费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="PCFee">物业费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <param name="Bonus">优惠金额</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser1PCFee(int BN, int RN, Double PCFee, int Year, int Month, int Day, Double Bonus)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[7];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_PCFee", OracleType.Double);//PCFee参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = PCFee;
                Parm[3] = new OracleParameter("v_Year", OracleType.Int16);//Year参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Year;
                Parm[4] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Month;
                Parm[5] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Day;
                Parm[6] = new OracleParameter("v_Bonus", OracleType.Double);//Bonus参数
                Parm[6].Direction = ParameterDirection.Input;//输入
                Parm[6].Value = Bonus;

                OracleCommand AddUser1PCFee = new OracleCommand("proc_AddUser1PCFee", Connect);//指定存储过程
                AddUser1PCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser1PCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser1PCFee.Parameters.Add(tP);
                }
                if (AddUser1PCFee.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增户主物业费收费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除户主物业费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser1PCFee(int BN, int RN, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;

                OracleCommand DelUser1PCFee = new OracleCommand("proc_DelUser1PCFee", Connect);//指定存储过程
                DelUser1PCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser1PCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser1PCFee.Parameters.Add(tP);
                }
                if (DelUser1PCFee.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除户主物业费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 修改户主物业费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="PCFee">物业费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <param name="Bonus">优惠金额</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1PCFee(int BN, int RN, Double PCFee, int Year, int Month, int Day, Double Bonus)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[7];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_PCFee", OracleType.Double);//PCFee参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = PCFee;
                Parm[3] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Year;
                Parm[4] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Month;
                Parm[5] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Day;
                Parm[6] = new OracleParameter("v_Bonus", OracleType.Double);//Bonus参数
                Parm[6].Direction = ParameterDirection.Input;//输入
                Parm[6].Value = Bonus;
                
                OracleCommand SetUser1PCFee = new OracleCommand("proc_SetUser1PCFee", Connect);//指定存储过程
                SetUser1PCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser1PCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser1PCFee.Parameters.Add(tP);
                }
                if (SetUser1PCFee.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置户主物业费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 查询户主物业费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据栋号户号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetUser1PCFee(int BN, int RN, int Year, int Month, int Day, int SearchType, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User1PCFeeList = new List<String[]>();//待返回的户主物业费缴费细则列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[8];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int32);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;
                Parm[5] = new OracleParameter("v_SearchType", OracleType.Int32);//SearchType参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = SearchType;
                Parm[6] = new OracleParameter("v_LikeQuery", OracleType.Int16);//LikeQuery参数
                Parm[6].Direction = ParameterDirection.Input;//输入
                Parm[6].Value = Convert.ToInt16(LikeQuery);
                Parm[7] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[7].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand GetUser1PCFee = new OracleCommand("proc_GetUser1PCFee", Connect);//指定存储过程
                GetUser1PCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetUser1PCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetUser1PCFee.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetUser1PCFee);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User1PCFeeList.Add(new String[] {datatable.Rows[i][0].ToString(),//栋号
                                                        datatable.Rows[i][1].ToString(),//户号
                                                        datatable.Rows[i][2].ToString(),//物业费
                                                        datatable.Rows[i][3].ToString(),//缴费年份
                                                        datatable.Rows[i][4].ToString(),//缴费月份
                                                        datatable.Rows[i][5].ToString(),//缴费日期
                                                        datatable.Rows[i][6].ToString()});//优惠金额
                }
                return User1PCFeeList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询户主物业费缴费细则失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //户主公摊费收费细则处理
        /// <summary> 新增户主公摊费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="PUBCFee">公摊费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser1PUBCFee(int BN, int RN, Double PUBCFee, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[6];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_PUBCFee", OracleType.Double);//PUBCFee参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = PUBCFee;
                Parm[3] = new OracleParameter("v_Year", OracleType.Int16);//Year参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Year;
                Parm[4] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Month;
                Parm[5] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Day;

                OracleCommand AddUser1PUBCFee = new OracleCommand("proc_AddUser1PUBCFee", Connect);//指定存储过程
                AddUser1PUBCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser1PUBCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser1PUBCFee.Parameters.Add(tP);
                }
                if (AddUser1PUBCFee.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增户主公摊费收费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除户主公摊费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser1PUBCFee(int BN, int RN, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;

                OracleCommand DelUser1PUBCFee = new OracleCommand("proc_DelUser1PUBCFee", Connect);//指定存储过程
                DelUser1PUBCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser1PUBCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser1PUBCFee.Parameters.Add(tP);
                }
                if (DelUser1PUBCFee.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除户主公摊费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 修改户主公摊费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="PUBCFee">公摊费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1PUBCFee(int BN, int RN, Double PUBCFee, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[6];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_PUBCFee", OracleType.Double);//PUBCFee参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = PUBCFee;
                Parm[3] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Year;
                Parm[4] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Month;
                Parm[5] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Day;

                OracleCommand SetUser1PUBCFee = new OracleCommand("proc_SetUser1PUBCFee", Connect);//指定存储过程
                SetUser1PUBCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser1PUBCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser1PUBCFee.Parameters.Add(tP);
                }
                if (SetUser1PUBCFee.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置户主公摊费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 查询户主公摊费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据栋号户号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>符号条件的行</returns>
        public static List<String[]> GetUser1PUBCFee(int BN, int RN, int Year, int Month, int Day, int SearchType, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User1PCFeeList = new List<String[]>();//待返回的户主公摊费缴费细则列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[8];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int32);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;
                Parm[5] = new OracleParameter("v_SearchType", OracleType.Int32);//SearchType参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = SearchType;
                Parm[6] = new OracleParameter("v_LikeQuery", OracleType.Int16);//LikeQuery参数
                Parm[6].Direction = ParameterDirection.Input;//输入
                Parm[6].Value = Convert.ToInt16(LikeQuery);
                Parm[7] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[7].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand GetUser1PUBCFee = new OracleCommand("proc_GetUser1PUBCFee", Connect);//指定存储过程
                GetUser1PUBCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetUser1PUBCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetUser1PUBCFee.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetUser1PUBCFee);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User1PCFeeList.Add(new String[] {datatable.Rows[i][0].ToString(),//栋号
                                                        datatable.Rows[i][1].ToString(),//户号
                                                        datatable.Rows[i][2].ToString(),//公摊费
                                                        datatable.Rows[i][3].ToString(),//缴费年份
                                                        datatable.Rows[i][4].ToString(),//缴费月份
                                                        datatable.Rows[i][5].ToString()});//缴费日期
                }
                return User1PCFeeList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询户主公摊费缴费细则失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //户主垃圾转运费收费细则处理
        /// <summary> 新增户主垃圾转运费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="GTFee">垃圾转运费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser1GTFee(int BN, int RN, Double GTFee, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[6];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_GTFee", OracleType.Double);//GTFee参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = GTFee;
                Parm[3] = new OracleParameter("v_Year", OracleType.Int16);//Year参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Year;
                Parm[4] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Month;
                Parm[5] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Day;

                OracleCommand AddUser1GTFee = new OracleCommand("proc_AddUser1GTFee", Connect);//指定存储过程
                AddUser1GTFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser1GTFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser1GTFee.Parameters.Add(tP);
                }
                if (AddUser1GTFee.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增户主垃圾转运费收费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除户主垃圾转运费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser1GTFee(int BN, int RN, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;

                OracleCommand DelUser1GTFee = new OracleCommand("proc_DelUser1GTFee", Connect);//指定存储过程
                DelUser1GTFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser1GTFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser1GTFee.Parameters.Add(tP);
                }
                if (DelUser1GTFee.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除户主垃圾转运费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 修改户主垃圾转运费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="GTFee">垃圾转运费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1GTFee(int BN, int RN, Double GTFee, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[6];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_GTFee", OracleType.Double);//GTFee参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = GTFee;
                Parm[3] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Year;
                Parm[4] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Month;
                Parm[5] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Day;

                OracleCommand SetUser1GTFee = new OracleCommand("proc_SetUser1GTFee", Connect);//指定存储过程
                SetUser1GTFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser1GTFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser1GTFee.Parameters.Add(tP);
                }
                if (SetUser1GTFee.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置户主垃圾转运费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 查询户主垃圾转运费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据栋号户号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>符号条件的行</returns>
        public static List<String[]> GetUser1GTFee(int BN, int RN, int Year, int Month, int Day, int SearchType, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User1GTFeeList = new List<String[]>();//待返回的户主垃圾转运费缴费细则列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[8];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int32);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;
                Parm[5] = new OracleParameter("v_SearchType", OracleType.Int32);//SearchType参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = SearchType;
                Parm[6] = new OracleParameter("v_LikeQuery", OracleType.Int16);//LikeQuery参数
                Parm[6].Direction = ParameterDirection.Input;//输入
                Parm[6].Value = Convert.ToInt16(LikeQuery);
                Parm[7] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[7].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand GetUser1GTFee = new OracleCommand("proc_GetUser1GTFee", Connect);//指定存储过程
                GetUser1GTFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetUser1GTFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetUser1GTFee.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetUser1GTFee);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User1GTFeeList.Add(new String[] {datatable.Rows[i][0].ToString(),//栋号
                                                        datatable.Rows[i][1].ToString(),//户号
                                                        datatable.Rows[i][2].ToString(),//垃圾转运费
                                                        datatable.Rows[i][3].ToString(),//缴费年份
                                                        datatable.Rows[i][4].ToString(),//缴费月份
                                                        datatable.Rows[i][5].ToString()});//缴费日期
                }
                return User1GTFeeList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询户主垃圾转运费缴费细则失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //户主车辆信息处理
        /// <summary> 新增某户主车辆信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="CarID">车牌号</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser1Car(int BN, int RN, String CarID)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[3];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = CarID;

                OracleCommand AddUser1Car = new OracleCommand("proc_AddUser1Car", Connect);//指定存储过程
                AddUser1Car.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser1Car.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser1Car.Parameters.Add(tP);
                }
                if (AddUser1Car.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增户主车辆信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 根据车牌号删除
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <returns></returns>
        public static Boolean DeleteUser1Car(String CarID)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[1];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;

                OracleCommand DelUser1Car = new OracleCommand("proc_DelUser1Car", Connect);//指定存储过程
                DelUser1Car.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser1Car.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser1Car.Parameters.Add(tP);
                }
                if (DelUser1Car.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除户主车辆信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 设置某车辆对应的户主栋号户号
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="CarID">车牌号</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1Car(int BN, int RN, String CarID)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[3];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = CarID;

                OracleCommand SetUser1Car = new OracleCommand("proc_SetUser1Car", Connect);//指定存储过程
                SetUser1Car.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser1Car.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser1Car.Parameters.Add(tP);
                }
                if (SetUser1Car.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置户主车辆信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 根据栋号户号获取某户主的所有车辆信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>表中符合条件的行</returns>
        public static List<String[]> GetUser1Car(int BN, int RN, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User1CarList = new List<String[]>();//待返回的户主车辆信息列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[4];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("v_LikeQuery", OracleType.Int32);//LikeQuery参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Convert.ToInt16(LikeQuery);
                Parm[3] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[3].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand GetUser1Car = new OracleCommand("proc_GetUser1Car", Connect);//指定存储过程
                GetUser1Car.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetUser1Car.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetUser1Car.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetUser1Car);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User1CarList.Add(new String[] {datatable.Rows[i][0].ToString(),//栋号
                                                   datatable.Rows[i][1].ToString(),//户号
                                                   datatable.Rows[i][2].ToString()});//车牌号
                }
                return User1CarList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询户主车辆信息失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //楼栋统一价格信息处理
        /// <summary> 新增某楼栋统一价格
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="UPC">物业费每平米单价</param>
        /// <param name="PUBC">公摊费</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser1UFee(int BN, Double UPC, Double PUBC)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[3];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_UPC", OracleType.Double);//UPC参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = UPC;
                Parm[2] = new OracleParameter("v_PUBC", OracleType.Double);//PUBC参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = PUBC;

                OracleCommand AddUser1UFee = new OracleCommand("proc_AddUser1UFee", Connect);//指定存储过程
                AddUser1UFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser1UFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser1UFee.Parameters.Add(tP);
                }
                if (AddUser1UFee.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增楼栋价格失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除某楼栋统一价格
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser1UFee(int BN)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[1];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;

                OracleCommand DelUser1UFee = new OracleCommand("proc_DelUser1UFee", Connect);//指定存储过程
                DelUser1UFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser1UFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser1UFee.Parameters.Add(tP);
                }
                if (DelUser1UFee.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除楼栋价格失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 设置某楼栋统一价格
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="UPC">物业费每平米单价</param>
        /// <param name="PUBC">公摊费</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1UFee(int BN, Double UPC, Double PUBC)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[3];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_UPC", OracleType.Double);//UPC参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = UPC;
                Parm[2] = new OracleParameter("v_PUBC", OracleType.Double);//PUBC参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = PUBC;

                OracleCommand SetUser1UFee = new OracleCommand("proc_SetUser1UFee", Connect);//指定存储过程
                SetUser1UFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser1UFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser1UFee.Parameters.Add(tP);
                }
                if (SetUser1UFee.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置楼栋价格信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 获取某楼栋统一价格
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>表中符合条件的行</returns>
        public static List<String[]> GetUser1UFee(int BN, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User1UFeeList = new List<String[]>();//待返回的楼栋统一价格信息列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[3];//实例化参数列表
                Parm[0] = new OracleParameter("v_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("v_LikeQuery", OracleType.Int32);//LikeQuery参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Convert.ToInt16(LikeQuery);
                Parm[2] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[2].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand GetUser1UFee = new OracleCommand("proc_GetUser1UFee", Connect);//指定存储过程
                GetUser1UFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetUser1UFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetUser1UFee.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetUser1UFee);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User1UFeeList.Add(new String[] {datatable.Rows[i][0].ToString(),//栋号
                                                   datatable.Rows[i][1].ToString(),//物业费每平米单价
                                                   datatable.Rows[i][2].ToString()});//公摊费
                }
                return User1UFeeList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询楼栋统一价格信息失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //店面信息处理
        /// <summary> 获取店面信息，根据查询类型对相应参数填null
        /// </summary>
        /// <param name="SearchType">查询类型 0：根据店面编号 1：根据店名 2：根据店主姓名 3：根据电话</param>
        /// <param name="SN">店面编号</param>
        /// <param name="SName">店名</param>
        /// <param name="Name">店主姓名</param>
        /// <param name="Tel">电话</param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>表中符合条件的行</returns>
        public static List<String[]> GetUser2Info(int SearchType, int SN, String SName, String Name, String Tel, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User2List = new List<String[]>();//待返回的店面信息列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[7];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_SName", OracleType.VarChar);//SName参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = SName;
                Parm[2] = new OracleParameter("v_Name", OracleType.VarChar);//Name参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Name;
                Parm[3] = new OracleParameter("v_Tel", OracleType.VarChar);//Tel参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Tel;
                Parm[4] = new OracleParameter("v_LikeQuery", OracleType.Int32);//模糊查找参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Convert.ToInt16(LikeQuery);
                Parm[5] = new OracleParameter("v_SearchType", OracleType.Int32);//查找类型参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = SearchType;
                Parm[6] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[6].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand QueryUser2 = new OracleCommand("proc_GetUser2Info", Connect);//指定存储过程
                QueryUser2.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                QueryUser2.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    QueryUser2.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(QueryUser2);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User2List.Add(new String[] {datatable.Rows[i][0].ToString(),//店面编号
                                                datatable.Rows[i][1].ToString(),//面积
                                                datatable.Rows[i][2].ToString(),//店名
                                                datatable.Rows[i][3].ToString(),//店主姓名
                                                datatable.Rows[i][4].ToString()});//电话
                }
                return User2List;//查询成功
            }
            catch (Exception)
            {
                return null;//查询店面信息表失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 根据店面编号设置店面信息
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Area">面积</param>
        /// <param name="SName">店名</param>
        /// <param name="Name">店主姓名</param>
        /// <param name="Tel">电话</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser2Info(int SN, Double Area, String SName, String Name, String Tel)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Area", OracleType.Double);//Area参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Area;
                Parm[2] = new OracleParameter("v_SName", OracleType.VarChar);//SName参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = SName;
                Parm[3] = new OracleParameter("v_Name", OracleType.VarChar);//Name参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Name;
                Parm[4] = new OracleParameter("v_Tel", OracleType.VarChar);//Tel参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Tel;

                OracleCommand SetUser2 = new OracleCommand("proc_SetUser2Info", Connect);//指定存储过程
                SetUser2.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser2.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser2.Parameters.Add(tP);
                }
                if (SetUser2.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置店面信息表失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 新增店面信息
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Area">面积</param>
        /// <param name="SName">店名</param>
        /// <param name="Name">店主姓名</param>
        /// <param name="Tel">电话</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser2Info(int SN, Double Area, String SName, String Name, String Tel)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Area", OracleType.Double);//Area参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Area;
                Parm[2] = new OracleParameter("v_SName", OracleType.VarChar);//SName参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = SName;
                Parm[3] = new OracleParameter("v_Name", OracleType.VarChar);//Name参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Name;
                Parm[4] = new OracleParameter("v_Tel", OracleType.VarChar);//Tel参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Tel;

                OracleCommand AddUser2 = new OracleCommand("proc_AddUser2Info", Connect);//指定存储过程
                AddUser2.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser2.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser2.Parameters.Add(tP);
                }
                if (AddUser2.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增店面信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除店面信息
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser2Info(int SN)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                //删除户主
                OracleParameter[] Parm = new OracleParameter[1];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int16);//店面编号参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;

                OracleCommand DelUser2 = new OracleCommand("proc_DelUser2", Connect);//指定存储过程
                DelUser2.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser2.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser2.Parameters.Add(tP);
                }
                //调用存储过程
                if (DelUser2.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除店面信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //店面对应月份缴费标志信息处理
        /// <summary> 新增某店面某月缴费标志信息
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="PCFee_Flag">物业费标志</param>
        /// <param name="PUBCFee_Flag">公摊费标志</param>
        /// <param name="ELEFee_Flag">电费标志</param>
        /// <param name="GTFee_Flag">垃圾转运费标志</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser2FeeFlag(int SN, int Year, int Month, Boolean PCFee_Flag, Boolean PUBCFee_Flag, Boolean ELEFee_Flag, Boolean GTFee_Flag)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[7];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_PCFee_Flag", OracleType.Int16);//PCFee_Flag参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Convert.ToInt16(PCFee_Flag);
                Parm[4] = new OracleParameter("v_PUBCFee_Flag", OracleType.Int16);//PUBCFee_Flag参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Convert.ToInt16(PUBCFee_Flag);
                Parm[5] = new OracleParameter("v_ELEFee_Flag", OracleType.Int16);//ELEFee_Flag参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Convert.ToInt16(ELEFee_Flag);
                Parm[6] = new OracleParameter("v_GTFee_Flag", OracleType.Int16);//GTFee_Flag参数
                Parm[6].Direction = ParameterDirection.Input;//输入
                Parm[6].Value = Convert.ToInt16(GTFee_Flag);

                OracleCommand AddUser2FeeFlag = new OracleCommand("proc_AddUser2FeeFlag", Connect);//指定存储过程
                AddUser2FeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser2FeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser2FeeFlag.Parameters.Add(tP);
                }
                if (AddUser2FeeFlag.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增店面缴费标志信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 按一定条件删除某店面的缴费标志信息
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="DeleteType">删除规则：
        ///     0：按店面编号删除店面的所有交费信息
        ///     1：按店面编号与年份删除某店面某年的所有交费信息
        ///     2：按店面编号与年份月份删除某店面某月的所有交费信息
        ///     3：按年份删除所有店面某年的所有交费信息
        /// </param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser2FeeFlag(int SN, int Year, int Month, int DeleteType)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[4];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_DeleteType", OracleType.Int16);//DeleteType参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = DeleteType;

                OracleCommand DelUser2FeeFlag = new OracleCommand("proc_DelUser2FeeFlag", Connect);//指定存储过程
                DelUser2FeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser2FeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser2FeeFlag.Parameters.Add(tP);
                }
                if (DelUser2FeeFlag.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除店面缴费标志信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 设置某店面某月的交费标志信息
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="PCFee_Flag">物业费标志</param>
        /// <param name="PUBCFee_Flag">公摊费标志</param>
        /// <param name="ELEFee_Flag">电费标志</param>
        /// <param name="GTFee_Flag">垃圾转运费标志</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser2FeeFlag(int SN, int Year, int Month, Boolean PCFee_Flag, Boolean PUBCFee_Flag, Boolean ELEFee_Flag, Boolean GTFee_Flag)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[7];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_PCFee_Flag", OracleType.Int16);//PCFee_Flag参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Convert.ToInt16(PCFee_Flag);
                Parm[4] = new OracleParameter("v_PUBCFee_Flag", OracleType.Int16);//PUBCFee_Flag参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Convert.ToInt16(PUBCFee_Flag);
                Parm[5] = new OracleParameter("v_ELEFee_Flag", OracleType.Int16);//ELEFee_Flag参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Convert.ToInt16(ELEFee_Flag);
                Parm[6] = new OracleParameter("v_GTFee_Flag", OracleType.Int16);//GTFee_Flag参数
                Parm[6].Direction = ParameterDirection.Input;//输入
                Parm[6].Value = Convert.ToInt16(GTFee_Flag);

                OracleCommand SetUser2FeeFlag = new OracleCommand("proc_SetUser2FeeFlag", Connect);//指定存储过程
                SetUser2FeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser2FeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser2FeeFlag.Parameters.Add(tP);
                }
                if (SetUser2FeeFlag.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置店面缴费标志信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 按店面编号年份月份查询店面的缴费标志信息
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>表中符合条件的行</returns>
        public static List<String[]> GetUser2FeeFlag(int SN, int Year, int Month, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User2FeeFlagList = new List<String[]>();//待返回的店面缴费标志列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_LikeQuery", OracleType.Int16);//LikeQuery参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = LikeQuery;
                Parm[4] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[4].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand GetUser2FeeFlag = new OracleCommand("proc_GetUser2FeeFlag", Connect);//指定存储过程
                GetUser2FeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetUser2FeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetUser2FeeFlag.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetUser2FeeFlag);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User2FeeFlagList.Add(new String[] {datatable.Rows[i][0].ToString(),//店面编号
                                                        datatable.Rows[i][1].ToString(),//年份
                                                        datatable.Rows[i][2].ToString(),//月份
                                                        datatable.Rows[i][3].ToString(),//物业费标志
                                                        datatable.Rows[i][4].ToString(),//公摊费标志
                                                        datatable.Rows[i][5].ToString(),//电费标志
                                                        datatable.Rows[i][6].ToString()});//垃圾转运费标志
                }
                return User2FeeFlagList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询店面缴费标志信息失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //店面物业费收费细则处理
        /// <summary> 新增店面物业费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="PCFee">物业费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <param name="Bonus">优惠金额</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser2PCFee(int SN, Double PCFee, int Year, int Month, int Day, Double Bonus)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[6];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_PCFee", OracleType.Double);//PCFee参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = PCFee;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int16);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;
                Parm[5] = new OracleParameter("v_Bonus", OracleType.Double);//Bonus参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Bonus;

                OracleCommand AddUser2PCFee = new OracleCommand("proc_AddUser2PCFee", Connect);//指定存储过程
                AddUser2PCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser2PCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser2PCFee.Parameters.Add(tP);
                }
                if (AddUser2PCFee.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增店面物业费收费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除店面物业费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser2PCFee(int SN, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[4];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Day;

                OracleCommand DelUser2PCFee = new OracleCommand("proc_DelUser2PCFee", Connect);//指定存储过程
                DelUser2PCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser2PCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser2PCFee.Parameters.Add(tP);
                }
                if (DelUser2PCFee.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除店面物业费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 修改店面物业费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="PCFee">物业费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <param name="Bonus">优惠金额</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser2PCFee(int SN, Double PCFee, int Year, int Month, int Day, Double Bonus)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[6];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_PCFee", OracleType.Double);//PCFee参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = PCFee;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;
                Parm[5] = new OracleParameter("v_Bonus", OracleType.Double);//Bonus参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Bonus;

                OracleCommand SetUser2PCFee = new OracleCommand("proc_SetUser2PCFee", Connect);//指定存储过程
                SetUser2PCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser2PCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser2PCFee.Parameters.Add(tP);
                }
                if (SetUser2PCFee.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置店面物业费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 查询店面物业费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据店面编号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetUser2PCFee(int SN, int Year, int Month, int Day, int SearchType, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User2PCFeeList = new List<String[]>();//待返回的店面物业费缴费细则列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[7];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_Day", OracleType.Int32);//Day参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Day;
                Parm[4] = new OracleParameter("v_SearchType", OracleType.Int32);//SearchType参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = SearchType;
                Parm[5] = new OracleParameter("v_LikeQuery", OracleType.Int16);//LikeQuery参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Convert.ToInt16(LikeQuery);
                Parm[6] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[6].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand GetUser2PCFee = new OracleCommand("proc_GetUser2PCFee", Connect);//指定存储过程
                GetUser2PCFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetUser2PCFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetUser2PCFee.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetUser2PCFee);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User2PCFeeList.Add(new String[] {datatable.Rows[i][0].ToString(),//店面编号
                                                        datatable.Rows[i][1].ToString(),//物业费
                                                        datatable.Rows[i][2].ToString(),//缴费年份
                                                        datatable.Rows[i][3].ToString(),//缴费月份
                                                        datatable.Rows[i][4].ToString(),//缴费日期
                                                        datatable.Rows[i][5].ToString()});//优惠金额
                }
                return User2PCFeeList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询店面物业费缴费细则失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //店面电费收费细则处理
        /// <summary> 新增店面电费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="ELEFee">电费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser2ELEFee(int SN, Double ELEFee, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_ELEFee", OracleType.Double);//ELEFee参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = ELEFee;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int16);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;

                OracleCommand AddUser2ELEFee = new OracleCommand("proc_AddUser2ELEFee", Connect);//指定存储过程
                AddUser2ELEFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser2ELEFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser2ELEFee.Parameters.Add(tP);
                }
                if (AddUser2ELEFee.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增店面电费收费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除店面电费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser2ELEFee(int SN, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[4];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Day;

                OracleCommand DelUser2ELEFee = new OracleCommand("proc_DelUser2ELEFee", Connect);//指定存储过程
                DelUser2ELEFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser2ELEFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser2ELEFee.Parameters.Add(tP);
                }
                if (DelUser2ELEFee.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除店面电费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 修改店面电费收费细则
        /// </summary>
        /// <param name="SN">栋号</param>
        /// <param name="ELEFee">电费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser2ELEFee(int SN, Double ELEFee, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_ELEFee", OracleType.Double);//ELEFee参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = ELEFee;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;

                OracleCommand SetUser2ELEFee = new OracleCommand("proc_SetUser2ELEFee", Connect);//指定存储过程
                SetUser2ELEFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser2ELEFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser2ELEFee.Parameters.Add(tP);
                }
                if (SetUser2ELEFee.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置店面电费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 查询店面电费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据店面编号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetUser2ELEFee(int SN, int Year, int Month, int Day, int SearchType, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User2ELEFeeList = new List<String[]>();//待返回的店面电费缴费细则列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[7];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_Day", OracleType.Int32);//Day参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Day;
                Parm[4] = new OracleParameter("v_SearchType", OracleType.Int32);//SearchType参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = SearchType;
                Parm[5] = new OracleParameter("v_LikeQuery", OracleType.Int16);//LikeQuery参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Convert.ToInt16(LikeQuery);
                Parm[6] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[6].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand GetUser2ELEFee = new OracleCommand("proc_GetUser2ELEFee", Connect);//指定存储过程
                GetUser2ELEFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetUser2ELEFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetUser2ELEFee.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetUser2ELEFee);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User2ELEFeeList.Add(new String[] {datatable.Rows[i][0].ToString(),//店面编号
                                                        datatable.Rows[i][1].ToString(),//电费
                                                        datatable.Rows[i][2].ToString(),//缴费年份
                                                        datatable.Rows[i][3].ToString(),//缴费月份
                                                        datatable.Rows[i][4].ToString()});//缴费日期
                }
                return User2ELEFeeList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询店面电费缴费细则失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //户主垃圾转运费收费细则处理
        /// <summary> 新增店面垃圾转运费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="GTFee">垃圾转运费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser2GTFee(int SN, Double GTFee, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_GTFee", OracleType.Double);//GTFee参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = GTFee;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int16);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;

                OracleCommand AddUser2GTFee = new OracleCommand("proc_AddUser2GTFee", Connect);//指定存储过程
                AddUser2GTFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser2GTFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser2GTFee.Parameters.Add(tP);
                }
                if (AddUser2GTFee.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增店面垃圾转运费收费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除店面垃圾转运费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser2GTFee(int SN, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[4];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Day;

                OracleCommand DelUser2GTFee = new OracleCommand("proc_DelUser2GTFee", Connect);//指定存储过程
                DelUser2GTFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser2GTFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser2GTFee.Parameters.Add(tP);
                }
                if (DelUser2GTFee.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除店面垃圾转运费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 修改店面垃圾转运费收费细则
        /// </summary>
        /// <param name="SN">栋号</param>
        /// <param name="GTFee">垃圾转运费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser2GTFee(int SN, Double GTFee, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_GTFee", OracleType.Double);//GTFee参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = GTFee;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;

                OracleCommand SetUser2GTFee = new OracleCommand("proc_SetUser2GTFee", Connect);//指定存储过程
                SetUser2GTFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser2GTFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser2GTFee.Parameters.Add(tP);
                }
                if (SetUser2GTFee.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置店面垃圾转运费缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 查询店面垃圾转运费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据店面编号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetUser2GTFee(int SN, int Year, int Month, int Day, int SearchType, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User2GTFeeList = new List<String[]>();//待返回的店面垃圾转运费缴费细则列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[7];//实例化参数列表
                Parm[0] = new OracleParameter("v_SN", OracleType.Int32);//SN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = SN;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_Day", OracleType.Int32);//Day参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Day;
                Parm[4] = new OracleParameter("v_SearchType", OracleType.Int32);//SearchType参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = SearchType;
                Parm[5] = new OracleParameter("v_LikeQuery", OracleType.Int16);//LikeQuery参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Convert.ToInt16(LikeQuery);
                Parm[6] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[6].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand GetUser2GTFee = new OracleCommand("proc_GetUser2GTFee", Connect);//指定存储过程
                GetUser2GTFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetUser2GTFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetUser2GTFee.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetUser2GTFee);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User2GTFeeList.Add(new String[] {datatable.Rows[i][0].ToString(),//店面编号
                                                        datatable.Rows[i][1].ToString(),//垃圾转运费
                                                        datatable.Rows[i][2].ToString(),//缴费年份
                                                        datatable.Rows[i][3].ToString(),//缴费月份
                                                        datatable.Rows[i][4].ToString()});//缴费日期
                }
                return User2GTFeeList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询店面垃圾转运费缴费细则失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //外租车信息处理
        /// <summary> 获取外租车信息，根据查询类型对相应参数填null
        /// </summary>
        /// <param name="SearchType">查询类型 0：根据车牌号 1：根据姓名 2：根据电话</param>
        /// <param name="CarID">车牌号</param>
        /// <param name="Name">姓名</param>
        /// <param name="Tel">电话</param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>表中符合条件的行</returns>
        public static List<String[]> GetUser3Info(int SearchType, String CarID, String Name, String Tel, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> User3List = new List<String[]>();//待返回的外租车主信息列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[6];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;
                Parm[1] = new OracleParameter("v_Name", OracleType.VarChar);//Name参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Name;
                Parm[2] = new OracleParameter("v_Tel", OracleType.VarChar);//Tel参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Tel;
                Parm[3] = new OracleParameter("v_LikeQuery", OracleType.Int32);//模糊查找参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Convert.ToInt16(LikeQuery);
                Parm[4] = new OracleParameter("v_SearchType", OracleType.Int32);//查找类型参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = SearchType;
                Parm[5] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[5].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand QueryUser3 = new OracleCommand("proc_GetUser3Info", Connect);//指定存储过程
                QueryUser3.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                QueryUser3.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    QueryUser3.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(QueryUser3);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    User3List.Add(new String[] {datatable.Rows[i][0].ToString(),//车牌号
                                                datatable.Rows[i][1].ToString(),//姓名
                                                datatable.Rows[i][2].ToString()});//电话
                }
                return User3List;//查询成功
            }
            catch (Exception)
            {
                return null;//查询外租车主信息表失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 根据外租车车牌号设置信息
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Name">姓名</param>
        /// <param name="Tel">电话</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser3Info(String CarID, String Name, String Tel)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[3];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;
                Parm[1] = new OracleParameter("v_Name", OracleType.VarChar);//Name参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Name;
                Parm[2] = new OracleParameter("v_Tel", OracleType.VarChar);//Tel参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Tel;

                OracleCommand SetUser3 = new OracleCommand("proc_SetUser3Info", Connect);//指定存储过程
                SetUser3.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetUser3.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetUser3.Parameters.Add(tP);
                }
                if (SetUser3.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置外租车主信息表失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 新增外租车信息
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Name">店主姓名</param>
        /// <param name="Tel">电话</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser3Info(String CarID, String Name, String Tel)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[3];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;
                Parm[1] = new OracleParameter("v_Name", OracleType.VarChar);//Name参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Name;
                Parm[2] = new OracleParameter("v_Tel", OracleType.VarChar);//Tel参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Tel;

                OracleCommand AddUser3 = new OracleCommand("proc_AddUser3Info", Connect);//指定存储过程
                AddUser3.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddUser3.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddUser3.Parameters.Add(tP);
                }
                if (AddUser3.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增外租车主信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除外租车信息
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser3Info(String CarID)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                //删除户主
                OracleParameter[] Parm = new OracleParameter[1];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//车牌号参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;

                OracleCommand DelUser3 = new OracleCommand("proc_DelUser3", Connect);//指定存储过程
                DelUser3.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelUser3.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelUser3.Parameters.Add(tP);
                }
                //调用存储过程
                if (DelUser3.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除外租车主信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //车辆对应月份缴费标志信息处理
        /// <summary> 新增某车辆缴费标志信息
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="CarFee_Flag">停车费标志</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddCarFeeFlag(String CarID, int Year, int Month, Boolean CarFee_Flag)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[7];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_CarFee_Flag", OracleType.Int16);//CarFee_Flag参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Convert.ToInt16(CarFee_Flag);


                OracleCommand AddCarFeeFlag = new OracleCommand("proc_AddCarFeeFlag", Connect);//指定存储过程
                AddCarFeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddCarFeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddCarFeeFlag.Parameters.Add(tP);
                }
                if (AddCarFeeFlag.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增车辆缴费标志信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 按一定条件删除某车辆的缴费标志信息
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="DeleteType">删除规则：
        ///     0：按车牌号删除车辆的所有交费信息
        ///     1：按车牌号与年份删除某车某年的所有交费信息
        ///     2：按车牌号与年份月份删除某车某月的所有交费信息
        ///     3：按年份删除所有车辆某年的所有交费信息
        /// </param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteCarFeeFlag(String CarID, int Year, int Month, int DeleteType)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[4];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_DeleteType", OracleType.Int16);//DeleteType参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = DeleteType;

                OracleCommand DelCarFeeFlag = new OracleCommand("proc_DelCarFeeFlag", Connect);//指定存储过程
                DelCarFeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelCarFeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelCarFeeFlag.Parameters.Add(tP);
                }
                if (DelCarFeeFlag.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除车辆缴费标志信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 设置某车辆某月的交费标志信息
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="CarFee_Flag">停车费标志</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetCarFeeFlag(String CarID, int Year, int Month, Boolean CarFee_Flag)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[4];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_CarFee_Flag", OracleType.Int16);//CarFee_Flag参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Convert.ToInt16(CarFee_Flag);
                
                OracleCommand SetCarFeeFlag = new OracleCommand("proc_SetCarFeeFlag", Connect);//指定存储过程
                SetCarFeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetCarFeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetCarFeeFlag.Parameters.Add(tP);
                }
                if (SetCarFeeFlag.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置车费缴费标志信息失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 按车牌号年份月份查询车辆的缴费标志信息
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>表中符合条件的行</returns>
        public static List<String[]> GetCarFeeFlag(String CarID, int Year, int Month, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> CarFeeFlagList = new List<String[]>();//待返回的车费缴费细则列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[4];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_LikeQuery", OracleType.Int32);//LikeQuery参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Convert.ToInt16(LikeQuery);
                
                OracleCommand GetCarFeeFlag = new OracleCommand("proc_GetCarFeeFlag", Connect);//指定存储过程
                GetCarFeeFlag.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetCarFeeFlag.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetCarFeeFlag.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetCarFeeFlag);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    CarFeeFlagList.Add(new String[] {datatable.Rows[i][0].ToString(),//车牌号
                                                        datatable.Rows[i][1].ToString(),//年份
                                                        datatable.Rows[i][2].ToString(),//月份
                                                        datatable.Rows[i][3].ToString()});//停车费标志
                }
                return CarFeeFlagList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询车费缴费标志失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }

        //车辆收费细则处理
        /// <summary> 新增车辆停车费收费细则
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="CarFee">停车费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddCarFee(String CarID, Double CarFee, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;
                Parm[1] = new OracleParameter("v_CarFee", OracleType.Double);//CarFee参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = CarFee;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int16);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;

                OracleCommand AddCarFee = new OracleCommand("proc_AddCarFee", Connect);//指定存储过程
                AddCarFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                AddCarFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    AddCarFee.Parameters.Add(tP);
                }
                if (AddCarFee.ExecuteNonQuery() == 0)
                    return false;//添加失败
                else
                    return true;//添加成功
            }
            catch (Exception)
            {
                return false;//新增车辆收费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 删除车辆停车费收费细则
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteCarFee(String CarID, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[4];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int16);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Day;

                OracleCommand DelCarFee = new OracleCommand("proc_DelCarFee", Connect);//指定存储过程
                DelCarFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                DelCarFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    DelCarFee.Parameters.Add(tP);
                }
                if (DelCarFee.ExecuteNonQuery() == 0)
                    return false;//删除失败
                else
                    return true;//删除成功
            }
            catch (Exception)
            {
                return false;//删除车辆缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 修改车辆停车费收费细则
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="CarFee">停车费</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetCarFee(String CarID, Double CarFee, int Year, int Month, int Day)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[5];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;
                Parm[1] = new OracleParameter("v_CarFee", OracleType.Double);//CarFee参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = CarFee;
                Parm[2] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Year;
                Parm[3] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Month;
                Parm[4] = new OracleParameter("v_Day", OracleType.Int16);//Day参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = Day;

                OracleCommand SetCarFee = new OracleCommand("proc_SetCarFee", Connect);//指定存储过程
                SetCarFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                SetCarFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    SetCarFee.Parameters.Add(tP);
                }
                if (SetCarFee.ExecuteNonQuery() == 0)
                    return false;//设置失败
                else
                    return true;//设置成功
            }
            catch (Exception)
            {
                return false;//设置车辆缴费细则失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 查询车辆停车费收费细则
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Year">缴费年份</param>
        /// <param name="Month">缴费月份</param>
        /// <param name="Day">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据车牌号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <param name="LikeQuery">是否模糊查询</param>
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetCarFee(String CarID, int Year, int Month, int Day, int SearchType, Boolean LikeQuery)
        {
            String Connect_Str = GetDBConnectStr();//获取数据库连接参数字符串
            List<String[]> CarFeeList = new List<String[]>();//待返回的车辆缴费细则列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[7];//实例化参数列表
                Parm[0] = new OracleParameter("v_CarID", OracleType.VarChar);//CarID参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = CarID;
                Parm[1] = new OracleParameter("v_Year", OracleType.Int32);//Year参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Year;
                Parm[2] = new OracleParameter("v_Month", OracleType.Int32);//Month参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Month;
                Parm[3] = new OracleParameter("v_Day", OracleType.Int32);//Day参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Day;
                Parm[4] = new OracleParameter("v_SearchType", OracleType.Int32);//SearchType参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = SearchType;
                Parm[5] = new OracleParameter("v_LikeQuery", OracleType.Int16);//LikeQuery参数
                Parm[5].Direction = ParameterDirection.Input;//输入
                Parm[5].Value = Convert.ToInt16(LikeQuery);
                Parm[6] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[6].Direction = ParameterDirection.Output;//定义引用游标输出参数

                OracleCommand GetCarFee = new OracleCommand("proc_GetCarFee", Connect);//指定存储过程
                GetCarFee.CommandType = CommandType.StoredProcedure;//本次查询为存储过程
                GetCarFee.Parameters.Clear();//清空参数列表
                foreach (OracleParameter tP in Parm)
                {//填充参数列表
                    GetCarFee.Parameters.Add(tP);
                }
                OracleDataAdapter OA = new OracleDataAdapter(GetCarFee);
                DataTable datatable = new DataTable();
                OA.Fill(datatable);//调用存储过程并拉取数据
                int i = datatable.Rows.Count;//循环行数次
                while ((i--) != 0)//按行读取，直到结尾
                {
                    CarFeeList.Add(new String[] {datatable.Rows[i][0].ToString(),//车牌号
                                                 datatable.Rows[i][1].ToString(),//停车费
                                                 datatable.Rows[i][2].ToString(),//缴费年份
                                                 datatable.Rows[i][3].ToString(),//缴费月份
                                                 datatable.Rows[i][4].ToString()});//缴费日期
                }
                return CarFeeList;//查询成功
            }
            catch (Exception)
            {
                return null;//查询车辆缴费细则失败则返回null
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
    }
}
