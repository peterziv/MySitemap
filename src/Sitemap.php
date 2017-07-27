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

        function genearteXml($list)
        {
            //创建一个新的 DOM文档
            $dom = new \DOMDocument('1.0', 'UTF-8');
            $dom->formatOutput = true;

            //在根节点创建 departs标签
            $urlset = $dom->createElement('urlset');
            $dom->appendChild($urlset);

            $lastmodVal = date("Y-m-d");
            foreach ($list as $url => $val) {
            $this->addUrl($dom, $urlset, $url, $lastmodVal, $val['changefreq'], $val['priority']);
            }

            $dom->save('sitemap.xml');
        }


        public function addUrl($dom, &$urlset, $locVal, $lastmodVal, $freqVal, $priorityVal = '1.0')
        {
            $url = $dom->createElement('url');
            $urlset->appendChild($url);

            $loc = $dom->createElement('loc');
            $url->appendChild($loc);
            $locValue = $dom->createTextNode($locVal);
            $loc->appendChild($locValue);

            $lastmod = $dom->createElement('lastmod');
            $url->appendChild($lastmod);
            $lastmodValue = $dom->createTextNode($lastmodVal);
            $lastmod->appendChild($lastmodValue);

            $freq = $dom->createElement('changefreq');
            $url->appendChild($freq);
            $freqValue = $dom->createTextNode($freqVal);
            $freq->appendChild($freqValue);

            $priority = $dom->createElement('priority');
            $url->appendChild($priority);
            $priorityValue = $dom->createTextNode($priorityVal);
            $priority->appendChild($priorityValue);
        }

    }

}

