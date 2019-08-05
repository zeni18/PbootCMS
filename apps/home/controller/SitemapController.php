<?php
/**
 * @copyright (C)2016-2099 Hnaoyun Inc.
 * @author XingMeng
 * @email hnxsh@foxmail.com
 * @date 2018年7月15日
 *  生成sitemap文件
 */
namespace app\home\controller;

use core\basic\Controller;
use app\home\model\SitemapModel;
use core\basic\Url;

class SitemapController extends Controller
{

    protected $model;

    public function __construct()
    {
        $this->model = new SitemapModel();
    }

    public function index()
    {
        header("Content-type:text/xml;charset=utf-8");
        $str = '<?xml version="1.0" encoding="UTF-8" ?>' . "\n" . '<urlset>';
        $str .= $this->makeNode('', date('Y-m-d'), 1); // 根目录
        
        $url_break_char = $this->config('url_break_char') ?: '_';
        
        $sorts = $this->model->getSorts();
        foreach ($sorts as $value) {
            if ($value->outlink) {
                continue;
            } elseif ($value->type == 1) {
                $value->urlname = $value->urlname ?: 'list';
                if ($value->filename) {
                    $link = Url::home('/home/Index/' . $value->filename);
                } else {
                    $link = Url::home('/home/Index/' . $value->urlname . $url_break_char . $value->scode);
                }
                $str .= $this->makeNode($link, date('Y-m-d'), 0.8);
            } else {
                $value->urlname = $value->urlname ?: 'list';
                if ($value->filename) {
                    $link = Url::home('home/Index/' . $value->filename);
                } else {
                    $link = Url::home('home/Index/' . $value->urlname . $url_break_char . $value->scode);
                }
                $str .= $this->makeNode($link, date('Y-m-d'), 0.8);
                $contents = $this->model->getSortContent($value->scode);
                foreach ($contents as $value2) {
                    if ($value2->outlink) { // 外链
                        continue;
                    } else {
                        $value2->urlname = $value2->urlname ?: 'list';
                        if ($value2->filename && $value2->sortfilename) {
                            $link = Url::home('home/Index/' . $value2->sortfilename . '/' . $value2->filename, true);
                        } elseif ($value2->sortfilename) {
                            $link = Url::home('home/Index/' . $value2->sortfilename . '/' . $value2->id, true);
                        } elseif ($value2->contentfilename) {
                            $link = Url::home('home/Index/' . $value2->urlname . $url_break_char . $value2->scode . '/' . $value2->filename, true);
                        } else {
                            $link = Url::home('home/Index/' . $value2->urlname . $url_break_char . $value2->scode . '/' . $value2->id, true);
                        }
                    }
                    $str .= $this->makeNode($link, date('Y-m-d'), 0.8);
                }
            }
        }
        echo $str . "\n</urlset>";
    }

    // 生成结点信息
    private function makeNode($link, $date, $priority = 0.6)
    {
        $node = '
<url>
    <loc>' . get_http_url() . $link . '</loc>
    <lastmod>' . $date . '</lastmod>
    <changefreq>daily</changefreq>
    <priority>' . $priority . '</priority>
</url>';
        return $node;
    }
}