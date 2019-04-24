<?php
#ReqeustEvent.php created by stcer@jz at 2018/8/13 0013
namespace j\apiSimple\event;

use j\apiSimple\Response;
use j\event\Event as Base;
use j\apiSimple\App;

/**
 * Class ReqeustEvent
 * @package j\apiSimple
 */
class InvalidRequestEvent extends Base{
    /**
     * @var Response
     */
    public $response;
    /**
     * ReqeustEvent constructor.
     * @param $name
     * @param $requestData
     */
    public function __construct($response){
        parent::__construct(App::EVENT_INVALID_REQUEST, []);
        $this->response = $response;
    }
}
