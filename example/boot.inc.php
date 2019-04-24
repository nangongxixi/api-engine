<?php

namespace j\apiSimple\example;

use j\base\Config;

$composerAutoload = [
    __DIR__ . '/../vendor/autoload.php', // in dev repo
    __DIR__ . '/../../../autoload.php', // installed as a composer binary
];

/** @var \Composer\Autoload\ClassLoader $loader */
$loader = null;

foreach ($composerAutoload as $autoload) {
    if (file_exists($autoload)) {
        $loader = require($autoload);
        $vendorPath = realpath(dirname($autoload));
        break;
    }
}
if(!isset($vendorPath)){
    throw new \Exception("Not found autoload.php");
}

$loader->addPsr4(__NAMESPACE__ . '\\', __DIR__);

// config
Config::getInstance()->sets([
    'apiGateway' => 'http://api.x2.cn/api.php?api=%s&charset=%s&',
    'apiNs' => 'j\\apiSimple\\example\\api\\',
    'apiMockDir' => __DIR__ . '/data/'
]);


