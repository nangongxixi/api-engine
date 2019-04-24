<?php

use j\apiSimple\App;
use j\base\Config;
use j\log\File as Log;
use j\tool\Strings;

require __DIR__ . '/../boot.inc.php';

//$_GET = Strings::toGbk($_GET);
//$_REQUEST = Strings::toGbk($_REQUEST);
//$_POST = Strings::toGbk($_POST);

$log = new Log(__DIR__ . "/api.log");
$config = Config::getInstance();

$app = new App();
$app->charset = 'gbk';
$accessManager = $app->getAccessManager();
$accessManager->addVoter(function($api, $reqAll) use($accessManager){
//    $query = $_GET;
//    $reqParams = array_filter($query);
//
//    $key = 'your key';
//    $sign = new QiakeSign($key);
//
//    if($sign->checkSign($reqParams)){
//        return $accessManager::ACCESS_ABSTAIN;
//    } else {
//        return $accessManager::ACCESS_DENIED;
//    }
});

$app->setLogger($log);
$app->setApiNs($config->get('apiNs'));
$app->setMockPath($config->get('apiMockDir'));

$app->run();
