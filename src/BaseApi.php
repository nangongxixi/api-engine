<?php

namespace j\apiSimple;

use j\apiSimple\event\AuthEvent;
use j\log\TraitLog;
use j\tool\ListFilter;


/**
 * Class Base
 * @package wxapp\lib
 */
abstract class BaseApi {

    use TraitLog;

    /**
     * @var string
     */
    protected $action;
    protected $response;
    protected $isPublic = true;
    public $charset;

    /**
     * @param null|AuthEvent $authEvent
     * @return bool
     */
    function authentication($authEvent = null) {
        return $this->isPublic();
    }

    protected $user;
    function setUser($user){
        $this->user = $user;
    }

    protected $params = [];

    /**
     * BaseApi constructor.
     * @param $action
     */
    public function __construct($action){
        $this->action = $action;
    }

    /**
     * @return string
     */
    protected function getAction(){
        return $this->action;
    }


    /**
     * @param array $params
     * @return $this
     */
    public function init($params = []){
        $this->setParams($params);
        return $this;
    }

    /**
     * @param array $params
     */
    public function setParams($params){
        $this->params = $params;
    }

    /**
     * @param $request
     * @return string|array|Response
     */
    abstract public function handle($request);

    /**
     * @return bool
     */
    public function isPublic(){
        return $this->isPublic;
    }

    /**
     * @param $data
     * @param string $charset
     * @return Response
     */
    protected function response($data, $charset = ''){
        if(!isset($this->response)){
            $this->response = new Response($data, $charset ?: $this->charset);
        }
        $this->response->setData($data);
        return $this->response;
    }

    /**
     * @param $n
     * @param int $min
     * @param int $max
     * @return int
     */
    protected function getInt($n, $min = 0, $max = 0){
        $n = intval($n);
        if($min && $n == 0){
            $n = $min;
        }elseif($max && $n > $max){
            $n = $max;
        }
        return $n;
    }

    /**
     * @var ListFilter
     */
    static $filter;

    /**
     * @return ListFilter
     */
    public function getFilter(){
        if(!isset(self::$filter)){
            self::$filter = new ListFilter();
            self::$filter->getModifier()->regCall('boolF', function(){
                return false;
            });
        }
        return self::$filter;
    }

    /**
     * @param $list
     * @param $keys
     * @param $callback
     * @return array
     */
    protected function filterList($list, $keys, $callback = null){
        return $this->getFilter()->filter($list, $keys, $callback);
    }

    /**
     * @param $info
     * @param array $keys
     * @return array
     */
    protected function filterInfo($info, $keys = []){
        if(!$keys){
            if(is_array($info)){
                return $info;
            }

            if(is_object($info) && method_exists($info, 'toArray')){
                return $info->toArray();
            }
        }
        return $this->getFilter()->filterRow($info, $keys);
    }
}
