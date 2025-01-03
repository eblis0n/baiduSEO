# -*- coding: utf-8 -*-
"""
@Datetime ： 2024/12/18 21:51
@Author ： eblis
@File ：website.py
@IDE ：PyCharm
@Motto：ABC(Always Be Coding)
"""
import json
import os
import sys
import time
from datetime import datetime
import requests

base_dr = str(os.path.dirname(os.path.dirname(os.path.dirname(__file__))))
bae_idr = base_dr.replace('\\', '/')
sys.path.append(bae_idr)

import subprocess
import shutil
import re
import openai
import pymysql
import random
from concurrent.futures import ThreadPoolExecutor, as_completed
from src.logoDesign.logo import logo


class website():

    def run(slef, font_path, img_folder, database_name, source_folder, destination_path, db_config_master, db_config_slave,
            sql_file_path, database_php_path, database_newdict, maccms_php_path, seo_floder, seo_dict, ng_path,
            well_known_path,  source_ng_path, source_ng_name, pseudo_path, pseudo_file_name, ng_newdict, random_documents):
        """
            @Datetime ： 2024/12/19 15:25
            @Author ：eblis
            @Motto：简单描述用途
        """
        print("第1步：复制模版 并改名")
        slef.copy_file(source_folder, destination_path)

        print(f"修改admin.php 文件名")
        domain = seo_dict['$site_url']

        # 使用 '.' 拆分字符串
        parts = domain.split('.')
        old_name = f"{destination_path}/yzlseoadmin.php"
        new_name = f"{destination_path}/{parts[0]}_admin.php"
        try:

            os.rename(old_name, new_name)
            print(f"文件已重命名：{old_name} -> {new_name}")
        except FileNotFoundError:
            print(f"错误：找不到文件 {old_name}")

        print("第2步：生成新的logo")
        lo = logo()

        output_folder = f"{destination_path}/statics/img/"
        output_upload_folder = f"{destination_path}/upload/site/20240114-1/"
        lo.generate_logo_with_image(font_path, img_folder, output_folder, output_upload_folder,
                                    text=f"{seo_dict['$brand_name']}",
                                    url=f"www.{seo_dict['$site_url']}".upper(), spacing=5, scale_factor=0.8,
                                    target_size=(801, 180)  # 目标尺寸
                                    )

        print("第3步：新建数据库")
        # 将数据库命名转

        slef.create_mysql_database(db_config_slave, database_name)

        print("第4步:执行sql 文件 生成 对应表")
        slef.execute_sql_file(db_config_slave, sql_file_path, database_name)

        print("第5步：修改 application/database.php")
        slef.revise_default(database_php_path, database_newdict)

        print("第6步：修改 application/extra/maccms.php")
        slef.revise_default(maccms_php_path, seo_dict)

        print("第7步：遍历模版目录  ../template/stui_tpl2/html/seo 替换对应变量")
        slef.process_directory(seo_floder, seo_dict)

        print("第8步：迁移并修改 /www/server/panel/vhost/nginx 配置文件夹")
        slef.copy_file(source_ng_path, ng_path, source_file_name=source_ng_name)

        with open(well_known_path, 'w+', encoding='utf-8') as file:
            file.write("")

        print("第9步：迁移并修改 /www/server/panel/vhost/rewrite/ 配置文件夹")
        slef.copy_file(source_ng_path, pseudo_path, pseudo_file_name)
        slef.revise_default(ng_path, ng_newdict)

        print("确保日志能顺利写入，手动新建日志目录")
        log_directory = f"/www/wwwlogs/rizhi/{ng_newdict['$site']}"
        os.makedirs(log_directory, exist_ok=True)
        slef.chmod_777(log_directory)


        print(f"第10步：从主影片库迁移分类到 站点")

        slef.insert_type_to_B(db_config_master, db_config_slave, database_name)
        time.sleep(5)

        print(f"第11步：从主影片库迁移{len(random_documents)} 条数据")
        slef.insert_data_to_B(db_config_slave, random_documents, database_name)

        print(f"第12步：修改文件夹权限chmod 777")
        slef.chmod_777(destination_path)




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

    def read_json(slef, json_path):
        """
            @Datetime ： 2024/12/19 16:02
            @Author ：eblis
            @Motto：简单描述用途
        """
        with open(json_path, 'r', encoding='utf-8') as file:
            json_data = file.read()
        return json_data

    @staticmethod
    def baota_restart_nginx():
        try:
            # 使用 subprocess 执行重启命令
            result = subprocess.run(['/etc/init.d/nginx', 'restart'], check=True, text=True, capture_output=True)
            print("Nginx 服务已成功重启")
            print("输出:", result.stdout)
        except subprocess.CalledProcessError as e:
            print(f"重启 Nginx 服务时出错: {e}")
            print("错误信息:", e.stderr)

    def centos_restart_nginx(slef):
        try:
            # 使用 subprocess 执行 systemctl 命令重启 Nginx 服务
            result = subprocess.run(['systemctl', 'restart', 'nginx'], check=True, text=True, capture_output=True)
            print("Nginx 服务已成功重启")
            print("输出:", result.stdout)
        except subprocess.CalledProcessError as e:
            print(f"重启 Nginx 服务时出错: {e}")
            print("错误信息:", e.stderr)
        except FileNotFoundError:
            print("无法找到 systemctl 命令，请确认它是否安装并可用。")

    def copy_file(slef, source_folder, destination_path, source_file_name=None):
        """
            @Datetime ： 2024/12/19 14:53
            @Author ：eblis
            @Motto：简单描述用途
        """
        if source_file_name is None:
            # 复制文件夹
            try:
                shutil.copytree(source_folder, destination_path)
                print(f"文件夹 {source_folder} 已成功复制到 {destination_path}")
            except Exception as e:
                print(f"复制文件夹时发生错误: {e}")
        else:
            source_file = f"{source_folder}/{source_file_name}"

            try:
                shutil.copy(source_file, destination_path)
                print(f"文件 {source_file} 已成功复制到 {destination_path}")
            except Exception as e:
                print(f"复制文件时发生错误: {e}")

    def create_mysql_database(self, db_config_slave, database_name):
        """
        创建一个 MySQL 数据库。
        """
        try:
            # 使用传入的配置连接到 MySQL
            connection_create = pymysql.connect(**db_config_slave)

            if connection_create.open:
                cursor = connection_create.cursor()

                # 创建数据库的 SQL 语句
                create_database_query = f"CREATE DATABASE {database_name};"
                cursor.execute(create_database_query)
                print(f"数据库 '{database_name}' 创建成功!")
                cursor.close()
                connection_create.close()
                return True
            else:
                print("连接失败")
                return False
        except pymysql.MySQLError as e:
            print(f"连接 MySQL 时发生错误: {e}")
            return False

    def execute_sql_file(self, db_config_slave, sql_file_path, database_name):
        """
        执行 SQL 文件中的所有 SQL 命令。
        """

        # 打开并读取 SQL 文件
        with open(sql_file_path, 'r', encoding='utf-8') as file:
            sql_commands = file.read()

        db_config_slave["database"] = database_name

        try:
            # 使用 pymysql 连接到 MySQL
            connection_create = pymysql.connect(**db_config_slave)

            if connection_create.open:
                cursor = connection_create.cursor()

                # 执行 SQL 文件中的所有 SQL 命令
                for command in sql_commands.split(';'):
                    if command.strip():  # 跳过空命令
                        cursor.execute(command)

                # 提交事务
                connection_create.commit()
                print(f"SQL 文件 '{sql_file_path}' 执行成功!")

                cursor.close()
                connection_create.close()
                return True
            else:
                print("连接失败")
                return False

        except pymysql.MySQLError as e:
            print(f"执行 SQL 时发生错误: {e}")
            return False

    def process_directory(slef, seo_floder, replacements):
        """遍历目录中的 HTML 文件并替换内容"""
        for root, _, files in os.walk(seo_floder):
            for file in files:
                if file.endswith('.html'):
                    # file_path1 = os.path.join(root, file)
                    # print("file_path1",file_path1)
                    file_path = f"{seo_floder}/{file}"
                    slef.revise_default(file_path, replacements)

    def revise_default(slef, file_path, newdict):
        """
            @Datetime ： 2024/12/19 15:11
            @Author ：eblis
            @Motto：简单描述用途
        """
        try:
            # 读取文件内容
            with open(file_path, 'r', encoding='utf-8') as file:
                file_data = file.read()

            # 遍历 newdict 并替换内容
            for key, value in newdict.items():
                # 将 $site 替换为实际的值, 确保替换全局出现的该变量
                file_data = re.sub(rf"{re.escape(key)}", f"{value}", file_data)

            # 写回修改后的内容
            with open(file_path, 'w', encoding='utf-8') as file:
                file.write(file_data)

            print(f"文件 {file_path} 修改成功！")

        except FileNotFoundError:
            print(f"错误: 文件 {file_path} 未找到！")
        except Exception as e:
            print(f"修改配置文件时发生错误: {e}")

    def ai_go(self, witch, AIbase, prompt):
        """
            @Datetime ： 2024/12/25 14:14
            @Author ：eblis
            @Motto：简单描述用途
        """
        if witch == "open":
            print(f"本次选用:{witch}")
            datas = self.open_ai(AIbase, prompt)
            return datas
        elif witch == "baidu":
            print(f"本次选用:{witch}")
            datas = self.baidu_ai(AIbase, prompt)
            return datas
        else:
            return None

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
        print("百度AI，开始干活！")

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

    def process_batch_baidu(self, baiduAIbase, documents, batch_size=100):
        success_documents = []
        failed_documents = []
        for i in range(0, len(documents), batch_size):
            batch = documents[i:i + batch_size]
            print(f"处理第 {i + 1} 到 {i + len(batch)} 个视频数据（批次）")
            with ThreadPoolExecutor(max_workers=batch_size) as executor:
                future_to_document = {
                    executor.submit(
                        self.baidu_ai,
                        baiduAIbase,
                        f"你是一个优秀的运营，请为 《{doc['vod_name']}》 这视频 写一份至少500字的简介"
                    ): doc for doc in batch
                }
                for future in as_completed(future_to_document):
                    document = future_to_document[future]  # 获取当前线程对应的文档
                    # print(f"获取当前线程对应的文档:{document}")
                    try:
                        vod_content = future.result()  # 获取 AI 生成的内容
                        # print(f"处理结果：{vod_content}")
                        if vod_content:
                            document['vod_content'] = vod_content  # 更新内容到原文档
                            success_documents.append(document)
                            print(f"文档 {document['vod_name']} 已处理完成")
                        else:

                            failed_documents.append(document)
                            print(f"文档 {document['vod_name']} 未生成有效内容")
                    except Exception as e:
                        print(f"处理文档 {document['vod_name']} 时发生错误: {e}")
                        failed_documents.append(document)

        return success_documents, failed_documents

    def batchGO(self, baiduAIbase, documents, all_success, max_retries):
        """
        批量处理任务，最大重试 max_retries 次
        """
        retry_count = 0  # 初始化重试计数

        while retry_count <= max_retries:
            print(f"开始第 {retry_count + 1} 次处理任务...")

            # 调用批量处理函数
            success_documents, failed_documents = self.process_batch_baidu(baiduAIbase, documents)
            all_success.extend(success_documents)

            # 计算失败率
            failure_rate = len(failed_documents) / len(documents)
            print(f"当前失败率: {failure_rate:.2%}")

            if failure_rate <= 0.05:
                print("失败率低于 5%，任务处理完成。")
                all_success.extend(failed_documents)
                break

            retry_count += 1

            if retry_count > max_retries:
                print("已达到最大重试次数，任务处理结束。")
                all_success.extend(failed_documents)
                break
            # 更新处理的文档列表为失败的文档
            documents = failed_documents

            # 暂停后继续重试
            print(f"失败率超过 5%，将在 2 秒后重新尝试...")
            time.sleep(2)

        return all_success

    def fetch_fixed_data_from_master(self, db_config_master, sample_size=100):
        all_samples = []
        try:
            # 建立数据库连接
            with pymysql.connect(
                    cursorclass=pymysql.cursors.DictCursor,
                    **db_config_master
            ) as connection_master:
                try:
                    with connection_master.cursor() as cursor:
                        # 读取所有非 0 的分类
                        cursor.execute(
                            f"SELECT `type_id`, `type_name`, `type_pid` FROM `mac_type` WHERE `type_pid` != 0;"
                        )
                        sub_categories = cursor.fetchall()
                        print(f"{len(sub_categories)} 个分类需要处理")

                        # 根据 sample_size 提取对应电影最大数
                        for categorie in sub_categories:
                            # 打印当前分类的 id
                            print(
                                f"type_id: {categorie['type_id']} , type_name: {categorie['type_name']}， 数量：{sample_size}")
                            query = f"""
                                        SELECT * 
                                        FROM mac_vod 
                                        WHERE type_id = {categorie["type_id"]} 
                                        ORDER BY RAND()
                                        LIMIT {sample_size};
                                      """
                            cursor.execute(query)
                            samples = cursor.fetchall()
                            all_samples.extend(samples)

                except Exception as e:
                    print(f"数据错误：{e}")
                    connection_master.rollback()
                    raise
                return all_samples  # 确保在所有操作完成后再返回结果

        except pymysql.err.OperationalError as e:
            print(f"数据库连接失败：{e}")
            raise

    def insert_type_to_B(self, db_config_master, db_config_slave, database_name):
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

        try:
            # 连接到 A 库
            connection_A = pymysql.connect(**db_config_master)
            # 连接到数据库
            connection_B = pymysql.connect(**db_config_slave)
            try:
                with connection_A.cursor() as cursor_A:
                    cursor_A.execute("SELECT * FROM mac_type")
                    rows = cursor_A.fetchall()
                with connection_B.cursor() as cursor_B:
                    # SQL 插入语句
                    insert_query = """
                        INSERT INTO `mac_type` (
                            `type_id`, `type_name`, `type_en`, `type_sort`, `type_mid`, `type_pid`, 
                            `type_status`, `type_tpl`, `type_tpl_list`, `type_tpl_detail`, `type_tpl_play`, 
                            `type_tpl_down`, `type_key`, `type_des`, `type_title`, `type_union`, `type_extend`, 
                            `type_logo`, `type_pic`, `type_jumpurl`
                        ) 
                        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s,%s, %s, %s, %s, %s)
                    """
                    cursor_B.executemany(insert_query, rows)
                    connection_B.commit()
                    print(f"成功导入 {len(rows)} 条数据到 B 表")

            except Exception as e:
                print(f"插入数据时发生错误：{e}")
                connection_B.rollback()
                raise
            finally:
                connection_B.close()
                print("数据库连接已关闭")

        except pymysql.err.OperationalError as e:
            print(f"数据库连接失败：{e}")
            raise

    def copy_database_datas(self, db_config_slave, database_name):
        """
            @Datetime ： 2024/12/25 23:38
            @Author ：eblis
            @Motto：简单描述用途
        """
        db_config_slave["database"] = database_name
        try:
            # 建立数据库连接
            with pymysql.connect(
                    cursorclass=pymysql.cursors.DictCursor,
                    **db_config_slave
            ) as connection:
                try:
                    with connection.cursor() as cursor:
                        query = f"""
                                    SELECT * FROM mac_vod ;
                                    """
                        cursor.execute(query)
                        rows = cursor.fetchall()
                        return rows
                except Exception as e:
                    print(f"数据错误：{e}")
                    connection.rollback()
                    raise

        except pymysql.err.OperationalError as e:
            print(f"数据库连接失败：{e}")
            raise

    def insert_data_to_B(self, db_config_slave, random_documents, database_name, batch_size=1000):
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
            connection_B = pymysql.connect(**db_config_slave)
            try:
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
                    # 分批插入
                    for i in range(0, len(random_documents), batch_size):
                        batch = random_documents[i:i + batch_size]
                        cursor.executemany(sql, batch)
                        connection_B.commit()
                        print(f"成功插入 {len(batch)} 条记录，已处理到第 {i + len(batch)} 条记录")

            except Exception as e:
                print(f"插入数据时发生错误：{e}")
                connection_B.rollback()
                raise
            finally:
                connection_B.close()
                print("数据库连接已关闭")

        except pymysql.err.OperationalError as e:
            print(f"数据库连接失败：{e}")
            raise

    def random_time(self, documents):
        """
            @Datetime ： 2024/12/28 00:21
            @Author ：eblis
            @Motto：简单描述用途
        """
        random_documents = []
        for document in documents:
            # 获取当前时间戳
            current_timestamp = int(time.time())
            # 设置一个范围，假设你想生成的随机时间戳从 2000年到现在
            start_timestamp = int(time.mktime(time.strptime("2023-01-01", "%Y-%m-%d")))
            # 生成随机时间戳
            random_timestamp = random.randint(start_timestamp, current_timestamp)

            random_hits = random.randint(1, 9999)
            document['vod_hits'] = int(random_hits)
            document['vod_hits_day'] = int(random_hits)
            document['vod_hits_week'] = int(random_hits)
            document['vod_hits_month'] = int(random_hits)
            document['vod_time'] = int(random_timestamp)
            document['vod_time_add'] = int(random_timestamp)
            random_documents.append(document)
        return random_documents


    def chmod_777(self, path):
        """
        递归设置路径的权限为777
        :param path: 需要修改权限的文件或目录路径
        """
        try:
            # 检查路径是否存在
            if not os.path.exists(path):
                print(f"路径 {path} 不存在！")
                return

            # 修改目标路径的权限
            os.chmod(path, 0o777)
            print(f"已将 {path} 的权限设置为 777")

            # 如果是目录，递归设置其中所有文件和子目录的权限
            if os.path.isdir(path):
                for root, dirs, files in os.walk(path):
                    for dir_name in dirs:
                        dir_path = os.path.join(root, dir_name)
                        os.chmod(dir_path, 0o777)
                        print(f"已将目录 {dir_path} 的权限设置为 777")
                    for file_name in files:
                        file_path = os.path.join(root, file_name)
                        os.chmod(file_path, 0o777)
                        print(f"已将文件 {file_path} 的权限设置为 777")
        except PermissionError as e:
            print(f"权限错误: {e}")
        except Exception as e:
            print(f"发生错误: {e}")

    def witchdatas(self, witch, new_db_config, databasename):
        """
            @Datetime ： 2024/12/28 00:52
            @Author ：eblis
            @Motto：简单描述用途
        """

        if witch == "nono":
            print("不ai，从指定库 将表数据导入")

            documents = self.copy_database_datas(new_db_config, databasename)

            print(f"从指定库，一共复制了{len(documents)} 条数据")
        else:
            documents = []
            # 提取数量

            sample_size = random.randint(200, 500)

            db_documents = self.fetch_fixed_data_from_master(db_config_master, sample_size)
            print(f"数据提取完毕，一共{len(db_documents)}")
            if witch == "open":
                AIbase = {
                    "url": "https://aip.baidubce.com/rpc/2.0/ai_custom/v1/wenxinworkshop/chat/ernie-4.0-turbo-128k",
                    "key": "sk-11JwEWidCWDj9htFA75cFb369773495c92EcF53470Cd2b69",
                    "model": "gpt-4o-2024-08-06"}

                for idx, document in enumerate(db_documents, start=1):

                    prompt = f"你是一个优秀的运营，请为 《{document['vod_name']}》 这视频 写一份至少500字的简介；要求：1、保持原文所使用的标记语言以及自然语言；"
                    vod_content = self.open_ai(AIbase, prompt)
                    if vod_content is not None:
                        document['vod_content'] = vod_content
                        documents.append(document)

            elif witch == "baidu":
                baiduAIbase = {
                    "API_KEY": "WXKSkg6JMBZrvslzePCHRWS7",
                    "SECRET_KEY": "9C0Zel85BYjRvtPTB1Gwuk1Vfj2kyHmK"}

                ws.batchGO(baiduAIbase, db_documents, documents, 10)
                print(f" baidu一共  有 {len(documents)} 条数据，需要入库")
            else:
                documents = []

        ai_time = datetime.now()
        aied_time = ai_time.strftime("%Y-%m-%d %H:%M:%S")
        print(f"ai/提取数据库花费时间:{aied_time}")

        return documents


