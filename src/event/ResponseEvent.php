<?php
#ReqeustEvent.php created by stcer@jz at 2018/8/13 0013
namespace j\apiSimple\event;

use j\event\Event as Base;
use j\apiSimple\App;
use j\apiSimple\Response;

/**
 * Class ReqeustEvent
 * @package j\apiSimple
 */
class ResponseEvent extends Base{

    /**
     * @var Response
     */
    public $response;

    /**
     * @var RequestEvent
     */
    public $requestEvent;

    /**
     * ResponseEvent constructor.
     * @param RequestEvent $requestEvent
     * @param $response
     * @param string $eventName
     */
    public function __construct(RequestEvent $requestEvent, Response $response, $eventName = App::EVENT_RESPONSE_BEFORE){
        parent::__construct($eventName, []);
        $this->requestEvent = $requestEvent;
        $this->response = $response;
    }
}
