<?php
#ReqeustEvent.php created by stcer@jz at 2018/8/13 0013
namespace j\apiSimple\event;

use j\event\Event as Base;
use j\apiSimple\App;

/**
 * Class ReqeustEvent
 * @package j\apiSimple
 */
class RequestEvent extends Base{

    protected $data;
    public $api;

    /**
     * ReqeustEvent constructor.
     * @param $name
     * @param $requestData
     */
    public function __construct($api, & $requestData){
        parent::__construct(App::EVENT_DISPATCH_BEFORE, []);
        $this->api = $api;
        $this->data =& $requestData;
    }

    function setRequestValue($key, $value){
        $this->data[$key] = $value;
    }

    public function getReqeustValue($key, $def = null){
        return gav($this->data, $key, $def);
    }

    public function getReqeustData(){
        return $this->data;
    }

    public function getApi(){
        return $this->api;
    }
}
