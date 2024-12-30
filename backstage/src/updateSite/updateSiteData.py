# -*- coding: utf-8 -*-
"""
@Datetime ： 2024/12/25 22:54
@Author ： eblis
@File ：updateSiteData.py
@IDE ：PyCharm
@Motto：ABC(Always Be Coding)
"""
import os
import random
import sys


base_dr = str(os.path.dirname(os.path.dirname(os.path.dirname(__file__))))
bae_idr = base_dr.replace('\\', '/')
sys.path.append(bae_idr)


import pymysql
from backstage.src.updateSite.googleOnline.google_online_excel_public import googleOnlinePublic
from backstage.src.common.method.setAI import setAI
from backstage.src.common.method.setOther import setOther
from backstage.src.common.method.setSQL import setSQL
class updateSiteData():
    def __init__(self):
        self.aigo = setAI()
        self.other = setOther()
        self.sql = setSQL()

        self.public = googleOnlinePublic()



    def les_go(self, sheet, groupName, witch, AIbase, db_config_slave, db_config_master):
        # 获取表格的所有行
        rows = sheet.get_all_values()
        # print(rows,rows)
        for row in rows[2:]:
            if row[0] in groupName:
                continue
            else:
                print(f"有{len(row) - 1 } 项需要执行")
                database_name = self.other.sanitize_mysql_name(row[0])
                for i in range(1, len(row)):
                    if row[i] != "" or row[i] != "0":
                        if i == 1:
                            print(f"随机从 主库mac_vod 表中读取的{row[i]},添加到 目标库;")
                            istrue = True
                            while istrue:
                                videoDatas = self.sql.fetch_fixed_data_from_master(db_config_master, int(row[i]))
                                failed_count = self.sql.single_insert_vod_to_B(db_config_slave, videoDatas, database_name)
                                if failed_count <= 0:
                                    istrue = False
                        elif i == 2:
                            print(f"在目标库 mac_comment 中 添加  指定视频数量的 1条评论；")
                            videoDatas = self.sql.fetch_fixed_data_from_master(db_config_master, int(row[i]))
                            commentInsert = []
                            for video in videoDatas:
                                prompt = f"你是一个资深的 视频点评人，请对 《{videoDatas['vod_name']}》 这视频 写 200字左右的点评；要求：1、内容必须为正面，不能出现粗言秽语；"
                                data = {
                                    "comment_mid": int(video["vod_id"]),
                                    "comment_rid": 0,
                                    "comment_pid": 0,
                                    "user_id": int(random.randint(1, 9999)),
                                    "comment_status": 1,
                                    "comment_name": "匿名",
                                    "comment_ip": f"{self.other.generate_china_ip()}",
                                    "comment_time": int(self.other.generate_random_timestamp(30)),
                                    "comment_content": f"{self.witchdatas(witch, AIbase, prompt)}",
                                    "comment_up": 0,
                                    "comment_down": 0,
                                    "comment_reply": 0,
                                    "comment_report": 0
                                }
                                commentInsert.append(data)
                            self.sql.batch_insert_comment_sql(db_config_slave, commentInsert, database_name)

                        elif i == 3:

                            print(f"在目标库 ai生成 的留言数量")
                            gbookInsert = []
                            prompt = f"你是一个喜欢通过视频网站观看视频的资深用户，请写一段150字左右的 期望或赞扬或改进 的 留言；要求：1、内容必须为正面，不能出现粗言秽语；"
                            for _ in range(int(row[i])):
                                data = {
                                    "gbook_rid": 0,
                                    "user_id": int(random.randint(1, 9999)),
                                    "gbook_status": 1,
                                    "gbook_name": "匿名",
                                    "gbook_ip": f"{self.other.generate_china_ip()}",
                                    "gbook_time": int(self.other.generate_random_timestamp(30)),
                                    "gbook_reply_time": 0,
                                    "gbook_content": f"{self.witchdatas(witch, AIbase, prompt)}",
                                    "gbook_reply": ""
                                }

                                gbookInsert.append(data)
                            self.sql.batch_insert_comment_sql(db_config_slave, gbookInsert, database_name)
                        elif i == 4:
                            print(f"随机栏目 发布文章数量")
                        return row[i]




    def run_vertical(self, target_url, sheetTab):
        """
            @Datetime ： 2024/9/20 01:02
            @Author ：eblis
            @Motto：遍历指定表格
        """

        workbook = self.public.google_online_excel_workbook(target_url)
        sheet = workbook.worksheet(sheetTab)
        # print("sheet", sheet)
        return sheet


    def witchdatas(self, witch, AIbase, prompt):
        """
            @Datetime ： 2024/12/28 00:52
            @Author ：eblis
            @Motto：简单描述用途
        """

        if witch == "open":
            content = self.aigo.open_ai(AIbase, prompt)
        else:

            content = self.aigo.baidu_ai(AIbase, prompt)
        return content




if __name__ == '__main__':
    usite = updateSiteData()

    db_config_master = {
        'host': '43.153.169.98',
        'user': 'root',
        'password': 'fr7ee!hRwzv',
        'database': 'movieSource',
        'charset': 'utf8mb4'
    }

    db_config_slave = {
        'host': '127.0.0.1',
        'user': 'root',
        'password': '49d89f00a11177b9'
    }
    baiduAIbase = {
        "API_KEY": "WXKSkg6JMBZrvslzePCHRWS7",
        "SECRET_KEY": "9C0Zel85BYjRvtPTB1Gwuk1Vfj2kyHmK"}

    AIbase = {
        "url": "https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/ernie-4.0-turbo-128k",
        "key": "sk-11JwEWidCWDj9htFA75cFb369773495c92EcF53470Cd2b69",
        "model": "gpt-4o-2024-08-06"}

    target_url = "https://docs.google.com/spreadsheets/d/1IGn5exrPILEUgUqTHdexW5EJdNlkmwAbdgo_1LG2ZBE/edit?gid=2139886459#gid=2139886459"
    sheetTab = "影视站"
    groupName = ["宝塔107.148.0.119"]

    sheet = usite.run_vertical(target_url, sheetTab)
    row = usite.les_go(db_config_slave, sheet, groupName)
    print(row)
