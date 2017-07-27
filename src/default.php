<?php
/**
 * Sitemap Tools loader file
 */
require_once __DIR__ . '/Sitemap.php';

error_reporting(E_ERROR);

$site = new \ZKit\seo\Sitemap();
$site->run($argv[1]);

