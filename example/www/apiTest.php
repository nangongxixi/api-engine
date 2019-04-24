<?php

use j\apiSimple\ApiTest;
use j\base\Config;
use j\tool\ArrayUtils;

require(__DIR__ . '/../boot.inc.php');

$config = Config::getInstance();
$test = new ApiTest(__DIR__ . '/../tests', $config->get('apiGateway'));

$apis = $test->getApis();
if(isset($_REQUEST['api'])){
    $api = ArrayUtils::gav($_REQUEST, 'api');
    $rs = $test->callApi($api);
    exit($rs);
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Test api</title>
    <!-- 最新版本的 Bootstrap 核心 CSS 文件 -->
    <link rel="stylesheet" href="https://cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css"
          integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
</head>
<body >

<div class="container">
    <header><h1>微信小程序Api测试</h1></header>
</div>
<div class="container" style="margin-top: 40px;">
    <div class="row">
        <div class="col-xs-2">
            <ul>
                <?php
                foreach($apis as $api => $item){
                    $url = "?api={$api}";
                    echo "<li><a href='{$url}'>{$item['name']}</a></li>\n";
                }
                ?>
            </ul>
        </div>
        <div class="col-xs-10">
            <pre id="rs">

点击左边api, 这里显示调用api的测试结果,

菜单配置在apiTests目录，配置文件名与api名称相同
    name|string 菜单名称
    doc|array 调用参数说明文档
    query|array get查询参数
    post|array post参数
    test|callback 测试用例，return true表明测试通过

            </pre>
        </div>
    </div>
</div>

<script src="https://cdn.bootcss.com/jquery/3.2.1/jquery.js"></script>
<script>
    $(function(){
      $('ul a').click(function(){
        $.get(this.href, function(rs){
          $("#rs").html(rs);
        });
        return false;
      })
    });
</script>
</body>
</html>
