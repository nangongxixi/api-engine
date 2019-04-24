<?php
#AuthEvent.php created by stcer@jz at 2018/8/14 0014
namespace j\apiSimple\event;

use j\apiSimple\BaseApi;
use j\apiSimple\Response;
use j\event\Event as Base;
use j\apiSimple\App;

/**
 * Class AuthEvent
 * @package j\apiSimple
 */
class AuthEvent extends Base{

    /** @var RequestEvent */
    protected $request;

    /**
     * @return mixed
     */
    public function getApiHandle(){
        return $this->apiHandle;
    }

    /** @var BaseApi */
    protected $apiClass;

    /**
     * AuthEvent constructor.
     * @param RequestEvent $requestEvent
     */
    public function __construct($requestEvent, $apiClass){
        parent::__construct(App::EVENT_AUTH_BEFORE, []);
        $this->apiHandle = $apiClass;
        $this->request = $requestEvent;
    }

    public function getReqeustValue($key, $def = null){
         return $this->request->getReqeustValue($key, $def);
    }

    public function getApi(){
        return $this->request->getApi();
    }
}
