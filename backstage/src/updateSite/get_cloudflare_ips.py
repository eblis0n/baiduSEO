import requests
from src.common.method.setOther import setOther


class cloudflare():

    def get_cloudflare(self):
        """
            @Datetime ： 2025/1/2 20:11
            @Author ：eblis
            @Motto：简单描述用途
        """

        # 定义文件路径
        file_path = "/www/baiduSEO/backstage/src/common/document/cloudflare_ips_v4.txt"

        # 获取 Cloudflare IPv4 地址
        ipv4_url = "https://www.cloudflare.com/ips-v4"
        ipv4_response = requests.get(ipv4_url)
        ipv4_addresses = ipv4_response.text.splitlines()

        # 获取 Cloudflare IPv6 地址
        ipv6_url = "https://www.cloudflare.com/ips-v6"
        ipv6_response = requests.get(ipv6_url)
        ipv6_addresses = ipv6_response.text.splitlines()

        # 将地址写入文件，按要求格式化每个IP
        with open(file_path, "w") as file:
            # 写入 IPv4 地址
            for ip in ipv4_addresses:
                file.write(f"set_real_ip_from {ip};\n")
            # 写入 IPv6 地址
            for ip in ipv6_addresses:
                file.write(f"set_real_ip_from {ip};\n")

if __name__ == '__main__':
    cf = cloudflare()
    other = setOther()
    print("更新CF")
    cf.get_cloudflare()
    print("重启NG")
    other.centos_restart_nginx()




