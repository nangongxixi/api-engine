<?php

namespace j\apiSimple;

/**
 * Class MockData
 * @package j\apiSimple
 */
class MockData {

    /**
     * @var string
     */
    protected $mockPath;

    /**
     * MockData constructor.
     * @param $mockPath
     */
    public function __construct($mockPath = ''){
        if($mockPath){
            $this->setMockPath($mockPath);
        }
    }

    /**
     * @param string $mockPath
     */
    public function setMockPath($mockPath){
        $this->mockPath = $mockPath;
    }


    /**
     * @param $api
     * @return bool|array
     */
    public function getMockData($api){
        if(!isset($this->mockPath) || !$api){
            return false;
        }

        $file = $this->mockPath . lcfirst($api) . ".js";
        if(!file_exists($file) || !file_get_contents($file)){
            return false;
        }

        $data = file_get_contents($file);
        $data = str_replace('export default', '', $data);
        $data = preg_replace('/,\s*([\]}])/m', '$1', $data);
        $data = json_decode($data, true);
        return $data;
    }
}