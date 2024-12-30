import pandas as pd
import json
import random
from datetime import datetime

# 文件路径
domain_file = '/Users/eblis/project/shanQ/ProFilePro/baiduSEO/backstage/src/movie/jsonfile/domains.txt'
tdk_file = '/Users/eblis/project/shanQ/ProFilePro/baiduSEO/backstage/src/movie/jsonfile/tdk.xlsx'
output_file = '/Users/eblis/project/shanQ/ProFilePro/baiduSEO/backstage/src/movie/output.json'
brand_file = '/Users/eblis/project/shanQ/ProFilePro/baiduSEO/backstage/src/movie/jsonfile/brand.txt'

# 获取当前年份
current_year = datetime.now().year

# 读取域名和备案号
def load_domains_with_icp(file_path):
    domains_with_icp = []
    with open(file_path, 'r', encoding='utf-8') as file:
        for line in file:
            line = line.strip()
            if not line:
                continue
            parts = line.split('@', 1)  # 按 `@` 分割，最多分割一次
            domain = parts[0]
            icp = parts[1] if len(parts) > 1 else ""  # 如果没有备案号，值为空
            domains_with_icp.append((domain, icp))
    return domains_with_icp

# 读取品牌词列表
def load_brands(file_path):
    with open(file_path, 'r', encoding='utf-8') as file:
        return [line.strip() for line in file if line.strip()]

# 替换规则
def replace_placeholders(content, brand):
    content = content.replace("$brand", brand)
    content = content.replace("{当前年份}", str(current_year))
    return content

# 生成 JSON 数据
def generate_json(domains_with_icp, tdk_data, brands):
    json_data = {}
    used_brands = set()  # 用于确保品牌词不重复
    
    for domain, icp in domains_with_icp:
        if not brands:
            print("品牌词列表为空，请检查品牌文件！")
            break
        
        # 随机选择一个未使用的品牌词
        brand = None
        while brands:
            potential_brand = random.choice(brands)
            if potential_brand not in used_brands:
                brand = potential_brand
                used_brands.add(brand)
                break
        
        if not brand:
            print(f"没有足够的品牌词供 {domain} 使用！")
            continue
        
        domain_data = {}
        # 添加固定键值
        domain_data["$site_url"] = domain
        domain_data["$site_icp"] = icp
        domain_data["$brand_name"] = brand
        
        # 生成 TDK 数据
        for column in tdk_data.columns:
            values = tdk_data[column].dropna().tolist()
            if values:  # 确保有数据可用
                value = random.choice(values)
                replaced_value = replace_placeholders(value, brand)  # 替换占位符
                # 如果是 $index_description，进行 $site_url 替换
                if column == "$index_description":
                    replaced_value = replaced_value.replace("$site_url", domain)
                domain_data[column] = replaced_value
        
        json_data[domain] = domain_data
    
    return json_data

# 主程序
def main():
    # 加载域名和备案号
    domains_with_icp = load_domains_with_icp(domain_file)
    if not domains_with_icp:
        print("域名列表为空，请检查 domain.txt 文件")
        return
    
    # 加载 TDK 数据
    tdk_data = pd.read_excel(tdk_file)
    if tdk_data.empty:
        print("TDK 表格为空，请检查 tdk.xlsx 文件")
        return
    
    # 加载品牌词
    brands = load_brands(brand_file)
    if not brands:
        print("品牌词列表为空，请检查 1.txt 文件")
        return
    
    # 生成 JSON 数据
    json_data = generate_json(domains_with_icp, tdk_data, brands)
    
    # 保存到文件
    with open(output_file, 'w', encoding='utf-8') as file:
        json.dump(json_data, file, ensure_ascii=False, indent=4)
    
    print(f"JSON 文件已生成并保存到 {output_file}")

# 执行脚本
if __name__ == "__main__":
    main()
