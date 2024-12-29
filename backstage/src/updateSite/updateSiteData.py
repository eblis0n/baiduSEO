# -*- coding: utf-8 -*-
"""
@Datetime ： 2024/12/25 22:54
@Author ： eblis
@File ：updateSiteData.py
@IDE ：PyCharm
@Motto：ABC(Always Be Coding)
"""
import json
import os
import random
import re
import sys
import time
from datetime import datetime, timedelta

import requests

base_dr = str(os.path.dirname(os.path.dirname(os.path.dirname(__file__))))
bae_idr = base_dr.replace('\\', '/')
sys.path.append(bae_idr)

import openai
import pymysql
from backstage.src.updateSite.googleOnline.google_online_excel_public import googleOnlinePublic

class updateSiteData():
    def __init__(self):

        self.public = googleOnlinePublic()

    # def main(self, ):
    #     """
    #         @Datetime ： 2024/12/28 20:38
    #         @Author ：eblis
    #         @Motto：简单描述用途
    #     """
    #     sheet =  self.run_vertical(target_url, sheetTab)


    def les_go(self, sheet, groupName, witch, AIbase, db_config_slave, db_config_master):
        # 获取表格的所有行
        rows = sheet.get_all_values()
        # print(rows,rows)
        for row in rows[2:]:
            if row[0] in groupName:
                continue
            else:
                print(f"有{len(row) - 1 } 项需要执行")
                database_name = self.sanitize_mysql_name(row[0])
                for i in range(1, len(row)):
                    if row[i] != "" or row[i] != "0":
                        if i == 1:
                            print(f"随机从 主库mac_vod 表中读取的{row[i]},添加到 目标库;")
                            istrue = True
                            while istrue:
                                videoDatas = self.fetch_fixed_data_from_master(db_config_master, int(row[i]))
                                failed_count = self.insert_data_to_B(db_config_slave, videoDatas, database_name)
                                if failed_count <= 0:
                                    istrue = False
                        elif i == 2:
                            print(f"在目标库 mac_comment 中 添加  指定视频数量的 1条评论；")
                            videoDatas = self.fetch_fixed_data_from_master(db_config_master, int(row[i]))
                            for video in videoDatas:
                                data = {
                                    "comment_mid": int(video["vod_id"]),
                                    "comment_rid": 0,
                                    "comment_pid": 0,
                                    "user_id": int(random.randint(1, 9999)),
                                    "comment_status": 1,
                                    "comment_name": "匿名",
                                    "comment_ip": f"{self.generate_china_ip()}",
                                    "comment_time": int(self.generate_random_timestamp()),
                                    "comment_content": f"{self.witchdatas(witch, AIbase, prompt)}",
                                    "comment_up": 0,
                                    "comment_down": 0,
                                    "comment_reply": 0,
                                    "comment_report": 0
                                }

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
                                    "gbook_ip": f"{self.generate_china_ip()}",
                                    "gbook_time": int(self.generate_random_timestamp()),
                                    "gbook_reply_time": 0,
                                    "gbook_content": f"{self.witchdatas(witch, AIbase, prompt)}",
                                    "gbook_reply": ""
                                }

                                gbookInsert.append(data)
                            self.gbook_insert_sql(db_config_slave, gbookInsert, database_name)
                        elif i == 4:
                            print(f"随机栏目 发布文章数量")
                        return row[i]


    def sanitize_mysql_name(slef, name: str) -> str:
        """
        将不符合 MySQL 命名规则的特殊字符替换为下划线 '_'

        :param name: 输入字符串
        :return: 转换后的符合 MySQL 规则的字符串
        """
        # 替换以数字开头的情况
        if name[0].isdigit():
            name = f"site_{name}"

        # 替换不符合规则的字符为 "_"
        sanitized_name = re.sub(r'[^a-zA-Z0-9_]', '_', name)

        # 如果出现连续的 "_"，替换为单个 "_"
        sanitized_name = re.sub(r'_+', '_', sanitized_name)

        # 如果以 "_" 结尾，去掉结尾的 "_"
        sanitized_name = sanitized_name.rstrip('_')

        return sanitized_name


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


    def generate_china_ip(self):
        """随机生成一个中国段的 IPv4 地址"""
        china_ip_ranges = [
            (607649792, 608174079),  # 36.56.0.0 - 36.63.255.255
            (1038614528, 1039007743),  # 61.232.0.0 - 61.237.255.255
            (1783627776, 1784676351),  # 106.80.0.0 - 106.95.255.255
            (2035023872, 2035154943),  # 121.76.0.0 - 121.77.255.255
            (2078801920, 2079064063),  # 123.232.0.0 - 123.235.255.255
        ]
        start, end = random.choice(china_ip_ranges)
        return f"{random.randint(start, end) >> 24}.{(random.randint(start, end) >> 16) & 0xFF}.{(random.randint(start, end) >> 8) & 0xFF}.{random.randint(start, end) & 0xFF}"


    def generate_random_timestamp(self):
        """生成最近一个月内的随机时间戳"""
        now = datetime.now()
        one_month_ago = now - timedelta(days=30)
        random_date = one_month_ago + timedelta(seconds=random.randint(0, int((now - one_month_ago).total_seconds())))
        return int(random_date.timestamp())



    def get_access_token(self, API_KEY, SECRET_KEY):
        """
        使用 AK，SK 生成鉴权签名（Access Token）
        :return: access_token，或是None(如果错误)
        """
        url = "https://aip.baidubce.com/oauth/2.0/token"
        params = {"grant_type": "client_credentials", "client_id": API_KEY, "client_secret": SECRET_KEY}
        return str(requests.post(url, params=params).json().get("access_token"))


    def baidu_ai(self, AIbase, prompt):
        """
            @Datetime ： 2024/12/25 14:13
            @Author ：eblis
            @Motto：简单描述用途
        """
        # print("百度AI，开始干活！")
        url = "https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/ernie_speed?access_token=" + self.get_access_token(
            AIbase["API_KEY"], AIbase["SECRET_KEY"])
        msg = {
            "messages": [
                {
                    "role": "user",
                    "content": f"{prompt}"
                }
            ]
        }
        payload = json.dumps(msg, ensure_ascii=False)
        headers = {
            'Content-Type': 'application/json'
        }
        try:
            response = requests.request("POST", url, headers=headers, data=payload.encode("utf-8"))
            content = response.json()
            return content["result"]
        except Exception as e:
            print(f"百度ai 处理异常: {e}")
            return None


    def open_ai(self, AIbase, prompt, max_retries=5, timeout=60, wait_time=10):


        if AIbase["url"] != "":
            try:
                print("使用新的openai")
                openai.api_base = f'{AIbase["url"]}'
            except Exception as e:
                print(f"openai.api_base失败：{e}")
        else:
            print("使用旧的 ")

        # 设置OpenAI API密钥
        openai.api_key = AIbase["key"]

        retries = 0
        while retries < max_retries:
            try:
                print(f'正在使用{AIbase["model"]} 对 {prompt} 进行训练！！')
                response = openai.ChatCompletion.create(
                    model=f'{AIbase["model"]}',
                    messages=[
                        {"role": "user",
                         "content": prompt
                         }
                    ],
                    max_tokens=2000,
                    n=1,
                    temperature=0.7,
                    timeout=timeout,
                )
                generated_text = response['choices'][0]['message']['content'].strip()
                print(f" ai执行结果：{generated_text}")
                # article = self.insert_article(generated_text)
                if "I am sorry" in generated_text:
                    print("生成失败，使用原文")
                    return None
                else:
                    return generated_text

            except Exception as e:
                print(f"e:{e}")
                print(f"达到速率限制，等待 {wait_time} 秒后重试")
                time.sleep(wait_time)
                retries += 1
        return None



    def witchdatas(self, witch, AIbase, prompt):
        """
            @Datetime ： 2024/12/28 00:52
            @Author ：eblis
            @Motto：简单描述用途
        """

        if witch == "open":
            content = self.open_ai(AIbase, prompt)
        else:

            content = self.baidu_ai(AIbase, prompt)
        return content



    def fetch_fixed_data_from_master(self, db_config_master, sample_size=10):
        try:
            # 建立数据库连接
            with pymysql.connect(
                    cursorclass=pymysql.cursors.DictCursor,
                    **db_config_master
            ) as connection_master:
                try:
                    with connection_master.cursor() as cursor:
                        query = f""" SELECT *  FROM mac_vod ORDER BY RAND() LIMIT {sample_size};"""
                        cursor.execute(query)
                        samples = cursor.fetchall()
                        return samples
                except Exception as e:
                    print(f"数据错误：{e}")
                    connection_master.rollback()
                    raise
        except pymysql.err.OperationalError as e:
            print(f"数据库连接失败：{e}")
            raise


    def insert_data_to_B(self, db_config_slave, random_documents, database_name):
        """
        批量插入数据到数据库B中的mac_vod表。
        支持大批量数据的分批插入。

        Args:
            db_config_slave (dict): 数据库连接配置。
            documents (list of dict): 要插入的记录列表。
            database_name (str): 数据库名称。
            batch_size (int): 每批次插入的数据条数，默认 1000。
        """
        db_config_slave["database"] = database_name

        failed_count = 0  # 成功计数
        try:
            # 连接到数据库
            connection_B = pymysql.connect(**db_config_slave)

            with connection_B.cursor() as cursor:
                # SQL 插入语句
                sql = """
                    INSERT IGNORE INTO `mac_vod` (
                    `vod_id`, `type_id`, `type_id_1`, `type_name`, `group_id`, `vod_name`, `vod_sub`, `vod_en`, `vod_status`, `vod_letter`,
                    `vod_color`, `vod_tag`, `vod_class`, `vod_pic`, `vod_pic_thumb`, `vod_pic_slide`, vod_pic_screenshot,
                    `vod_actor`, `vod_director`, `vod_writer`, `vod_behind`, `vod_blurb`, `vod_remarks`, `vod_pubdate`,
                    `vod_total`, `vod_serial`, `vod_tv`, `vod_weekday`, `vod_area`, `vod_lang`, `vod_year`, `vod_version`,
                    `vod_state`, `vod_author`, `vod_jumpurl`, `vod_tpl`, `vod_tpl_play`, `vod_tpl_down`, `vod_isend`, `vod_lock`,
                    `vod_level`, `vod_copyright`, `vod_points`, `vod_points_play`, `vod_points_down`, `vod_hits`, `vod_hits_day`,
                    `vod_hits_week`, `vod_hits_month`, `vod_duration`, `vod_up`, `vod_down`, `vod_score`, `vod_score_all`,
                    `vod_score_num`, `vod_time`, `vod_time_add`, `vod_time_hits`, `vod_time_make`, `vod_trysee`, `vod_douban_id`,
                    `vod_douban_score`, `vod_reurl`, `vod_rel_vod`, `vod_rel_art`, `vod_pwd`, `vod_pwd_url`, `vod_pwd_play`,
                    `vod_pwd_play_url`, `vod_pwd_down`, `vod_pwd_down_url`, `vod_content`, `vod_play_from`, `vod_play_server`,
                    `vod_play_note`, `vod_play_url`, `vod_down_from`, `vod_down_server`, `vod_down_note`, `vod_down_url`,
                    `vod_plot`, `vod_plot_name`, `vod_plot_detail`)
                    VALUES (
                        %(vod_id)s, %(type_id)s, %(type_id_1)s, %(type_name)s, %(group_id)s, %(vod_name)s, %(vod_sub)s, %(vod_en)s,
                        %(vod_status)s, %(vod_letter)s, %(vod_color)s, %(vod_tag)s, %(vod_class)s, %(vod_pic)s,
                        %(vod_pic_thumb)s, %(vod_pic_slide)s, %(vod_pic_screenshot)s, %(vod_actor)s, %(vod_director)s,
                        %(vod_writer)s, %(vod_behind)s, %(vod_blurb)s, %(vod_remarks)s, %(vod_pubdate)s,
                        %(vod_total)s, %(vod_serial)s, %(vod_tv)s, %(vod_weekday)s, %(vod_area)s, %(vod_lang)s,
                        %(vod_year)s, %(vod_version)s, %(vod_state)s, %(vod_author)s, %(vod_jumpurl)s, %(vod_tpl)s,
                        %(vod_tpl_play)s, %(vod_tpl_down)s, %(vod_isend)s, %(vod_lock)s, %(vod_level)s, %(vod_copyright)s,
                        %(vod_points)s, %(vod_points_play)s, %(vod_points_down)s, %(vod_hits)s, %(vod_hits_day)s,
                        %(vod_hits_week)s, %(vod_hits_month)s, %(vod_duration)s, %(vod_up)s, %(vod_down)s, %(vod_score)s,
                        %(vod_score_all)s, %(vod_score_num)s, %(vod_time)s, %(vod_time_add)s, %(vod_time_hits)s,
                        %(vod_time_make)s, %(vod_trysee)s, %(vod_douban_id)s, %(vod_douban_score)s, %(vod_reurl)s,
                        %(vod_rel_vod)s, %(vod_rel_art)s, %(vod_pwd)s, %(vod_pwd_url)s, %(vod_pwd_play)s,
                        %(vod_pwd_play_url)s, %(vod_pwd_down)s, %(vod_pwd_down_url)s, %(vod_content)s, %(vod_play_from)s,
                        %(vod_play_server)s, %(vod_play_note)s, %(vod_play_url)s, %(vod_down_from)s, %(vod_down_server)s,
                        %(vod_down_note)s, %(vod_down_url)s, %(vod_plot)s, %(vod_plot_name)s, %(vod_plot_detail)s
                    )
                """

                for document in random_documents:
                    try:
                        cursor.execute(sql, document)

                    except Exception as item_error:
                        failed_count += 1
                        print(f"插入单条记录失败: {item_error}. 跳过该记录.")
                # 提交所有成功的插入操作
                connection_B.commit()
                return failed_count


        except pymysql.err.OperationalError as e:
            print(f"数据库连接失败：{e}")
            raise







    def gbook_insert_sql(self, db_config_slave, insertDatas, database_name, batch_size=1000):
        """
        批量插入数据到数据库B中的mac_vod表。
        支持大批量数据的分批插入。

        Args:
            db_config_slave (dict): 数据库连接配置。
            documents (list of dict): 要插入的记录列表。
            database_name (str): 数据库名称。
            batch_size (int): 每批次插入的数据条数，默认 1000。
        """
        db_config_slave["database"] = database_name

        print(f"需要写入的是：{db_config_slave}")

        try:
            # 连接到数据库
            connection_gbook = pymysql.connect(**db_config_slave)
            try:
                with connection_gbook.cursor() as cursor:

                    # SQL 插入语句
                    sql = """
                        INSERT  INTO `gbook` (
                       `gbook_rid`, `user_id`, `gbook_status`, `gbook_name`, `gbook_ip`, `gbook_time`, `gbook_reply_time`, `gbook_content`, `gbook_reply`)
                        VALUES (
                            %(gbook_rid)s, %(user_id)s, %(gbook_status)s, %(gbook_name)s,  INET_ATON(%(gbook_ip)s), %(gbook_time)s, %(gbook_reply_time)s, %(gbook_content)s, %(gbook_reply)s)
                    """
                    # 分批插入
                    for i in range(0, len(insertDatas), batch_size):
                        batch = insertDatas[i:i + batch_size]
                        cursor.executemany(sql, batch)
                        connection_gbook.commit()
                        print(f"成功插入 {len(batch)} 条记录，已处理到第 {i + len(batch)} 条记录")

            except Exception as e:
                print(f"插入数据时发生错误：{e}")
                connection_gbook.rollback()
                raise

        except pymysql.err.OperationalError as e:
            print(f"数据库连接失败：{e}")
            raise




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
