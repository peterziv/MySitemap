<?php
/**
 * Sitemap Tools loader file
 */
require_once __DIR__ . '/Sitemap.php';

error_reporting(E_ERROR);

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
        echo '[INFO] syntax error! Please use php mysitemap-xxxx.phar domain [--kd]';
        exit(-1);
}
$site = new \ZKit\seo\Sitemap();
$site->run($url, $kd);

