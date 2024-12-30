<?php

namespace app\index\controller;
use think\Controller;

// Rss 控制器，主要用于生成各种 XML 格式的 RSS 文件
class Rss extends Controller
{
    public function __construct()
    {
        parent::__construct();
        header("Content-Type:text/xml");
    }

    public function index()
    {
        $content = $this->generateMainUrls();
        $this->saveFile($content, 'index.xml');
        echo $content;
    }

    public function generateAll()
    {
        $this->sitemap();
        $this->type();
        $this->vod();
        $this->art();
        $this->extra();
        $this->play();
    }

    public function sitemap()
    {
        $content = $this->generateMainUrls();
        $this->saveFile($content, 'sitemap.xml');
        echo $content;
    }

    public function type()
    {
        $content = $this->generateTypeUrls();
        $this->saveFile($content, 'type.xml');
        echo $content;
    }

    public function vod()
    {
        ob_clean();
        $this->generateVodUrls();
    }

    public function play()
    {
        ob_clean();
        $this->generatePlayUrls();
    }

    public function art()
    {
        $content = $this->generateArtUrls();
        $this->saveFile($content, 'art.xml');
        echo $content;
    }

    public function extra()
    {
        $content = $this->generateExtraUrls();
        $this->saveFile($content, 'extra.xml');
        echo $content;
    }

    private function saveFile($content, $filename)
    {
        $file = ROOT_PATH . "rss/{$filename}";
        if (!file_put_contents($file, $content)) {
            throw new \Exception("Failed to write to file: {$file}");
        }
    }

    private function generateMainUrls()
    {
        ob_start();
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // 定义主要菜单页面 URL 和优先级
        $mainUrls = [
            '/' => '1.0',
        ];

        foreach ($mainUrls as $url => $priority) {
            echo "<url>\n";
            echo "<loc>https://" . $_SERVER['HTTP_HOST'] . $url . "</loc>\n";
            echo "<lastmod>" . date('Y-m-d') . "</lastmod>\n";
            echo "<changefreq>daily</changefreq>\n";
            echo "<priority>{$priority}</priority>\n";
            echo "</url>\n";
        }

        // 添加分类 URL
        $typeList = db('type')->where(['type_status' => 1])->select();
        $category_map = config('maccms.category_map');

        foreach ($typeList as $type) {
            echo "<url>\n";
            $type_en = isset($category_map[$type['type_en']]) ? $category_map[$type['type_en']] : $type['type_en'];
            if ($type['type_mid'] == 1) {
                echo "<loc>https://" . $_SERVER['HTTP_HOST'] . "/vodclass/" . $type_en . ".html</loc>\n";
            } else if ($type['type_mid'] == 2) {
                echo "<loc>https://" . $_SERVER['HTTP_HOST'] . "/arttype/" . $type_en . ".html</loc>\n";
            }
            echo "<lastmod>" . date('Y-m-d') . "</lastmod>\n";
            echo "<changefreq>daily</changefreq>\n";
            echo "<priority>0.8</priority>\n";
            echo "</url>\n";
        }

        // 添加 vod.xml 和 play.xml 相关链接
        $this->addGeneratedFileUrls('vod', 500);
        $this->addGeneratedFileUrls('play', 500);

        echo '</urlset>';
        return ob_get_clean();
    }

    private function addGeneratedFileUrls($type, $limit)
    {
        $vodModel = db('vod');
        $totalCount = $vodModel->where(['vod_status' => 1])->count();
        $totalPages = ceil($totalCount / $limit);

        echo "<url>\n";
        echo "<loc>https://" . $_SERVER['HTTP_HOST'] . "/rss/{$type}.xml</loc>\n";
        echo "<lastmod>" . date('Y-m-d') . "</lastmod>\n";
        echo "<changefreq>weekly</changefreq>\n";
        echo "<priority>0.7</priority>\n";
        echo "</url>\n";

        for ($page = 2; $page <= $totalPages; $page++) {
            echo "<url>\n";
            echo "<loc>https://" . $_SERVER['HTTP_HOST'] . "/rss/{$type}_{$page}.xml</loc>\n";
            echo "<lastmod>" . date('Y-m-d') . "</lastmod>\n";
            echo "<changefreq>weekly</changefreq>\n";
            echo "<priority>0.6</priority>\n";
            echo "</url>\n";
        }
    }

