<?php
namespace app\index\controller;
use think\Controller;

// Rss 控制器，主要用于生成各种 XML 格式的 RSS 文件
class Rss extends Controller
{
    // 构造函数，设置 HTTP 响应头为 XML 格式
    public function __construct()
    {
        parent::__construct();
        header("Content-Type:text/xml");
    }

    // 默认方法，生成主 URL 的 sitemap 文件
    public function index()
    {
        $content = $this->generateMainUrls(); // 调用主 URL 生成方法
        $this->saveFile($content, 'index.xml'); // 保存到文件
        echo $content; // 输出内容
    }

    // 一键生成所有类型的 sitemap 文件
    public function generateAll()
    {
        $this->sitemap(); // 生成主 URL 的 sitemap
        $this->type(); // 生成分类 URL 的 sitemap
        $this->vod(); // 生成视频 URL 的 sitemap
        $this->art(); // 生成文章 URL 的 sitemap
        $this->extra(); // 生成其他 URL 的 sitemap
        $this->play(); // 生成播放页 URL 的 sitemap
    }

    // 生成主 URL 的 sitemap 文件
    public function sitemap()
    {
        $content = $this->generateMainUrls();
        $this->saveFile($content, 'sitemap.xml');
        echo $content;
    }

    // 生成分类 URL 的 sitemap 文件
    public function type()
    {
        $content = $this->generateTypeUrls();
        $this->saveFile($content, 'type.xml');
        echo $content;
    }

    // 生成视频 URL 的 sitemap 文件
    public function vod()
    {
        ob_clean(); // 清除之前的输出
        $this->generateVodUrls(); // 调用生成视频 URL 的方法
    }
    
    // 生成播放页 URL 的 sitemap 文件
    public function play()
    {
        ob_clean(); // 清除之前的输出
        $this->generatePlayUrls(); // 调用生成播放页 URL 的方法
    }

    // 生成文章 URL 的 sitemap 文件
    public function art()
    {
        $content = $this->generateArtUrls();
        $this->saveFile($content, 'art.xml');
        echo $content;
    }

    // 生成额外 URL 的 sitemap 文件
    public function extra()
    {
        $content = $this->generateExtraUrls();
        $this->saveFile($content, 'extra.xml');
        echo $content;
    }

    // 保存内容到指定文件
    private function saveFile($content, $filename)
    {
        $file = ROOT_PATH . "rss/{$filename}"; // 确定文件路径
        file_put_contents($file, $content); // 写入文件
    }

    // 生成主 URL 的 XML 内容
    private function generateMainUrls()
    {
        ob_start(); // 开始输出缓冲
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // 定义主要菜单页面 URL 和优先级
        $mainUrls = [
            '/' => '1.0',
            '/map' => '0.8',
            '/gbook' => '0.6',
            '/topic' => '0.8',
            '/actor' => '0.7',
            '/role' => '0.7'
        ];

        // 遍历主要菜单页面，生成 XML 节点
        foreach ($mainUrls as $url => $priority) {
            echo "<url>\n";
            echo "<loc>https://" . $_SERVER['HTTP_HOST'] . $url . "</loc>\n";
            echo "<lastmod>" . date('Y-m-d') . "</lastmod>\n";
            echo "<changefreq>daily</changefreq>\n";
            echo "<priority>" . $priority . "</priority>\n";
            echo "</url>\n";
        }

        // 获取分类页面并生成相应的 XML 节点
        $typeList = db('type')->where(['type_status' => 1])->select();
        foreach ($typeList as $type) {
            echo "<url>\n";
            if ($type['type_mid'] == 1) {
                echo "<loc>https://" . $_SERVER['HTTP_HOST'] . "/vodtype/" . $type['type_en'] . "</loc>\n";
            } else if ($type['type_mid'] == 2) {
                echo "<loc>https://" . $_SERVER['HTTP_HOST'] . "/arttype/" . $type['type_en'] . "</loc>\n";
            }
            echo "<lastmod>" . date('Y-m-d') . "</lastmod>\n";
            echo "<changefreq>daily</changefreq>\n";
            echo "<priority>0.8</priority>\n";
            echo "</url>\n";
        }

        echo '</urlset>'; // 结束 XML 标签
        return ob_get_clean(); // 获取缓冲内容并返回
    }

