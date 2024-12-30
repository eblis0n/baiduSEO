# -*- coding: utf-8 -*-
"""
@Datetime ： 2024/12/30 19:28
@Author ： eblis
@File ：setOther.py
@IDE ：PyCharm
@Motto：ABC(Always Be Coding)
"""
import os
import re
import sys

base_dr = str(os.path.dirname(os.path.dirname(os.path.dirname(__file__))))
bae_idr = base_dr.replace('\\', '/')
sys.path.append(bae_idr)
import random
from datetime import datetime, timedelta
class setOther():
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


    def generate_random_timestamp(self, num):
        """生成最近一个月内的随机时间戳"""
        now = datetime.now()
        one_month_ago = now - timedelta(days=num)
        random_date = one_month_ago + timedelta(seconds=random.randint(0, int((now - one_month_ago).total_seconds())))
        return int(random_date.timestamp())