    private function generateVodUrls()
    {
        $page = 1;
        $limit = 500;
        $vodModel = db('vod');
        $totalCount = $vodModel->where(['vod_status' => 1])->count();
        $totalPages = ceil($totalCount / $limit);

        for ($page = 1; $page <= $totalPages; $page++) {
            ob_start();
            echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            $vodList = $vodModel
                ->where(['vod_status' => 1])
                ->order('vod_time desc')
                ->limit(($page - 1) * $limit, $limit)
                ->select();

            foreach ($vodList as $vod) {
                // 确保生成正确的路径
                $vod_url = "/voditem/" . $vod['vod_en'] . ".html"; // 修改此行，确保路径为 /voditem/
                echo "<url>\n";
                echo "<loc>https://" . $_SERVER['HTTP_HOST'] . $vod_url . "</loc>\n";
                echo "<lastmod>" . date('Y-m-d', $vod['vod_time']) . "</lastmod>\n";
                echo "<changefreq>weekly</changefreq>\n";
                echo "<priority>0.6</priority>\n";
                echo "</url>\n";
            }

            echo '</urlset>';
            $content = ob_get_clean();
            $filename = $page === 1 ? 'vod.xml' : "vod_{$page}.xml";
            $this->saveFile($content, $filename);
        }
    }



    private function generatePlayUrls()
    {
        $page = 1;
        $limit = 500;
        $vodModel = db('vod');
        $totalCount = $vodModel->where(['vod_status' => 1])->count();
        $totalPages = ceil($totalCount / $limit);

        for ($page = 1; $page <= $totalPages; $page++) {
            ob_start();
            echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
            echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

            $vodList = $vodModel
                ->where(['vod_status' => 1])
                ->order('vod_time desc')
                ->limit(($page - 1) * $limit, $limit)
                ->select();

            foreach ($vodList as $vod) {
                // 确保只有存在播放源时才生成播放链接
                if (!empty($vod['vod_play_from'])) {
                    $play_from = explode('$$$', $vod['vod_play_from']);
                    $play_url = explode('$$$', $vod['vod_play_url']);

                    foreach ($play_from as $key => $from) {
                        if (!empty($play_url[$key])) {
                            // 处理每个播放源的链接
                            $play_url_parts = explode('#', $play_url[$key]);

                            foreach ($play_url_parts as $sid => $url) {
                                if (!empty($url)) {
                                    // 拼接正确的路径，确保使用 /playitem/ 路径
                                    $play_url_final = "/playitem/" . $vod['vod_en'] . "-" . urlencode($from) . ".html";

                                    echo "<url>\n";
                                    echo "<loc>https://" . $_SERVER['HTTP_HOST'] . $play_url_final . "</loc>\n";
                                    echo "<lastmod>" . date('Y-m-d', $vod['vod_time']) . "</lastmod>\n";
                                    echo "<changefreq>weekly</changefreq>\n";
                                    echo "<priority>0.5</priority>\n";
                                    echo "</url>\n";
                                }
                            }
                        }
                    }
                }
            }

            echo '</urlset>';
            $content = ob_get_clean();
            $filename = $page === 1 ? 'play.xml' : "play_{$page}.xml";
            $this->saveFile($content, $filename);
        }
    }


    private function generateExtraUrls()
    {
        ob_start();
        echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        $tables = [
            'topic' => ['url' => 'topicdetail', 'priority' => '0.7'],
            'actor' => ['url' => 'actordetail', 'priority' => '0.5'],
            'role' => ['url' => 'roledetail', 'priority' => '0.4']
        ];

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
                echo "<priority>{$config['priority']}</priority>\n";
                echo "</url>\n";
            }
        }

        echo '</urlset>';
        return ob_get_clean();
    }
}
