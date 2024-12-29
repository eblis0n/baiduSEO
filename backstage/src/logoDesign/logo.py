from PIL import Image, ImageDraw, ImageFont
import os
import random

class logo():
    def remove_transparent_border(slef, image):
        """移除图片的透明边框"""
        bbox = image.getbbox()  # 获取非透明区域的边界框
        return image.crop(bbox)

    def make_transparent(slef, image, threshold=200):
        """将图片背景变为透明"""
        image = image.convert("RGBA")
        data = image.getdata()

        new_data = []
        for item in data:
            brightness = sum(item[:3]) / 3
            if brightness > threshold:  # 如果亮度高于阈值，则设置为透明
                new_data.append((255, 255, 255, 0))
            else:
                new_data.append(item)

        image.putdata(new_data)
        return image

    def get_random_image(slef, folder_path, file_extension=".png"):
        """从文件夹中随机选择一个指定后缀的文件"""
        files = [f for f in os.listdir(folder_path) if f.endswith(file_extension)]
        if not files:
            raise FileNotFoundError(f"文件夹 {folder_path} 中没有找到任何 {file_extension} 文件")
        return os.path.join(folder_path, random.choice(files))

    def ensure_output_folder_exists(slef, folder_path):
        """确保输出文件夹存在，如果不存在则创建"""
        if not os.path.exists(folder_path):
            os.makedirs(folder_path)

    def draw_bold_text(slef, draw, position, text, font, fill, bold_width=3):
        """通过多次绘制模拟加粗文本"""
        x, y = position
        for dx in range(-bold_width, bold_width + 1):
            for dy in range(-bold_width, bold_width + 1):
                if dx != 0 or dy != 0:  # 避免重复绘制中心点
                    draw.text((x + dx, y + dy), text, font=font, fill=fill)
        # 最后绘制一次中心位置，避免偏移导致的空白
        draw.text((x, y), text, font=font, fill=fill)

    def generate_logo_with_image(slef, font_path, folder_path,  output_folder, output_upload_folder, text="丽宫影院", url="www.example.com", threshold=200,
                                 spacing=10, scale_factor=0.8, target_size=None):
        # 确保输出文件夹存在
        slef.ensure_output_folder_exists(output_folder)

        # 从文件夹中随机选择一张 PNG 图片
        image_path = slef.get_random_image(folder_path)
        print(f"随机选择的图片是：{image_path}")

        # 打开原始图片并处理为透明背景
        image = Image.open(image_path)
        image = slef.make_transparent(image, threshold)
        image = slef.remove_transparent_border(image)

        # 创建文字和 URL 图片并移除透明边框
        font_size = 100
        font = ImageFont.truetype(font_path, font_size)
        text_width, text_height = font.getbbox(text)[2:]
        url_font_size = 40
        url_font = ImageFont.truetype(font_path, url_font_size)
        url_width, url_height = url_font.getbbox(url)[2:]

        # 创建文字图片
        total_width = max(text_width, url_width)
        total_height = text_height + url_height + 10  # 添加间距

        text_image = Image.new("RGBA", (total_width, total_height), (255, 255, 255, 0))
        draw = ImageDraw.Draw(text_image)
        text_color = (12, 192, 223)  # 主文字颜色
        url_color = (0, 0, 0)  # URL 颜色

        # 绘制加粗的主文字
        slef.draw_bold_text(draw, ((total_width - text_width) // 2, 0), text, font=font, fill=text_color, bold_width=2)

        # 绘制加粗的 URL
        slef.draw_bold_text(draw, ((total_width - url_width) // 2, text_height + 10), url, font=url_font, fill=url_color,
                       bold_width=1)

        text_image = slef.remove_transparent_border(text_image)

        # 创建拼接图片
        combined_width = image.width + text_image.width + spacing
        combined_height = max(image.height, text_image.height)

        combined_image = Image.new("RGBA", (combined_width, combined_height), (255, 255, 255, 0))
        combined_image.paste(image, (0, (combined_height - image.height) // 2))  # 图片居中
        combined_image.paste(text_image, (image.width + spacing, (combined_height - text_image.height) // 2))  # 缩小间距

        # 生成等比缩小的图像
        scaled_width = int(combined_image.width * scale_factor)
        scaled_height = int(combined_image.height * scale_factor)
        scaled_image = combined_image.resize((scaled_width, scaled_height), Image.LANCZOS)
        scaled_output_path = os.path.join(output_folder, "logo_f.png")
        scaled_upload_path = os.path.join(output_upload_folder, "logo_f.png")
        scaled_image.save(scaled_output_path, "PNG")
        scaled_image.save(scaled_upload_path, "PNG")
        print(f"等比缩小的 Logo 已保存到 {scaled_output_path}")

        # 生成目标尺寸的图像
        if target_size:
            resized_image = combined_image.resize(target_size, Image.LANCZOS)
            resized_output_path = os.path.join(output_folder, f"logo_min_f.png")
            resized_image.save(resized_output_path, "PNG")
            resized_upload_path = os.path.join(output_upload_folder, f"logo_min_f.png")
            resized_image.save(resized_upload_path, "PNG")
            print(f"目标尺寸的 Logo 已保存到 {resized_output_path}")


if __name__ == '__main__':
    lo = logo()
    # 调用函数生成两种尺寸的 Logo
    font_path = "/backstage/src/movie/NotoSansSC-VariableFont_wght.ttf"  # 替换为支持中文的字体路径
    folder_path = "/backstage/src/documents/img/"
    output_folder = "/Users/eblis/project/shanQ/ProFilePro/baiduSEO/backstage/src/movie/yszhanqun/statics/img/"
    output_upload_folder = "/Users/eblis/project/shanQ/ProFilePro/baiduSEO/backstage/src/movie/yszhanqun/upload/site/20240114-1/"
    lo.generate_logo_with_image(font_path, folder_path,  output_folder, output_upload_folder, text="琪琪电影", url="www.gqjob_com".upper(), spacing=5, scale_factor=0.8,   target_size=(150, 50)  # 目标尺寸
    )
