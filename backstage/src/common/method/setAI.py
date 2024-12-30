# -*- coding: utf-8 -*-
"""
@Datetime ： 2024/12/30 19:23
@Author ： eblis
@File ：setAI.py
@IDE ：PyCharm
@Motto：ABC(Always Be Coding)
"""
import os
import sys

base_dr = str(os.path.dirname(os.path.dirname(os.path.dirname(__file__))))
bae_idr = base_dr.replace('\\', '/')
sys.path.append(bae_idr)
import requests
import time
import json
import openai

class setAI():

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
