<?php
# TraitCtrlAction.php
/**
 * User: Administrator
 * Date: 2017/12/12 0012
 * Time: 下午 16:33
 */

namespace j\apiSimple;

/**
 * Trait TraitCtrlAction
 * @package j\apiSimple
 */
trait TraitCtrlAction {

    /**
     * @return string
     */
    abstract protected function getAction();

    /**
     * @param $request
     * @return Response|array|string
     * @throws exceptions\InvalidApiException
     */
    public function handle($request){
        $method = "action" . ucfirst($this->getAction());

        if(method_exists($this, $method)){
            return $this->{$method}($request);
        }
        throw new exceptions\InvalidApiException("Invalid action");
    }

}