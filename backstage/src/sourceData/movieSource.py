# -*- coding: utf-8 -*-
import time

import requests
import pymysql
from pypinyin import pinyin, Style
from datetime import datetime

class movieSource():

    def get_movie(slef, url):
        """
            @Datetime ： 2024/12/18 17:19
            @Author ：eblis
            @Motto：简单描述用途
        """

        data = []
        try:
            # 发送 GET 请求
            response = requests.get(url)

            # 检查响应状态码
            if response.status_code == 200:
                # 解析 JSON 数据
                data = response.json()
                print("请求成功")

                return data
            else:
                print(f"请求失败，状态码: {response.status_code}, 响应: {response.text}")
                return data
        except requests.RequestException as e:
            print(f"请求发生错误: {e}")

            return data

    # 数据转换函数

    def parse_vod_list(slef, api_data):
        documents = []
        throwAway = ["两性课堂","伦理片","写真热舞", "影视解说","日本伦理","有声动漫","海外动漫","港台三级","篮球","西方伦理","足球","邵氏电影","韩国伦理","预告片"]
        for item in api_data['list']:
            # 如果 type_name 存在于 throwAway 列表中，则跳过
            if item.get("type_name") in throwAway:
                continue
            #
            # if item.get("type_id_1") == 41:
            #     item["type_id_1"] = 2

            if item["type_name"] in "喜剧片":
                item["type_name"] = "喜剧片"
                item["type_id"] = 7
                item["type_id_1"] = 1

            if item["type_name"] in "动作片":
                item["type_name"] = "动作片"
                item["type_id"] = 6
                item["type_id_1"] = 1
            if item["type_name"] in "爱情片":
                item["type_name"] = "爱情片"
                item["type_id"] = 8
                item["type_id_1"] = 1
            if item["type_name"] in "科幻片":
                item["type_name"] = "科幻片"
                item["type_id"] = 9
                item["type_id_1"] = 1
            if item["type_name"] in "恐怖片,悬疑片,惊悚片":
                item["type_name"] = "恐怖片"
                item["type_id"] = 10
                item["type_id_1"] = 1
            if item["type_name"] in "剧情片":
                item["type_name"] = "剧情片"
                item["type_id"] = 11
                item["type_id_1"] = 1
            if item["type_name"] in "战争片":
                item["type_name"] = "战争片"
                item["type_id"] = 12
                item["type_id_1"] = 1

            if item["type_name"] in "纪录片":
                item["type_name"] = "纪录片"
                item["type_id"] = 21
                item["type_id_1"] = 1

            if item["type_name"] in "犯罪片":
                item["type_name"] = "犯罪片"
                item["type_id"] = 34
                item["type_id_1"] = 1

            if item["type_name"] in "冒险片,奇幻片,灾难片":
                item["type_name"] = "奇幻片"
                item["type_id"] = 35
                item["type_id_1"] = 1


    ############################################################################################################

            if item["type_name"] in "国产剧,大陆剧":
                item["type_name"] = "大陆剧"
                item["type_id"] = 13
                item["type_id_1"] = 2

            if item["type_name"] in "港剧,台剧,香港剧,台湾剧,台剧,港剧,港台剧":
                item["type_name"] = "港台剧"
                item["type_id"] = 14
                item["type_id_1"] = 2

            if item["type_name"] in "欧美剧,美剧":
                item["type_name"] = "美剧"
                item["type_id"] = 15
                item["type_id_1"] = 2


            if item["type_name"] in "韩国剧,韩剧":
                item["type_name"] = "韩国剧"
                item["type_id"] = 22
                item["type_id_1"] = 2

            if item["type_name"] in "泰剧,新马剧,泰国剧":
                item["type_name"] = "泰剧"
                item["type_id"] = 37
                item["type_id_1"] = 2


            if item["type_name"] in "日剧,日本剧":
                item["type_name"] = "日本剧"
                item["type_id"] = 23
                item["type_id_1"] = 2

            if item["type_name"] in "海外剧":
                item["type_name"] = "海外剧"
                item["type_id"] = 24
                item["type_id_1"] = 2

    ############################################################################################################

            if item["type_name"] in "大陆综艺,国产综艺":
                item["type_name"] = "大陆综艺"
                item["type_id"] = 25
                item["type_id_1"] = 3

            if item["type_name"] in "港台综艺,新马泰综艺":
                item["type_name"] = "港台综艺"
                item["type_id"] = 27
                item["type_id_1"] = 3


            if item["type_name"] in "日韩综艺,韩国综艺":
                item["type_name"] = "日韩综艺"
                item["type_id"] = 26
                item["type_id_1"] = 3


            if item["type_name"] in "演唱会":
                item["type_name"] = "演唱会"
                item["type_id"] = 44
                item["type_id_1"] = 3

            if item["type_name"] in "欧美综艺":
                item["type_name"] = "欧美综艺"
                item["type_id"] = 28
                item["type_id_1"] = 3

    ############################################################################################################

            if item["type_name"] in "日韩动漫,日本动漫":
                item["type_name"] = "日韩动漫"
                item["type_id"] = 30
                item["type_id_1"] = 4

            if item["type_name"] in "动画片,国产动漫,动画电影":
                item["type_name"] = "国产动漫"
                item["type_id"] = 29
                item["type_id_1"] = 4

            if item["type_name"] in "新马泰动漫,港台动漫":
                item["type_name"] = "港台动漫"
                item["type_id"] = 42
                item["type_id_1"] = 4

            if item["type_name"] in "欧美动漫":
                item["type_name"] = "欧美动漫"
                item["type_id"] = 31
                item["type_id_1"] = 4

    ############################################################################################################

            if item["type_name"] in "女频恋爱,现代都市":
                item["type_name"] = "现代都市"
                item["type_id"] = 59
                item["type_id_1"] = 5


            if item["type_name"] in "年代穿越":
                item["type_name"] = "年代穿越"
                item["type_id"] = 57
                item["type_id_1"] = 5


            if item["type_name"] in "古装仙侠":
                item["type_name"] = "年代穿越"
                item["type_id"] = 58
                item["type_id_1"] = 5

            if item["type_name"] in "反转爽剧":
                item["type_name"] = "反转爽剧"
                item["type_id"] = 55
                item["type_id_1"] = 5

            if item["type_name"] in "脑洞悬疑":
                item["type_name"] = "脑洞悬疑"
                item["type_id"] = 56
                item["type_id_1"] = 5

    ############################################################################################################

            document = {
                "vod_id": item.get("vod_id", 0),
                "type_id": item.get("type_id", 0),
                "type_id_1": item.get("type_id_1", 0),
                "type_name": item.get("type_name", ""),
                "group_id": item.get("group_id", 0),
                "vod_name": item.get("vod_name", ""),
                "vod_sub": item.get("vod_sub", ""),
                "vod_en": item.get("vod_en", ""),
                "vod_status": item.get("vod_status", 0),
                "vod_letter": item.get("vod_letter", ""),
                "vod_color": item.get("vod_color", ""),
                "vod_tag": item.get("vod_tag", ""),
                "vod_class": item.get("vod_class", ""),
                "vod_pic": item.get("vod_pic", ""),
                "vod_pic_thumb": item.get("vod_pic_thumb", ""),
                "vod_pic_slide": item.get("vod_pic_slide", ""),
                "vod_pic_screenshot": item.get("vod_pic_screenshot", ""),
                "vod_actor": item.get("vod_actor", ""),
                "vod_director": item.get("vod_director", ""),
                "vod_writer": item.get("vod_writer", ""),
                "vod_behind": item.get("vod_behind", ""),
                "vod_blurb": item.get("vod_blurb", ""),
                "vod_remarks": item.get("vod_remarks", ""),
                "vod_pubdate": item.get("vod_pubdate", ""),
                "vod_total": item.get("vod_total", 0),
                "vod_serial": item.get("vod_serial", ""),
                "vod_tv": item.get("vod_tv", ""),
                "vod_weekday": item.get("vod_weekday", ""),
                "vod_area": item.get("vod_area", ""),
                "vod_lang": item.get("vod_lang", ""),
                "vod_year": item.get("vod_year", ""),
                "vod_version": item.get("vod_version", ""),
                "vod_state": item.get("vod_state", ""),
                "vod_author": item.get("vod_author", ""),
                "vod_jumpurl": item.get("vod_jumpurl", ""),
                "vod_tpl": item.get("vod_tpl", ""),
                "vod_tpl_play": item.get("vod_tpl_play", ""),
                "vod_tpl_down": item.get("vod_tpl_down", ""),
                "vod_isend": item.get("vod_isend", 0),
                "vod_lock": item.get("vod_lock", 0),
                "vod_level": item.get("vod_level", 0),
                "vod_copyright": item.get("vod_copyright", 0),
                "vod_points": item.get("vod_points", 0),
                "vod_points_play": item.get("vod_points_play", 0),
                "vod_points_down": item.get("vod_points_down", 0),
                "vod_hits": item.get("vod_hits", 0),
                "vod_hits_day": item.get("vod_hits_day", 0),
                "vod_hits_week": item.get("vod_hits_week", 0),
                "vod_hits_month": item.get("vod_hits_month", 0),
                "vod_duration": item.get("vod_duration", ""),
                "vod_up": item.get("vod_up", 0),
                "vod_down": item.get("vod_down", 0),
                "vod_score": float(item.get("vod_score", 0.0)),
                "vod_score_all": item.get("vod_score_all", 0),
                "vod_score_num": item.get("vod_score_num", 0),
                "vod_time": datetime.strptime(item.get("vod_time", "1970-01-01 00:00:00"), "%Y-%m-%d %H:%M:%S").timestamp(),
                "vod_time_add": item.get("vod_time_add", 0),
                "vod_time_hits": item.get("vod_time_hits", 0),
                "vod_time_make": item.get("vod_time_make", 0),
                "vod_trysee": item.get("vod_trysee", 0),
                "vod_douban_id": item.get("vod_douban_id", 0),
                "vod_douban_score": float(item.get("vod_douban_score", 0.0)),
                "vod_reurl": item.get("vod_reurl", ""),
                "vod_rel_vod": item.get("vod_rel_vod", ""),
                "vod_rel_art": item.get("vod_rel_art", ""),
                "vod_pwd": item.get("vod_pwd", ""),
                "vod_pwd_url": item.get("vod_pwd_url", ""),
                "vod_pwd_play": item.get("vod_pwd_play", ""),
                "vod_pwd_play_url": item.get("vod_pwd_play_url", ""),
                "vod_pwd_down": item.get("vod_pwd_down", ""),
                "vod_pwd_down_url": item.get("vod_pwd_down_url", ""),
                "vod_content": item.get("vod_content", ""),
                "vod_play_from": item.get("vod_play_from", ""),
                "vod_play_server": item.get("vod_play_server", ""),
                "vod_play_note": item.get("vod_play_note", ""),
                "vod_play_url": item.get("vod_play_url", ""),
                "vod_down_from": item.get("vod_down_from", ""),
                "vod_down_server": item.get("vod_down_server", ""),
                "vod_down_note": item.get("vod_down_note", ""),
                "vod_down_url": item.get("vod_down_url", ""),
                "vod_plot": item.get("vod_plot", 0),
                "vod_plot_name": item.get("vod_plot_name", ""),
                "vod_plot_detail": item.get("vod_plot_detail", ""),
            }

            documents.append(document)

        return documents


    def insert_vod_list_to_mysql(slef, api_data, connection):
        """
        插入解析后的数据到 MySQL 数据库，使用传入的现有连接
        """
        try:

            # # 解析 API 数据
            documents = slef.parse_vod_list(api_data)

            #     遍历 documents 过滤集合

            with connection.cursor() as cursor:

                sql = f"""
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
                    VALUES (%(vod_id)s, %(type_id)s, %(type_id_1)s, %(type_name)s, %(group_id)s, %(vod_name)s, %(vod_sub)s, %(vod_en)s,
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
                        %(vod_rel_vod)s, %(vod_rel_art)s, %(vod_pwd)s, %(vod_pwd_url)s, %(vod_pwd_play)s, %(vod_pwd_play_url)s,
                        %(vod_pwd_down)s, %(vod_pwd_down_url)s, %(vod_content)s, %(vod_play_from)s, %(vod_play_server)s,
                        %(vod_play_note)s, %(vod_play_url)s, %(vod_down_from)s, %(vod_down_server)s, %(vod_down_note)s,
                        %(vod_down_url)s, %(vod_plot)s, %(vod_plot_name)s, %(vod_plot_detail)s)
                """

                success_count = 0  # 成功计数
                for document in documents:
                    try:
                        cursor.execute(sql, document)
                        success_count += 1
                    except Exception as item_error:
                        print(f"插入单条记录失败: {item_error}. 跳过该记录.")

                # 提交所有成功的插入操作
                connection.commit()
                print(f"{success_count}/{len(documents)} 条记录入库成功！")

        except Exception as e:
            print(f"整体插入操作失败: {e}")
            connection.rollback()
        # 不在此关闭连接，确保外部关闭






    def convert_to_pinyin(slef, text):
        # 获取文本的拼音
        pinyin_text = pinyin(text, style=Style.NORMAL)  # 使用普通拼音格式
        # 拼接拼音并返回
        return ''.join([word[0] for word in pinyin_text])
    
    
    def del_repeat_data(slef, connection):
        """
            @Datetime ： 2024/12/28 16:46
            @Author ：eblis
            @Motto：简单描述用途
        """
        try:
            with connection.cursor() as cursor:
                sql = f"""DELETE FROM mac_vod
                    WHERE vod_id NOT IN (
                        SELECT keep_id FROM (
                            SELECT 
                                MIN(vod_id) AS keep_id
                            FROM 
                                mac_vod
                            GROUP BY 
                                vod_name
                        ) AS subquery
                    );"""
                # 执行 SQL
                print("开始删除重复数据...")
                cursor.execute(sql)
                affected_rows = cursor.rowcount  # 获取受影响的行数
                connection.commit()  # 提交事务
                print(f"删除重复数据成功，受影响的行数：{affected_rows}")

        except Exception as e:
            print(f"整体插入操作失败: {e}")
            connection.rollback()
    


    def main(slef, db_config, collectionurl, page, maxpage):
        # 连接到 MySQL 数据库（在外部创建连接）
        connection = pymysql.connect(
            cursorclass=pymysql.cursors.DictCursor,  # 使用 DictCursor
            **db_config
        )

        try:
            for i in range(page, int(maxpage)):
                url = f"{collectionurl}?ac=detail&ac=videolist&t=&pg={i}&h=&ids=&wd="
                print(f"第一步 请求接口:{url}")
                api_data = slef.get_movie(url)
                print(api_data)
                # 转为 MySQL 数据并插入
                if api_data != []:
                    slef.insert_vod_list_to_mysql(api_data, connection)
                    time.sleep(3)
            print(f"删除重复数据")
            slef.del_repeat_data(connection)

        except:
            print("请检查终端输出的日志")

        finally:
            # 统一关闭数据库连接
            connection.close()

        print("跑完了")

    ########################################################################################################################

