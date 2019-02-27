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
    public partial class Form1 : Form
    {
        /// <summary> 主界面初始化
        /// </summary>
        public Form1()
        {
            InitializeComponent();
        }
        /// <summary> 主界面加载时
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void Form1_Load(object sender, EventArgs e)
        {

        }
        /// <summary> 单击设置垃圾转运费按钮
        /// </summary>
        /// <param name="sender"></param>
        /// <param name="e"></param>
        private void button1_Click(object sender, EventArgs e)
        {
            InputGTFee Form_GTFee = new InputGTFee();
            Form_GTFee.ShowDialog();
        }
    }
}
