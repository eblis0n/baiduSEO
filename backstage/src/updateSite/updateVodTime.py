# -*- coding: utf-8 -*-
"""
@Datetime ： 2025/1/2 21:55
@Author ： eblis
@File ：updateVodTime.py
@IDE ：PyCharm
@Motto：ABC(Always Be Coding)
"""
import os
import sys

base_dr = str(os.path.dirname(os.path.dirname(os.path.dirname(__file__))))
bae_idr = base_dr.replace('\\', '/')
sys.path.append(bae_idr)
from src.common.method.setSQL import setSQL
sql = setSQL()

db_config_slave = {
        'host': '127.0.0.1',
        'user': 'root',
        'password': 'abingou2016'
    }

vodList = ["cnjinglei_com",
    "fuyuanzhiye_com",
    "gqjob_com",
    "hbmlm_com",
    "hualungroup_com",
    "ipower_tek_com",
    "led66_com",
    "lfloushi_com",
    "mingdustone_com",
    "njrhzs_com",
    "shqianneng_com",
    "site_99flora_com",
    "tongli180_com",
    "xinzhicai_com",
    "zhixinnet_com",
    "kuobifu.cn",
    "kanuolang.com.cn",
    "yunkusou.cn",
    "chifengqiye.cn",
    "baijiaxian.cn",
    "tjyttl.com",
    "sdhonggu.com",
    "hbmlm.com",
    "ups999.com",
    "leimengmofenji.com",
    "1gamesite.com",
    "ofoos.com",
    "fshuana.com",
    "shqzjn.com",
    "hnjhyz.com",
    "7cyyc.cn",
    "caipinshibie.com.cn",
    "bairunjiaxiao.cn",
    "chenhongkang.com.cn",
    "bjjxjw.com",
    "jxzsk.com"
    ]

for vod in vodList:
    updated_rows = sql.update_vod_time(db_config_slave, vod, 30)
    print(f"{vod} 更新了 {updated_rows} 条记录的 `vod_time` 字段。")
