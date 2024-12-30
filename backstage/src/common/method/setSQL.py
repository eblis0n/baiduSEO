# -*- coding: utf-8 -*-
"""
@Datetime ： 2024/12/30 19:35
@Author ： eblis
@File ：setSQL.py
@IDE ：PyCharm
@Motto：ABC(Always Be Coding)
"""
import os
import sys

base_dr = str(os.path.dirname(os.path.dirname(os.path.dirname(__file__))))
bae_idr = base_dr.replace('\\', '/')
sys.path.append(bae_idr)
import pymysql

class setSQL():

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
    
    def select_type_rand_sql(self, db_config_slave, database_name):
        """
            @Datetime ： 2024/12/31 01:22
            @Author ：eblis
            @Motto：简单描述用途
        """
        db_config_slave["database"] = database_name

        try:
            # 连接到数据库
            connection = pymysql.connect(cursorclass=pymysql.cursors.DictCursor,**db_config_slave)

            with connection.cursor() as cursor:
                # SQL 插入语句
                sql = """
                            SELECT `type_id`, `type_pid` FROM `mac_type` WHERE  type_mid = 2 ORDER BY RAND() LIMIT 1;
                        """

                cursor.execute(sql)
                samples = cursor.fetchall()
                return samples

        except pymysql.err.OperationalError as e:
            print(f"数据库连接失败：{e}")
            raise
    

    def single_insert_vod_to_B(self, db_config_slave, random_documents, database_name):
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
        
    

    def batch_insert_art_sql(self, db_config_slave, insertDatas, database_name, batch_size=1000):
        """
        批量插入数据到数据库中的art表。
        支持大批量数据的分批插入。

        Args:
            db_config_slave (dict): 数据库连接配置。
            insertDatas (list of dict): 要插入的记录列表。
            database_name (str): 数据库名称。
            batch_size (int): 每批次插入的数据条数，默认 1000。
        """
        db_config_slave["database"] = database_name

        print(f"需要写入的是：{db_config_slave}")

        try:
            # 连接到数据库
            connection_art = pymysql.connect(**db_config_slave)

            with connection_art.cursor() as cursor:
                # SQL 插入语句
                sql = """
                    INSERT INTO art (
                        type_id, type_id_1, group_id, art_name, art_sub, art_en, art_status, art_letter,
                        art_color, art_from, art_author, art_tag, art_class, art_pic, art_pic_thumb, art_pic_slide,
                        art_pic_screenshot, art_blurb, art_remarks, art_jumpurl, art_tpl, art_level, art_lock,
                        art_points, art_points_detail, art_up, art_down, art_hits, art_hits_day, art_hits_week,
                        art_hits_month, art_time, art_time_add, art_time_hits, art_time_make, art_score, art_score_all,
                        art_score_num, art_rel_art, art_rel_vod, art_pwd, art_pwd_url, art_title, art_note, art_content
                    ) VALUES (
                        %(type_id)s, %(type_id_1)s, %(group_id)s, %(art_name)s, %(art_sub)s, %(art_en)s, %(art_status)s, %(art_letter)s,
                        %(art_color)s, %(art_from)s, %(art_author)s, %(art_tag)s, %(art_class)s, %(art_pic)s, %(art_pic_thumb)s, %(art_pic_slide)s,
                        %(art_pic_screenshot)s, %(art_blurb)s, %(art_remarks)s, %(art_jumpurl)s, %(art_tpl)s, %(art_level)s, %(art_lock)s,
                        %(art_points)s, %(art_points_detail)s, %(art_up)s, %(art_down)s, %(art_hits)s, %(art_hits_day)s, %(art_hits_week)s,
                        %(art_hits_month)s, %(art_time)s, %(art_time_add)s, %(art_time_hits)s, %(art_time_make)s, %(art_score)s, %(art_score_all)s,
                        %(art_score_num)s, %(art_rel_art)s, %(art_rel_vod)s, %(art_pwd)s, %(art_pwd_url)s, %(art_title)s, %(art_note)s, %(art_content)s
                    )
                """
                # 分批插入
                for i in range(0, len(insertDatas), batch_size):
                    batch = insertDatas[i:i + batch_size]
                    cursor.executemany(sql, batch)
                    connection_art.commit()
                    print(f"art成功插入 {len(batch)} 条记录，已处理到第 {i + len(batch)} 条记录")

        except pymysql.err.OperationalError as e:
            print(f"数据库连接失败：{e}")
            raise


    def batch_insert_comment_sql(self, db_config_slave, insertDatas, database_name, batch_size=1000):
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
            connection_comment = pymysql.connect(**db_config_slave)

            with connection_comment.cursor() as cursor:

                # SQL 插入语句
                sql = """
                        INSERT INTO comments (
                            comment_mid, comment_rid, comment_pid, user_id, 
                            comment_status, comment_name, comment_ip, comment_time, 
                            comment_content, comment_up, comment_down, comment_reply, comment_report
                        ) VALUES (
                            %(comment_mid)s, %(comment_rid)s, %(comment_pid)s, %(user_id)s, 
                            %(comment_status)s, %(comment_name)s, %(comment_ip)s, %(comment_time)s, 
                            %(comment_content)s, %(comment_up)s, %(comment_down)s, %(comment_reply)s, %(comment_report)s
                        )
                        """
                # 分批插入
                for i in range(0, len(insertDatas), batch_size):
                    batch = insertDatas[i:i + batch_size]
                    cursor.executemany(sql, batch)
                    connection_comment.commit()
                    print(f"comment成功插入 {len(batch)} 条记录，已处理到第 {i + len(batch)} 条记录")


        except pymysql.err.OperationalError as e:
            print(f"数据库连接失败：{e}")
            raise

    def batch_insert_gbook_sql(self, db_config_slave, insertDatas, database_name, batch_size=1000):
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
                    print(f"gbook成功插入 {len(batch)} 条记录，已处理到第 {i + len(batch)} 条记录")


        except pymysql.err.OperationalError as e:
            print(f"数据库连接失败：{e}")
            raise