if __name__ == '__main__':

    mov = movieSource()
    # 数据库配置
    db_config = {
        'user': 'root',
        'password': 'fr7ee!hRwzv',
        'host': '43.153.169.98',
        'database': 'movieSource'
    }
    # 资源数据 list
    sourceList = [{
        "name": "最大资源站",
        "url": "https://api.zuidapi.com/api.php/provide/vod/from/zuidam3u8/",
        "page": 1,
        "maxpage": 4416

    },
    {
        "name": "无尽资源网",
        "url": "https://api.wujinapi.me/api.php/provide/vod/",
        "page": 1,
        "maxpage": 3816

    },
    {
        "name": "卧龙资源站资源",
        "url": "https://collect.wolongzy.cc/api.php/provide/vod/",
        "page": 1,
        "maxpage": 2421

    }
    ]
    # 第一步  在浏览器 访问  资源站 list 获取最大页码
    # 采集入口： https://api.wujinapi.me/api.php/provide/vod/?ac=list
    #  采集入口： https://api.zuidapi.com/api.php/provide/vod/from/zuidam3u8/?ac=list
    #  采集入口： https://collect.wolongzy.cc/api.php/provide/vod/?ac=list
    # 第二步： 将 资源站信息，写入sourceList

    # 第三步：执行脚本
    for source in sourceList:
        mov.main(db_config, source["url"],  source["page"], source["maxpage"])