if __name__ == '__main__':
    ws = website()
    # 配置数据库A的连接
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
        'password': 'abingou2016'
    }
    # 格式化打印当前时间
    current_time = datetime.now()
    formatted_time = current_time.strftime("%Y-%m-%d %H:%M:%S")
    print(f"脚本开始运行时间:{formatted_time}")

    witch = "nono"
    # 指定数据库信息，
    new_db_config = {
        'host': '127.0.0.1',
        'user': 'root',
        'password': 'abingou2016'
    }
    documents = ws.witchdatas(witch, new_db_config, "bingo_t_com")
    # print(f"本批次数据使用：{documents}")

    # 项目根目录
    pro_folder = "/www"
    # 项目模版目录
    source_folder = f"{pro_folder}/baiduSEO/backstage/src/movie/yszhanqun"

    source_ng_path = "/www/baiduSEO/backstage/src/common/document"
    font_path = f"{pro_folder}/baiduSEO/backstage/src/logoDesign/NotoSansSC-VariableFont_wght.ttf"  # 替换为支持中文的字体路径
    img_folder = f"{pro_folder}/baiduSEO/backstage/src/logoDesign/img/"

    json_path = f"{pro_folder}/baiduSEO/backstage/src/movie/output.json"
    file_datas = ws.read_json(json_path)

    json_datas = json.loads(file_datas)
    print(f"读取配置成功，一共有{len(json_datas)} 个站点需要上线， 开始干活！！！！")

    for idx, (site, data) in enumerate(json_datas.items()):
        print(f"开始构建第 {idx + 1} 个，总: {len(json_datas)}")
        random_documents = ws.random_time(documents)
        # print("random_documents",random_documents)
        database_name = ws.sanitize_mysql_name(f"{site}")
        destination_path = f"{pro_folder}/{database_name}"
        seo_floder = f"{destination_path}/template/stui_tpl2/html/seo"
        sql_file_path = f"{destination_path}/cms_com.sql"
        database_php_path = f"{destination_path}/application/database.php"
        maccms_php_path = f"{destination_path}/application/extra/maccms.php"
        source_ng_name = "stencil_nginx.conf"
        ng_path = f"/usr/local/nginx/conf/vhost/{database_name}.conf"
        well_known_path = f"/usr/local/nginx/conf/vhost/well-known/{database_name}.conf"
        database_newdict = {'$site': f"{site}", '$database': f"{database_name}", '$user': f"{db_config_slave['user']}",
                            '$password': f"{db_config_slave['password']}"}
        ng_newdict = {'$site': f"{site}", '$foldername': f"{database_name}"}
        data['$foldername'] = f"{database_name}"
        seo_dict = data
        pseudo_path = f"/usr/local/nginx/conf/rewrite/{database_name}.conf"
        pseudo_file_name = "site.com.conf"
        ws.run(font_path, img_folder, database_name, source_folder, destination_path, db_config_master, db_config_slave,
               sql_file_path, database_php_path, database_newdict, maccms_php_path, seo_floder, seo_dict, ng_path,
               well_known_path, source_ng_path, source_ng_name, pseudo_path, pseudo_file_name, ng_newdict, random_documents)
    print("重启NG 收工")
    try:
        ws.baota_restart_nginx()
    except:
        ws.centos_restart_nginx()

    always_time = datetime.now()
    siteed_always_time = always_time.strftime("%Y-%m-%d %H:%M:%S")

    print(f"完成:{len(json_datas)}  站点上线 耗时：开始时间：{formatted_time} 结束于：{siteed_always_time},")

