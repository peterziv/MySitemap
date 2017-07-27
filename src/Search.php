<?php
/**
 * MySitemap
 * @license  GNU LESSER GENERAL PUBLIC LICENSE Version 2.1
 * @author peter <peter.ziv@hotmail.com>
 */

namespace ZKit\seo;

require_once __DIR__ . '/idna/idna_convert.class.php';

/**
 * This class is to Search the sub-url under the specified url.
 * @version 1.0
 */
class Search
{

    private $domain = null;
    private $host = null;
    private $list = array();
    public $supportKeyWordsAndDescription = false;

    public function run($url)
    {
        $tmp = parse_url($url);
        if (false == $tmp || !array_key_exists('host', $tmp)) {
            echo '[ERROR] Please input the right home URL for making sitemap!' . PHP_EOL;
            return array();
        }
        libxml_use_internal_errors(true);
        $this->host = $this->verifyHost($tmp['host']);
        $this->domain = $url;
        $this->lookup($url);
        return $this->list;
    }

    private function emailCheck($email)
    {
        $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,3}(\\.[a-z]{2})?)$/i";
        return preg_match($pattern, $email);
    }

    public function lookup($url = '', $deep = 0)
    {
        $html = file_get_contents($url);
        if (false === $html) {
            echo '[WARNING] ' . $url . " is invalid url " . PHP_EOL;
            return;
        }
        if (!$this->addList($url, $deep)) {
            return;
        }

        $dom = new \DOMDocument();
        $dom->loadHTML($html);
        // grab all the on the page
        $xpath = new \DOMXPath($dom);
        $this->findParam($xpath, $url);
        $hrefs = $xpath->evaluate("/html/body//a");
        for ($i = 0; $i < $hrefs->length; $i++) {
            $href = $hrefs->item($i);
            $url = $href->getAttribute('href');
            $this->handleUrl($url, $deep + 1);
        }
    }

    private function handleUrl($url, $deep)
    {
        $urlTemp = parse_url($url);
        if (false === $urlTemp) {
            return;
        }

        if (array_key_exists('host', $urlTemp) && $this->verifyHost($urlTemp['host']) !== $this->host) {
            echo '[WARNING] ' . $url . ' is external links' . PHP_EOL;
            return;
        }

        if (!array_key_exists('path', $urlTemp)) {
            echo '[WARNING] path is not existing! - ' . $url . PHP_EOL;
            return;
        }
        $path = $urlTemp['path'];
        $this->handlePath($path, $url, $deep);
    }

    private function handlePath($path, $url, $deep)
    {
        switch ($path) {
            case '/':
            case '.':
            case '';
            case '?':
            case '#':
                echo '[WARNING] ' . $url . ' is itself!' . PHP_EOL;
                break;
            default:
                if (!$this->emailCheck($path)) {
                    $this->lookup($this->domain . $path, $deep);
                } else {
                    echo '[WARNING] This is one email address.' . $path . PHP_EOL;
                }
        }
    }

    private function verifyHost($host)
    {
        $IDN = new \idna_convert();
        return $IDN->encode($host);
    }

    private function addList($url, $deep)
    {
        if (array_key_exists($url, $this->list)) {
            echo '[WARNING] ' . $url . ' is existing!' . PHP_EOL;
            $newPrority = $this->calcPriority($deep);
            if ($this->list[$url]['priority'] < $newPrority) {
                echo '[INFO] Upgrade the priority from ' . $this->list[$url]['priority'] . ' to ' . $newPrority . ' for ' . $url . PHP_EOL;
                $this->list[$url]['priority'] = $newPrority;
            }
            return false;
        }
        echo '[INFO] Add new url ' . $url . PHP_EOL;
        $this->list[$url] = array(
            'priority' => $this->calcPriority($deep),
            'changefreq' => 'Always',
        );
        return true;
    }

    private function findParam($xpath, $url)
    {
        if (!$this->supportKeyWordsAndDescription) {
            return;
        }
        $metas = $xpath->evaluate("/html/head/meta");
        for ($i = 0; $i < $metas->length; $i++) {
            $meta = $metas->item($i);
            switch ($meta->getAttribute('name')) {
                case 'keywords':
                case 'description':
                    $this->addParam($url, $meta->getAttribute('name'), $meta->getAttribute('content'));
                    break;
                default:
                    break;
            }
        }
    }

    private function addParam($url, $key, $value)
    {
        if (array_key_exists($url, $this->list)) {
            echo '[INFO] Set the ' . $key . ' as ' . $value . ' for ' . $url . PHP_EOL;
            $this->list[$url][$key] = $value;
        }
    }

    private function calcPriority($deep = 0)
    {
        return $deep < 5 ? (1 - $deep * 0.2) : 0.1;
    }

}
