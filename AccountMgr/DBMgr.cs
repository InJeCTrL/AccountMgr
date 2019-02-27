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
        /// <summary> 操作员的数据库用户名
        /// </summary>
        private static String UserCode_OP;
        /// <summary> 操作员的数据库密码
        /// </summary>
        private static String Password_OP;
        /// <summary> 操作员成功登入标志
        /// </summary>
        private static Boolean OPLogin = false;
        /// <summary> 管理员的数据库用户名
        /// </summary>
        private static String UserCode_AD;
        /// <summary> 管理员的数据库密码
        /// </summary>
        private static String Password_AD;
        /// <summary> 管理员成功登入标志
        /// </summary>
        private static Boolean ADLogin = false;

        /// <summary> 获取操作员连接数据库的登入字符串
        /// </summary>
        /// <returns>操作员已登入成功则返回登入字符串，否则返回空</returns>
        private static String GetOPConnectStr()
        {
            if (OPLogin)
            {
                return "User ID=" + UserCode_OP + ";" +    //操作员用户名
                      "Password=" + Password_OP +          //操作员密码
                      ";Data Source=(DESCRIPTION = (ADDRESS_LIST= (ADDRESS = " +
                      "(PROTOCOL = TCP)(HOST = 127.0.0.1)(PORT = 1521))) " +        //数据库地址localhost，端口1521
                      "(CONNECT_DATA = (SERVICE_NAME = xe)))";                      //数据库表示XE
            }
            else
            {
                return null;
            }
        }
        /// <summary> 获取管理员连接数据库的登入字符串
        /// </summary>
        /// <returns>管理员已登入成功则返回登入字符串，否则返回空</returns>
        private static String GetADConnectStr()
        {
            if (ADLogin)
            {
                return "User ID=" + UserCode_AD + ";" +    //管理员用户名
                      "Password=" + Password_AD +          //管理员密码
                      ";Data Source=(DESCRIPTION = (ADDRESS_LIST= (ADDRESS = " +
                      "(PROTOCOL = TCP)(HOST = 127.0.0.1)(PORT = 1521))) " +        //数据库地址localhost，端口1521
                      "(CONNECT_DATA = (SERVICE_NAME = xe)))";                      //数据库表示XE
            }
            else
            {
                return null;
            }
        }
        
        /// <summary> 检查管理系统的数据库各项表格、存储过程等是否已初始化
        /// </summary>
        /// <returns>已初始化返回真，否则返回假</returns>
        public static Boolean IsInitialized()
        {
            if (File.Exists(Environment.CurrentDirectory + "/Config.inf"))//根目录存在配置文件
            {
                String[] Config_Lines = File.ReadAllLines(Environment.CurrentDirectory + "/Config.inf");//读取配置文件中所有行

                //配置文件只有Initialized一行，认为是已经初始化
                if (Config_Lines.Length == 1 && Config_Lines[0].Equals("Initialized"))
                {
                    return true;
                }
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
        /// <summary> 新增操作员或管理员
        /// </summary>
        /// <param name="UserCode">登入用户名</param>
        /// <param name="Password">登入密码</param>
        /// <param name="OP">是否是操作员</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser(String UserCode, String Password, Boolean OP)
        {
            String Connect_Str = GetADConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                //添加数据库用户
                OracleCommand AddUserSQL = new OracleCommand(@"Create USER '"+ UserCode + "' IDENTIFIED BY '" + Password + "'");
                AddUserSQL.Connection = Connect;//指定连接
                AddUserSQL.ExecuteNonQuery();//执行新建

                //插入管理系统用户表
                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("v_UserCode", OracleType.VarChar);//UserCode参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = UserCode;
                Parm[1] = new OracleParameter("v_OP", OracleType.Int16);//OP参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Convert.ToInt16(OP);

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
            String Connect_Str = GetADConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                //删除数据库用户
                OracleCommand DelUserSQL = new OracleCommand(@"Drop USER '" + UserCode + "'");
                DelUserSQL.Connection = Connect;//指定连接
                DelUserSQL.ExecuteNonQuery();//执行新建

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
        /// <param name="OP">是否是操作员</param>
        /// <returns>设置成功则返回真，否则返回假</returns>
        public static Boolean SetUser(String UserCode, String Password, Boolean OP)
        {
            String Connect_Str = GetADConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                //设置数据库用户
                OracleCommand AlterUserSQL = new OracleCommand(@"Alter USER '" + UserCode + "' IDENTIFIED BY '" + Password + "'");
                AlterUserSQL.Connection = Connect;//指定连接
                AlterUserSQL.ExecuteNonQuery();//执行新建

                //设置管理系统用户表
                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("v_UserCode", OracleType.VarChar);//UserCode参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = UserCode;
                Parm[1] = new OracleParameter("v_OP", OracleType.Int16);//OP参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = Convert.ToInt16(OP);

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
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetUser(String UserCode)
        {
            String Connect_Str = GetOPConnectStr();//获取数据库连接参数字符串
            List<String[]> UserList = new List<String[]>();//待返回的操作员、管理员信息列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("v_UserCode", OracleType.VarChar);//UserCode参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = UserCode;
                Parm[1] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[1].Direction = ParameterDirection.Output;//定义引用游标输出参数

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
                                                datatable.Rows[i][1].ToString()});//是否是操作员
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
        /// <summary> 检查操作员用户是否正确
        /// </summary>
        /// <param name="_UserCode_OP">操作员用户名</param>
        /// <param name="_Password_OP">操作员密码</param>
        /// <returns>认证通过返回真，否则返回假</returns>
        public static Boolean CheckOPUser(String _UserCode_OP, String _Password_OP)
        {
            UserCode_OP = _UserCode_OP;
            Password_OP = _Password_OP;
            OPLogin = true;//临时赋值，用于尝试登入
            List<String[]> Users = GetUser(UserCode_OP);//获取用户列表

            //查询成功
            if (Users != null)
            { 
                foreach (String[] p in Users)
                {
                    //返回的列表中找到登入名相同的用户
                    if (p[0].Equals(UserCode_OP))
                    {
                        //身份是操作员
                        if (p[1].Equals("1"))
                        {
                            return true;
                        }
                        //身份是管理员
                        else
                        {
                            break;
                        }
                    }
                }
            }
            //登入失败（没有记录这个操作员或管理员或身份不匹配）
            UserCode_OP = null;
            Password_OP = null;
            OPLogin = false;//撤销临时赋值
            return false;
        }
        /// <summary> 检查管理员用户是否正确
        /// </summary>
        /// <param name="_UserCode_OP">管理员用户名</param>
        /// <param name="_Password_OP">管理员密码</param>
        /// <returns>认证通过返回真，否则返回假</returns>
        public static Boolean CheckADUser(String _UserCode_AD, String _Password_AD)
        {
            UserCode_AD = _UserCode_AD;
            Password_AD = _Password_AD;
            ADLogin = true;//临时赋值，用于尝试登入
            List<String[]> Users = GetUser(UserCode_AD);//获取用户列表

            //查询成功
            if (Users != null)
            {
                foreach (String[] p in Users)
                {
                    //返回的列表中找到登入名相同的用户
                    if (p[0].Equals(UserCode_AD))
                    {
                        //身份是操作员
                        if (p[1].Equals("1"))
                        {
                            break;
                        }
                        //身份是管理员
                        else
                        {
                            return true;
                        }
                    }
                }
            }
            //登入失败（没有记录这个操作员或管理员或身份不匹配）
            UserCode_AD = null;
            Password_AD = null;
            ADLogin = false;//撤销临时赋值
            return false;
        }

        //单独数据处理
        /// <summary> 设置垃圾转运费
        /// </summary>
        /// <param name="Fee">垃圾转运费</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetGTFee(Double Fee)
        {
            String Connect_Str = GetOPConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("V_Fee", OracleType.Number);//垃圾转运费
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = Fee;
                Parm[1] = new OracleParameter("V_ItemID", OracleType.Int16);//垃圾转运费项目编号
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = 0;

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
                return false;//设置垃圾转运费失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 获取垃圾转运费
        /// </summary>
        /// <returns>获取成功返回非负数，否则返回-1</returns>
        public static Double GetGTFee()
        {
            String Connect_Str = GetOPConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("V_Fee", OracleType.Number);//垃圾转运费
                Parm[0].Direction = ParameterDirection.Output;//输出
                Parm[1] = new OracleParameter("V_ItemID", OracleType.Int16);//垃圾转运费项目编号
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = 0;

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
                return -1;//获取垃圾转运费失败则返回-1
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 设置店面电费单价
        /// </summary>
        /// <param name="UFee">店面电费单价</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetShopEleUFee(Double UFee)
        {
            String Connect_Str = GetOPConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("V_Fee", OracleType.Number);//店面电费单价
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = UFee;
                Parm[1] = new OracleParameter("V_ItemID", OracleType.Int16);//店面电费单价项目编号
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = 1;

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
                return false;//设置店面电费单价失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 获取店面电费单价
        /// </summary>
        /// <returns>获取成功返回非负数，否则返回-1</returns>
        public static Double GetShopEleUFee()
        {
            String Connect_Str = GetOPConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("V_Fee", OracleType.Number);//店面电费单价
                Parm[0].Direction = ParameterDirection.Output;//输出
                Parm[1] = new OracleParameter("V_ItemID", OracleType.Int16);//店面电费单价项目编号
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = 1;

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
                return -1;//获取店面电费单价失败则返回-1
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 设置店面物业费单价
        /// </summary>
        /// <param name="UFee">店面物业费单价</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetShopPCUFee(Double UFee)
        {
            String Connect_Str = GetOPConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("V_Fee", OracleType.Number);//店面物业费单价
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = UFee;
                Parm[1] = new OracleParameter("V_ItemID", OracleType.Int16);//店面物业费单价项目编号
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = 2;

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
                return false;//设置店面物业费单价失败则返回假
            }
            finally
            {
                Connect.Close();//最后必须关闭数据库连接
            }
        }
        /// <summary> 获取店面物业费单价
        /// </summary>
        /// <returns>获取成功返回非负数，否则返回-1</returns>
        public static Double GetShopPCUFee()
        {
            String Connect_Str = GetOPConnectStr();//获取数据库连接参数字符串
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[2];//实例化参数列表
                Parm[0] = new OracleParameter("V_Fee", OracleType.Number);//店面物业费单价
                Parm[0].Direction = ParameterDirection.Output;//输出
                Parm[1] = new OracleParameter("V_ItemID", OracleType.Int16);//店面物业费单价项目编号
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = 2;

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
                return -1;//获取店面物业费单价失败则返回-1
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
        /// <returns>表中符合条件的行</returns>
        public static List<String[]> GetUser1Info(int SearchType, int BN, int RN, String Tel, String Name)
        {
            String Connect_Str = GetOPConnectStr();//获取数据库连接参数字符串
            List<String[]> User1List = new List<String[]>();//待返回的户主列表
            OracleConnection Connect = new OracleConnection(Connect_Str);//实例化连接oracle类

            try
            {
                Connect.Open();//尝试连接数据库

                OracleParameter[] Parm = new OracleParameter[6];//实例化参数列表
                Parm[0] = new OracleParameter("V_BN", OracleType.Int32);//BN参数
                Parm[0].Direction = ParameterDirection.Input;//输入
                Parm[0].Value = BN;
                Parm[1] = new OracleParameter("V_RN", OracleType.Int32);//RN参数
                Parm[1].Direction = ParameterDirection.Input;//输入
                Parm[1].Value = RN;
                Parm[2] = new OracleParameter("V_Tel", OracleType.VarChar);//Tel参数
                Parm[2].Direction = ParameterDirection.Input;//输入
                Parm[2].Value = Tel;
                Parm[3] = new OracleParameter("V_Name", OracleType.VarChar);//Name参数
                Parm[3].Direction = ParameterDirection.Input;//输入
                Parm[3].Value = Name;
                Parm[4] = new OracleParameter("V_Type", OracleType.Int32);//Type参数
                Parm[4].Direction = ParameterDirection.Input;//输入
                Parm[4].Value = SearchType;
                Parm[5] = new OracleParameter("p_cur", OracleType.Cursor);
                Parm[5].Direction = ParameterDirection.Output;//定义引用游标输出参数

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

        }
        /// <summary> 新增户主信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Area">面积</param>
        /// <param name="Tel">电话号码</param>
        /// <param name="Name">姓名</param>
        /// <returns>新增成功返回1，因栋号户号重复新增失败返回-1，其它原因新增失败返回0</returns>
        public static int AddUser1Info(int BN, int RN, Double Area, String Tel, String Name)
        {

        }
        /// <summary> 删除户主信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="AllClear">是否关联删除该户主所有记录</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser1Info(int BN, int RN, Boolean AllClear)
        {

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

        }
        /// <summary> 按栋号户号年份月份查询户主的缴费标志信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <returns>表中符合条件的行</returns>
        public static Boolean[] GetUser1FeeFlag(int BN, int RN, int Year, int Month)
        {

        }

        //户主物业费收费细则处理
        /// <summary> 新增户主物业费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="PCFee">物业费</param>
        /// <param name="Date">缴费日期</param>
        /// <param name="Bonus">优惠金额</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser1PCFee(int BN, int RN, Double PCFee, String Date, Double Bonus)
        {

        }
        /// <summary> 删除户主物业费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser1PCFee(int BN, int RN, String Date)
        {

        }
        /// <summary> 修改户主物业费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="PCFee">物业费</param>
        /// <param name="Date">缴费日期</param>
        /// <param name="Bonus">优惠金额</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1PCFee(int BN, int RN, Double PCFee, String Date, Double Bonus)
        {

        }
        /// <summary> 查询户主物业费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Date">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据栋号户号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetUser1PCFee(int BN, int RN, String Date, int SearchType)
        {

        }

        //户主公摊费收费细则处理
        /// <summary> 新增户主公摊费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="PUBCFee">公摊费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser1PUBCFee(int BN, int RN, Double PUBCFee, String Date)
        {

        }
        /// <summary> 删除户主公摊费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser1PUBCFee(int BN, int RN, String Date)
        {

        }
        /// <summary> 修改户主公摊费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="PUBCFee">公摊费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1PUBCFee(int BN, int RN, Double PUBCFee, String Date)
        {

        }
        /// <summary> 查询户主公摊费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Date">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据栋号户号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <returns>符号条件的行</returns>
        public static List<String[]> GetUser1PUBCFee(int BN, int RN, String Date, int SearchType)
        {

        }

        //户主垃圾转运费收费细则处理
        /// <summary> 新增户主垃圾转运费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="GTFee">垃圾转运费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser1GTFee(int BN, int RN, Double GTFee, String Date)
        {

        }
        /// <summary> 删除户主垃圾转运费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser1GTFee(int BN, int RN, String Date)
        {

        }
        /// <summary> 修改户主垃圾转运费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="GTFee">垃圾转运费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1GTFee(int BN, int RN, Double GTFee, String Date)
        {

        }
        /// <summary> 查询户主垃圾转运费收费细则
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="Date">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据栋号户号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <returns>符号条件的行</returns>
        public static List<String[]> GetUser1GTFee(int BN, int RN, String Date, int SearchType)
        {

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

        }
        /// <summary> 根据车牌号删除
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="AllClear">是否关联删除该车辆所有记录</param>
        /// <returns></returns>
        public static Boolean DeleteUser1Car(String CarID, Boolean AllClear)
        {

        }
        /// <summary> 设置某车辆对应的户主栋号户号
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <param name="CarID">车牌号</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1Car(int BN, int RN, String CarID)
        {

        }
        /// <summary> 根据栋号户号获取某户主的所有车辆信息
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="RN">户号</param>
        /// <returns>表中符合条件的行</returns>
        public static List<String> GetUser1Car(int BN, int RN)
        {

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

        }
        /// <summary> 删除某楼栋统一价格
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser1UFee(int BN)
        {

        }
        /// <summary> 设置某楼栋统一价格
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <param name="UPC">物业费每平米单价</param>
        /// <param name="PUBC">公摊费</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser1UFee(int BN, Double UPC, Double PUBC)
        {

        }
        /// <summary> 获取某楼栋统一价格
        /// </summary>
        /// <param name="BN">栋号</param>
        /// <returns>表中符合条件的行</returns>
        public static List<Double[]> GetUser1UFee(int BN)
        {

        }

        //店面信息处理
        /// <summary> 获取店面信息，根据查询类型对相应参数填null
        /// </summary>
        /// <param name="SearchType">查询类型 0：根据店面编号 1：根据店名 2：根据店主姓名 3：根据电话</param>
        /// <param name="SN">店面编号</param>
        /// <param name="Area">面积</param>
        /// <param name="SName">店面</param>
        /// <param name="Name">店主姓名</param>
        /// <param name="Tel">电话</param>
        /// <returns>表中符合条件的行</returns>
        public static List<String[]> GetUser2Info(int SearchType, int SN, Double Area, String SName, String Name, String Tel)
        {


        }
        /// <summary> 根据店面编号设置店面信息
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Area">面积</param>
        /// <param name="SName">点名</param>
        /// <param name="Name">店主姓名</param>
        /// <param name="Tel">电话</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser2Info(int SN, Double Area, String SName, String Name, String Tel)
        {

        }
        /// <summary> 新增店面信息
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Area">面积</param>
        /// <param name="SName">点名</param>
        /// <param name="Name">店主姓名</param>
        /// <param name="Tel">电话</param>
        /// <returns>新增成功返回1，因店面编号重复新增失败返回-1，其它原因新增失败返回0</returns>
        public static int AddUser2Info(int SN, Double Area, String SName, String Name, String Tel)
        {

        }
        /// <summary> 删除店面信息
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="AllClear">是否关联删除该店面所有记录</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser2Info(int SN, Boolean AllClear)
        {

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

        }
        /// <summary> 按店面编号年份月份查询店面的缴费标志信息
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <returns>表中符合条件的行</returns>
        public static Boolean[] GetUser2FeeFlag(int SN, int Year, int Month)
        {

        }

        //店面物业费收费细则处理
        /// <summary> 新增店面物业费收费细则
        /// </summary>
        /// <param name="SN">栋号</param>
        /// <param name="PCFee">物业费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser2PCFee(int SN, Double PCFee, String Date)
        {

        }
        /// <summary> 删除店面物业费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser2PCFee(int SN, String Date)
        {

        }
        /// <summary> 修改店面物业费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="PCFee">物业费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser2PCFee(int BN, int RN, Double PCFee, String Date, Double Bonus)
        {

        }
        /// <summary> 查询店面物业费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Date">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据店面编号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetUser2PCFee(int BN, String Date, int SearchType)
        {

        }

        //店面电费收费细则处理
        /// <summary> 新增店面电费收费细则
        /// </summary>
        /// <param name="SN">栋号</param>
        /// <param name="ELEFee">电费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser2ELEFee(int SN, Double ELEFee, String Date)
        {

        }
        /// <summary> 删除店面电费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser2ELEFee(int SN, String Date)
        {

        }
        /// <summary> 修改店面电费收费细则
        /// </summary>
        /// <param name="SN">栋号</param>
        /// <param name="ELEFee">电费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser2ELEFee(int SN, Double ELEFee, String Date)
        {

        }
        /// <summary> 查询店面电费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Date">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据店面编号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetUser2ELEFee(int SN, String Date, int SearchType)
        {

        }

        //户主垃圾转运费收费细则处理
        /// <summary> 新增店面垃圾转运费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="GTFee">垃圾转运费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddUser2GTFee(int SN, Double GTFee, String Date)
        {

        }
        /// <summary> 删除店面垃圾转运费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser2GTFee(int SN, String Date)
        {

        }
        /// <summary> 修改店面垃圾转运费收费细则
        /// </summary>
        /// <param name="SN">栋号</param>
        /// <param name="GTFee">垃圾转运费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser2GTFee(int SN, Double GTFee, String Date)
        {

        }
        /// <summary> 查询店面垃圾转运费收费细则
        /// </summary>
        /// <param name="SN">店面编号</param>
        /// <param name="Date">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据店面编号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetUser2GTFee(int SN, String Date, int SearchType)
        {

        }

        //外租车信息处理
        /// <summary> 获取外租车信息，根据查询类型对相应参数填null
        /// </summary>
        /// <param name="SearchType">查询类型 0：根据车牌号 1：根据姓名 2：根据电话</param>
        /// <param name="CarID">车牌号</param>
        /// <param name="Name">姓名</param>
        /// <param name="Tel">电话</param>
        /// <returns>表中符合条件的行</returns>
        public static List<String[]> GetUser3Info(int SearchType, String CarID, String Name, String Tel)
        {


        }
        /// <summary> 根据外租车车牌号设置信息
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Name">姓名</param>
        /// <param name="Tel">电话</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetUser3Info(String CarID, String Name, String Tel)
        {

        }
        /// <summary> 新增外租车信息
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Name">店主姓名</param>
        /// <param name="Tel">电话</param>
        /// <returns>新增成功返回1，因车牌号重复新增失败返回-1，其它原因新增失败返回0</returns>
        public static int AddUser3Info(String CarID, String Name, String Tel)
        {

        }
        /// <summary> 删除外租车信息
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="AllClear">是否关联删除该车牌号所有记录</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteUser3Info(String CarID, Boolean AllClear)
        {

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

        }
        /// <summary> 按车牌号年份月份查询车辆的缴费标志信息
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Year">年份</param>
        /// <param name="Month">月份</param>
        /// <returns>表中符合条件的行</returns>
        public static Boolean GetCarFeeFlag(String CarID, int Year, int Month)
        {

        }

        //车辆收费细则处理
        /// <summary> 新增车辆停车费收费细则
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="CarFee">停车费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>新增成功返回真，否则返回假</returns>
        public static Boolean AddCarFee(String CarID, Double CarFee, String Date)
        {

        }
        /// <summary> 删除车辆停车费收费细则
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>删除成功返回真，否则返回假</returns>
        public static Boolean DeleteCarFee(String CarID, String Date)
        {

        }
        /// <summary> 修改车辆停车费收费细则
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="CarFee">停车费</param>
        /// <param name="Date">缴费日期</param>
        /// <returns>设置成功返回真，否则返回假</returns>
        public static Boolean SetCarFee(String CarID, Double CarFee, String Date)
        {

        }
        /// <summary> 查询车辆停车费收费细则
        /// </summary>
        /// <param name="CarID">车牌号</param>
        /// <param name="Date">缴费日期</param>
        /// <param name="SearchType">查询类型：
        ///     1：根据车牌号查询
        ///     2：根据缴费日期查询
        /// </param>
        /// <returns>符合条件的行</returns>
        public static List<String[]> GetCarFee(String CarID, String Date, int SearchType)
        {

        }
        
    }
}
