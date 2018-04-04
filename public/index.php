<?php
declare(strict_types=1);

require_once '../vendor/autoload.php';
$config = [];
/** @var \GlobIterator $file */
foreach (new \GlobIterator(__DIR__.'/../config/*.config.php', GLOB_BRACE) as $file) {
    $config[] = include $file->getRealPath();
}
$config = array_merge(...$config);
$handler = new \Composer\Satis\Webhook\Handler($config);
$handler->run();