<?php
// 数据库连接配置
$host = '101.32.169.4';
$dbname = 'site_101_32_169_4';
$username = 'root';
$password = 'b690fe5897350b29';

try {
    // 创建 PDO 实例
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 插入数据的 SQL 语句
    $sql = "INSERT INTO mac_custom_type (
        index_title,
        index_keywords,
        index_description,
        one_category_title,
        one_category_keywords,
        one_category_description,
        tow_category_title,
        tow_category_keywords,
        tow_category_description,
        article_category_title,
        article_category_keywords,
        article_category_description,
        play_title,
        play_keywords,
        play_description
    ) VALUES (
        :index_title, :index_keywords, :index_description,
        :one_category_title, :one_category_keywords, :one_category_description,
        :tow_category_title, :tow_category_keywords, :tow_category_description,
        :article_category_title, :article_category_keywords, :article_category_description,
        :play_title, :play_keywords, :play_description
    )";

    // 准备 SQL 语句
    $stmt = $pdo->prepare($sql);

    // 数据映射
    $data = [
        ':index_title' => '丽宫影院-最新高清电影,电视剧大全,短剧全集免费观看-丽宫影院在线追剧网',
        ':index_keywords' => '丽宫影院,高清电影,免费电视剧大全,热门短剧,最新电影,手机在线追剧网',
        ':index_description' => '丽宫影院（{$site_url}）是一个免费手机在线观看大量免费高清电影...',
        ':one_category_title' => '最新{$obj.type_name}免费观看_热门{$obj.type_name}排行榜_第{$param.page}页-丽宫影院',
        ':one_category_keywords' => '最新{$obj.type_name},{$obj.type_name}免费观看,热门{$obj.type_name}',
        ':one_category_description' => '丽宫影院为您提供了各类最新好看的{$obj.type_name}免费观看...',
        ':tow_category_title' => '最新{$obj.type_name}免费观看_热门{$obj.type_name}排行榜-丽宫影院',
        ':tow_category_keywords' => '最新{$obj.type_name},{$obj.type_name}免费观看,热门{$obj.type_name}',
        ':tow_category_description' => '丽宫影院为您提供了各类最新好看的{$obj.type_name}免费观看...',
        ':article_category_title' => '{$obj.type_name}《{影视名}》高清无删减完整版在线观看-丽宫影院',
        ':article_category_keywords' => '{$obj.vod_name},{$obj.vod_name}在线观看,{$obj.vod_name}高清无删减',
        ':article_category_description' => '清理后的内容: strip_tags($obj[\'vod_content\'])',
        ':play_title' => '{$obj.vod_name}-{$obj.type_name}免费在线观看-未删减一线 - 丽宫影院',
        ':play_keywords' => '{$obj.vod_name}全集,{$obj.vod_name}高清完整版在线观看,{$obj.vod_name}免费观看',
        ':play_description' => '清理后的内容: strip_tags($obj[\'vod_content\'])'
    ];

    // 执行 SQL 语句
    $stmt->execute($data);

    echo "数据插入成功！";
} catch (PDOException $e) {
    echo "数据库错误: " . $e->getMessage();
}
?>
