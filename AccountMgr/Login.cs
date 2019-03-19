using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;

namespace AccountMgr
{
    public partial class Login : Form
    {
        /// <summary> 登入初始化
        /// </summary>
        public Login()
        {
            InitializeComponent();
        }
        /// <summary> 验证用户名密码及身份正确性
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void button1_Click(object sender, EventArgs e)
        {
            //验证操作员
            if (radioButton1.Checked == true)
            {
                //验证通过
                if (DBMgr.CheckOPUser(textBox1.Text.ToString(), textBox2.Text.ToString()))
                {
                    
                }
            }
            //验证管理员
            else
            {
                //验证通过
                if (DBMgr.CheckADUser(textBox1.Text.ToString(), textBox2.Text.ToString()))
                {

                }
            }
            MessageBox.Show("验证失败！");
        }

        private void Login_Load(object sender, EventArgs e)
        {

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
    }
}
