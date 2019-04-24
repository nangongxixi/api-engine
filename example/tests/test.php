<?php

return  [
    'name' => '测试APi',
    'query' => 'uid=6388',
    'doc' => [

        ],
    'test' => function($code, $data, $rs){
        var_dump($data);
        if($data['apiName'] == 'Test gbk\u4e2d\u6587'){
            return true;
        } else {
            return false;
        }
    }
];