    // 生成视频 URL 的 XML 内容
    private function generateVodUrls()
    {
        $page = 1; // 初始化页码
        $limit = 500; // 每页限制条数
        $vodModel = db('vod');
        $totalCount = $vodModel->where(['vod_status' => 1])->count(); // 获取总记录数
        $totalPages = ceil($totalCount / $limit); // 计算总页数

        for ($page = 1; $page <= $totalPages; $page++) {
            ob_start(); // 开始新的输出缓冲区
            echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            // 分页查询
            $vodList = $vodModel
                ->where(['vod_status' => 1])
                ->order('vod_time desc')
                ->limit(($page - 1) * $limit, $limit) // 计算偏移量
                ->select();

            foreach ($vodList as $vod) { // 遍历生成每个视频的 XML 节点
                echo "<url>\n";
                echo "<loc>https://" . $_SERVER['HTTP_HOST'] . "/voddetail/" . $vod['vod_en'] . "</loc>\n";
                echo "<lastmod>" . date('Y-m-d', $vod['vod_time']) . "</lastmod>\n";
                echo "<changefreq>weekly</changefreq>\n";
                echo "<priority>0.6</priority>\n";
                echo "</url>\n";
            }

            echo '</urlset>';
            $content = ob_get_clean(); // 获取并清空缓冲区内容

            // 根据页码生成文件名
            $filename = $page === 1 ? 'vod.xml' : "vod_{$page}.xml";
            $this->saveFile($content, $filename); // 保存到对应文件
        }
    }

    // 生成文章 URL 的 XML 内容
    private function generateArtUrls()
    {
        ob_start();
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $artList = db('art')
            ->where(['art_status' => 1])
            ->order('art_time desc')
            ->limit(500)
            ->select();

        foreach ($artList as $art) {
            echo "<url>\n";
            echo "<loc>https://" . $_SERVER['HTTP_HOST'] . "/artdetail/" . $art['art_en'] . "</loc>\n";
            echo "<lastmod>" . date('Y-m-d', $art['art_time']) . "</lastmod>\n";
            echo "<changefreq>weekly</changefreq>\n";
            echo "<priority>0.6</priority>\n";
            echo "</url>\n";
        }

        echo '</urlset>';
        return ob_get_clean();
    }
    
    // 生成播放页 URL 的 XML 内容
    private function generatePlayUrls()
    {
        $page = 1; // 初始化页码
        $limit = 500; // 每页限制条数
        $vodModel = db('vod');
        $totalCount = $vodModel->where(['vod_status' => 1])->count(); // 获取总记录数
        $totalPages = ceil($totalCount / $limit); // 计算总页数

        for ($page = 1; $page <= $totalPages; $page++) {
            ob_start(); // 开始新的输出缓冲区
            echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            // 分页查询
            $vodList = $vodModel
                ->where(['vod_status' => 1])
                ->order('vod_time desc')
                ->limit(($page - 1) * $limit, $limit)
                ->select();

            foreach ($vodList as $v) {
                if (!empty($v['vod_play_from'])) {
                    $play_from = explode('$$$', $v['vod_play_from']); // 分隔播放来源
                    $play_url = explode('$$$', $v['vod_play_url']);   // 分隔播放 URL

                    foreach ($play_from as $key => $from) {
                        if (!empty($play_url[$key])) {
                            $urls = explode('#', $play_url[$key]); // 分隔每个播放地址
                            foreach ($urls as $sid => $url) {
                                if (!empty($url)) {
                                    echo "<url>\n";
                                    echo "<loc>https://" . $_SERVER['HTTP_HOST'] . "/vodplay/" . $v['vod_en'] . "-" . ($key + 1) . "-" . ($sid + 1) . ".html</loc>\n";
                                    echo "<lastmod>" . date('Y-m-d', $v['vod_time']) . "</lastmod>\n";
                                    echo "<changefreq>always</changefreq>\n";
                                    echo "<priority>0.6</priority>\n";
                                    echo "</url>\n";
                                }
                            }
                        }
                    }
                }
            }

            echo '</urlset>';
            $content = ob_get_clean();
            $filename = $page === 1 ? 'play.xml' : "play_{$page}.xml"; // 保存分页文件
            $this->saveFile($content, $filename);
        }
    }

    // 生成额外 URL 的 XML 内容（如专题、演员、角色）
    private function generateExtraUrls()
    {
        ob_start();
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // 定义额外表格及其对应的 URL 和优先级
        $tables = [
            'topic' => ['url' => 'topicdetail', 'priority' => '0.7'],
            'actor' => ['url' => 'actordetail', 'priority' => '0.5'],
            'role' => ['url' => 'roledetail', 'priority' => '0.4']
        ];

        // 遍历表格数据并生成 XML 节点
        foreach ($tables as $table => $config) {
            $list = db($table)
                ->where(["{$table}_status" => 1])
                ->order("{$table}_time desc")
                ->select();

            foreach ($list as $item) {
                echo "<url>\n";
                echo "<loc>https://" . $_SERVER['HTTP_HOST'] . "/{$config['url']}/" . $item["{$table}_en"] . "</loc>\n";
                echo "<lastmod>" . date('Y-m-d', $item["{$table}_time"]) . "</lastmod>\n";
                echo "<changefreq>weekly</changefreq>\n";
                echo "<priority>" . $config['priority'] . "</priority>\n";
                echo "</url>\n";
            }
        }

        echo '</urlset>';
        return ob_get_clean();
    }
}

