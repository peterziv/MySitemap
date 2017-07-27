<?php
/**
 * Sitemap Tools loader file
 */
require_once __DIR__ . '/Sitemap.php';

error_reporting(E_ERROR);
define('MYSITEMAP_VERSION', '1.0.0');

echo 'MySitemap v' . MYSITEMAP_VERSION . PHP_EOL;
echo 'Copyright (C) 2017 PETER with LGPL-2.1 License' . PHP_EOL;
echo PHP_EOL;

$kd = false;
switch ($argc) {
    case 3:
        if ($argv[2] == '--kd') {
            echo '[INFO] Support keywords and description!' . PHP_EOL;
            $kd = true;
        }
    case 2:
        $url = $argv[1];
        break;
    default:
        echo '[INFO] syntax error! Please use php mysitemap-xxxx.phar domain [--kd]' . PHP_EOL;
        echo PHP_EOL;
        echo 'Usage Command: php mysitemap-' . MYSITEMAP_VERSION . '.phar domain [--kd]' . PHP_EOL;
        echo '[--kd] if you want to get keywords and description in sitemap, plesae add this parameter' . PHP_EOL;
        exit(-1);
}
$site = new \ZKit\seo\Sitemap();
$site->run($url, $kd);

