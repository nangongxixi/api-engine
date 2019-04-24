# 概述

一个简单的开发api应用组件, 真的非常非常简单, 核心执行流程20行代码

```
# 安装
composer require scalpel/api-engine
```

## 特性

1. api对象编写简单, 只需要实现 ::handle()方法
1. api多版本支持, 通过请求参数apiVersion设定api版本号
2. 集成简单灵活的授权验证
3. 灵活的api class加载器, 逐层向上查询, ns1.ns2.ns3, 方便api扩展
4. 灵活响应错误, 顶层拦截异常处理
5. 多种请求格式支持, GET/POST/JSON
5. MockData支持
5. 多种编码支持 GKB/utf8/其它, 默认utf8
6. 简单api文档及测试集成
6. 完善的请求及响应日志


## 执行流程

1. 初始化app, 设置log, apiNs, mock data path, AccessManager
2. 运行app::run(), 开始接收请求
3. 初始化请求的api, request(json请求来源php://input)
4. 通过api加载器创建响应对象(实现::handle方法), 并初始化 api::init($req)
5. 验证授权, 基于app::AccessManager 与 $class::authentication()
6. 调用api::handle(request)返回数据



## Example

api入口程序

```
# 入口程序
# index.php
<?php

use j\apiSimple\App;
use j\apiSimple\tool\QiakeSign;
use j\base\Config;
use j\log\File as Log;
use j\tool\Strings;

require __DIR__ . '/../boot.inc.php';

$log = new Log(__DIR__ . "/api.log");
$config = Config::getInstance();

$app = new App();

// 鉴权设置
$accessManager = $app->getAccessManager();
$accessManager->addVoter(function($api, $reqAll) use($accessManager){
    $query = $_GET;
    $reqParams = array_filter($query);

    $key = 'your key';
    $sign = new QiakeSign($key);

    if($sign->checkSign($reqParams)){
        return $accessManager::ACCESS_ABSTAIN;
    } else {
        return $accessManager::ACCESS_DENIED;
    }
});

// 日志
$app->setLogger($log);

// api namespace
$app->setApiNs($config->get('apiNs'));

// mock data path
$app->setMockPath($config->get('apiMockDir'));

$app->run();

```

Api class

```
/**
 * Class Test
 * @package wxapp\api
 */
class Test extends Base{

    /**
     * @inheritdoc
     */
    public function actionDefault($request){
        // TODO: Implement handle() method.
        return [
            'apiName' => 'Test',
            'message' => 'This is a test',
        ];
    }
}
```

请求api

```
$http = new j\net\http\Client;
$http->setLogger(new Log());
$apiUrl = sprintf($apiGatewayUrl, $api);
$rs = $http->post($apiUrl, $request);
```

## api doc

```
@see example/doc
```

- https://github.com/OAI/OpenAPI-Specification/blob/master/versions/3.0.1.md#schema
- https://swagger.io/
- https://github.com/zircote/swagger-php
