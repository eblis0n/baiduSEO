# -*- coding: utf-8 -*-
"""
@Datetime ： 2024/12/31 19:28
@Author ： eblis
@File ：cf_clear_cache.py
@IDE ：PyCharm
@Motto：ABC(Always Be Coding)
"""
import os
import sys

base_dr = str(os.path.dirname(os.path.dirname(os.path.dirname(__file__))))
bae_idr = base_dr.replace('\\', '/')
sys.path.append(bae_idr)

from cloudflare import Cloudflare

client = Cloudflare(
    api_email="gusaomeishi@gmail.com",
    api_key="250ba93007f73387fb4579bfe704d60dba2b3",
)

domainL = ['cnjinglei.com', 'njrhzs.com', 'gqjob.com', 'hualungroup.com', 'tjyttl.com', 'xinzhicai.com', '99flora.com', 'lfloushi.com', 'sdhonggu.com', 'mingdustone.com', 'fuyuanzhiye.com', 'hzbilun.com', 'shqianneng.com', 'tongli180.com', 'ipower-tek.com', 'zhixinnet.com', 'led66.com', 'hbmlm.com', 'ups999.com', 'bingo-t.com', 'leimengmofenji.com', 'am-video.cn']


# 列出所有区域并打印 `zone_id`
zones = client.zones.list()
# print("zones",zones)
for zone in zones:
    # 提取 ID 和域名
    zone_id = zone.id
    zone_name = zone.name
    if zone_name in domainL:
        print(f"Zone ID: {zone_id}")
        print(f"Domain Name: {zone_name}")
        try:
            response = client.cache.purge(
                zone_id=f"{zone_id}",
                purge_everything=True,
            )
            print(response.id)
        except:
            continue
print("执行完")

