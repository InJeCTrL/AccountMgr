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
    }
}
