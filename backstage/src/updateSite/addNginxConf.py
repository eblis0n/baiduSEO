# -*- coding: utf-8 -*-
"""
@Datetime ： 2025/1/3 14:35
@Author ： eblis
@File ：addNginxConf.py
@IDE ：PyCharm
@Motto：ABC(Always Be Coding)
"""
import json
import os
import sys

base_dr = str(os.path.dirname(os.path.dirname(os.path.dirname(__file__))))
bae_idr = base_dr.replace('\\', '/')
sys.path.append(bae_idr)

import re
import subprocess
import shutil
from ftplib import FTP
class addNginxConf():



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

    def copy_file( slef, source_folder, destination_path, source_file_name=None):
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


    def read_json(slef, json_path):
        """
            @Datetime ： 2024/12/19 16:02
            @Author ：eblis
            @Motto：简单描述用途
        """
        with open(json_path, 'r', encoding='utf-8') as file:
            json_data = file.read()
        return json_data


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

if __name__ == '__main__':
    add = addNginxConf()
    json_path = "/www/baiduSEO/backstage/src/updateSite/nginx_file/newNG_json.json"
    file_datas = add.read_json(json_path)

    json_datas = json.loads(file_datas)
    source_ng_path = "/www/baiduSEO/backstage/src/common/document"
    destination_path = "/usr/local/nginx/conf/vhost"

    for new in json_datas:
        database_name = add.sanitize_mysql_name(new["Primary"])
        print(f"开始处理 {database_name} 的二级 ")
        for subor in new["subordinate"]:
            newsub = add.sanitize_mysql_name(subor)
            ng_path = f"{destination_path}/{newsub}.conf"
            print("将文件复制")
            add.copy_file(source_ng_path, ng_path, source_file_name="stencil_nginx.conf")
            ng_newdict = {'$site': f"{subor}", '$foldername': f"{database_name}"}
            print("修改数据")
            add.revise_default(ng_path, ng_newdict)
        print(f'{new["Primary"]} 二级生成完了')
    print(f'{len(json_datas)} 处理成功')
    print("重启NG 收工")
    add.centos_restart_nginx()



