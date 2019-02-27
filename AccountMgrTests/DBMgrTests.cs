using Microsoft.VisualStudio.TestTools.UnitTesting;
using AccountMgr;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;

namespace AccountMgr.Tests
{
    [TestClass()]
    public class DBMgrTests
    {
        [TestMethod()]
        public void CheckOPUserTest()
        {
            DBMgr.CheckOPUser("system", "123");
            Assert.Fail();
        }
    }
}