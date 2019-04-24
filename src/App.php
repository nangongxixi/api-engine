<?php

namespace j\apiSimple;

use j\apiSimple\event\AuthEvent;
use j\log\TraitLog;
use j\tool\ArrayUtils;
use j\event\TraitManager;
use j\tool\Strings;


/**
 * Class Json
 * @package wxapp\lib
 *
 * @property Response $response
 */
class App{

    use TraitLog;

    use TraitManager;

    const EVENT_DISPATCH_BEFORE = 'dispatch.before';
    const EVENT_RESPONSE_BEFORE = 'respone.before';
    const EVENT_RESPONSE_AFTER = 'respone.after';
    const EVENT_AUTH_BEFORE = 'auth.before';
    const EVENT_INVALID_REQUEST = 'request.invalid';

    /**
     * @var string
     */
    protected $apiNs = '\\simple\\api\\';

    /**
     * @var ApiClassLoader
     */
    public $apiLoader;

    /**
     * @var string
     */
    protected $mockPath;

    /**
     * @var MockData
     */
    public $mockData;

    /**
     * @var AccessDecisionManager
     */
    public $accessManager;


    const CHARSET_DEFAULT = 'utf-8';

    /**
     * @var string
     */
    public $charset = self::CHARSET_DEFAULT;


    public $apiVersion = '';

    /**
     * @param string $apiNs
     */
    public function setApiNs($apiNs){
        $this->apiNs = $apiNs;
        if(isset($this->apiLoader)){
            $this->apiLoader->setApiNs($apiNs);
        }
    }

    /**
     * @param mixed $mockPath
     */
    public function setMockPath($mockPath){
        $this->mockPath = rtrim($mockPath, '/') . "/";
        if(isset($this->mockData)){
            $this->mockData->setMockPath($mockPath);
        }
    }

    /**
     * @return Response
     */
    protected function getResponse(){
        return new Response([], $this->charset);
    }

    /**
     * run
     */
    public function run(){
        $api = $req = null;
        $response = $this->getResponse();

        try
        {
            $api = $this->getApi();
            $req = $this->getRequest();
            if($this->charset != self::CHARSET_DEFAULT){
                $req = Strings::toGbk($req);
            }

            $requestEvent = new event\RequestEvent($api, $req);
            $this->trigger($requestEvent);

            $class = $this->getApiClass($api, ArrayUtils::gav($req, 'apiVersion', $this->apiVersion));
            if(!isset($class->charset)){
                $class->charset = $this->charset;
            }

            if(method_exists($class, 'init')){
                $class->init($req);
            }

            if($logger = $this->getLogger()){
                $class->setLogger($logger);
            }

            $this->authentication(new AuthEvent($requestEvent, $class), $_REQUEST);

            $data = $class->handle($req);
            if(!($data instanceof Response)){
                $response->setData($data);
            } else {
                $response = $data;
            }
        }
        catch (exceptions\BaseException $e)
        {
            if($e instanceof exceptions\InvalidApiException
                && isset($api)
                && ($data = $this->getMockData($api))
            ){
                // 测试数据
                $response->setData($data);
            } else {
                $response->setError($e);
            }
            $response->setCharset('utf-8');

        } catch (\Exception $e){
            $response->setError($e);
        }

        if(!isset($requestEvent)){
            if($this->isBind(self::EVENT_INVALID_REQUEST)){
                $event = new event\InvalidRequestEvent($response);
                $this->trigger($responseEvent);
            }
            $response->send();
        } else {
            if($this->isBind(self::EVENT_RESPONSE_BEFORE)) {
                // response before
                $responseEvent = new event\ResponseEvent($requestEvent, $response);
                $this->trigger($responseEvent);
            }

            $response->send();

            if($this->isBind(self::EVENT_RESPONSE_AFTER)) {
                // response after
                $responseEvent = new event\ResponseEvent($requestEvent, $response, self::EVENT_RESPONSE_AFTER);
                $this->trigger($responseEvent);
            }
        }
    }

    /**
     * @return mixed|string
     * @throws exceptions\InvalidApiException
     */
    protected function getApi(){
        if(isset($_REQUEST['ac'])){
            $api = $_REQUEST['ac'];
        }else{
            $api = ArrayUtils::gav($_REQUEST, 'api');
        }

        if(!preg_match('/^[a-zA-Z\.\/]+$/', $api)){
            throw new exceptions\InvalidApiException();
        }

        if(is_numeric(strpos($api, '/'))){
            $api = str_replace('/', '.', $api);
        }
        return trim($api, '.');
    }

    /**
     * @return mixed
     * @throws exceptions\ArgumentException
     */
    protected function getRequest(){
        if((isset($_SERVER['REQUEST_METHOD'])
            && strtoupper($_SERVER['REQUEST_METHOD']) == 'POST')
        ){
            if(isset($_SERVER['CONTENT_TYPE'])
                && strpos($_SERVER['CONTENT_TYPE'], 'application/json') === 0
            ){
                $content = file_get_contents('php://input');
                if(!$content){
                    throw new exceptions\ArgumentException("Invalid post data");
                }
                $_POST = json_decode($content, true);
            }
            return $_POST + $_REQUEST;
        }else{
            return $_REQUEST;
        }
    }

    /**
     * @param $api
     * @param int $apiVersion
     * @return BaseApi
     * @throws exceptions\BaseException
     */
    protected function getApiClass($api, $apiVersion = 0){
        return $this->getApiClassLoader()->loadClass($api, $apiVersion);
    }

    /**
     * @return ApiClassLoader
     */
    public function getApiClassLoader() {
        if(!isset($this->apiLoader)){
            $this->apiLoader = new ApiClassLoader($this->apiNs);
            if($log = $this->getLogger()){
                $this->apiLoader->setLogger($log);
            }
        }
        return $this->apiLoader;
    }

    /**
     * @param AuthEvent $authEvent
     * @param $reqAll
     * @return bool
     * @throws
     */
    protected function authentication($authEvent, $reqAll){
        $accessManager = $this->getAccessManager();

        // add default voter
        $class = $authEvent->getApiHandle();
        if(method_exists($authEvent->getApiHandle(), 'authentication')){
            $accessManager->addVoter(function() use($class, $authEvent){
                if($class->authentication($authEvent)){
                    return AccessDecisionManager::ACCESS_GRANTED;
                } else {
                    return AccessDecisionManager::ACCESS_DENIED;
                }
            });
        }

        if(!$accessManager->decide($authEvent, $reqAll)){
            throw new exceptions\AuthorizationException('Authorization fail');
        } else {
            return true;
        }
    }

    /**
     * @return AccessDecisionManager
     */
    public function getAccessManager(){
        if(!isset($this->accessManager)){
            $this->accessManager = new AccessDecisionManager();
        }

        return $this->accessManager;
    }

    /**
     * @param $api
     * @return bool|array
     */
    protected function getMockData($api){
        if(!isset($this->mockData)){
            if(!isset($this->mockPath)){
                return false;
            }
            $this->mockData = new MockData($this->mockPath);
        }
        return $this->mockData->getMockData($api);
    }
}
