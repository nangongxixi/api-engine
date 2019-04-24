<?php

namespace j\apiSimple;

use j\base\SingletonTrait;
use j\log\TraitLog;
use function var_dump;

/**
 * Class ClassLoader
 * @package j\apiSimple
 */
class ApiClassLoader {

    use TraitLog;

    /**
     * @var string
     */
    protected $apiNs;

    protected $versionNs = [];

    /**
     * @param array $versionNs
     */
    public function setVersionNs(array $versionNs){
        $this->versionNs = $versionNs;
    }

    /**
     * ClassLoader constructor.
     * @param $apiNs
     */
    public function __construct($apiNs, $versionNs = []){
        $this->apiNs = $apiNs;
        $this->versionNs = $versionNs;
    }

    /**
     * @param string $apiNs
     */
    public function setApiNs($apiNs){
        $this->apiNs = $apiNs;
    }

    /**
     * @param $api
     * @param $apiVersion
     * @return BaseApi
     * @throws exceptions\InvalidApiException
     */
    public function loadClass($api, $apiVersion){
        $path = explode('.', $api);
        $ns1 = $this->apiNs;
        if($apiVersion){
            $ns2 = isset($this->versionNs[$apiVersion]) ? $this->versionNs[$apiVersion] : null;
        }

        $action = 'default';
        while($path){
            $className = array_pop($path);
            $classPath = ($path ? (implode('\\', $path)) . "\\" : '') . ucfirst($className);

            if(isset($ns2)){
                $apiClass = $ns2 . $classPath;
                $this->log($apiClass);
                if(class_exists($apiClass)){
                    return new $apiClass($action);
                }
            }

            $apiClass = $ns1 . $classPath;
            $this->log($apiClass);
            $this->log($action);
            if(class_exists($apiClass)){
                return new $apiClass($action);
            }

            $action = $className;
        }

        throw new exceptions\InvalidApiException("Api class not found(b)");
    }
}
