<?php
/**
 * MySitemap
 * @license  GNU LESSER GENERAL PUBLIC LICENSE Version 2.1
 * @author peter <peter.ziv@hotmail.com>
 */

namespace ZKit\seo {
    require_once __DIR__ . '/Search.php';

    class Sitemap
    {

        public function run($url)
        {
            $search = new Search();
            $this->genearteXml($search->run($url));
        }

        private function genearteXml($list)
        {
            //创建一个新的 DOM文档
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->formatOutput = true;

            //在根节点创建 departs标签
            $urlset = $dom->createElement('urlset');
            $dom->appendChild($urlset);

            $lastmodVal = date("Y-m-d");
            foreach ($list as $url => $val) {
                $val['loc'] = $url;
                $val['lastmod'] = $lastmodVal;

                $this->addUrl($dom, $urlset, $val);
            }

            $dom->save('sitemap.xml');
        }

        private function addUrl($dom, $urlset, $var = array())
        {
            $url = $dom->createElement('url');
            $urlset->appendChild($url);
            $this->addPriorities($dom, $url, $var);
        }

        private function addPriorities($dom, $node, $var)
        {
            $keys = array('loc', 'lastmod', 'changefreq', 'priority', 'keywords', 'description');
            foreach ($keys as $key) {
                if (array_key_exists($key, $var)) {
                    $this->add($dom, $node, $key, $var[$key]);
                }
            }
        }

        private function add($dom, $node, $key, $value)
        {
            $priority = $dom->createElement($key);
            $priorityValue = $dom->createTextNode($value);
            $priority->appendChild($priorityValue);
            $node->appendChild($priority);
        }

    }

}

