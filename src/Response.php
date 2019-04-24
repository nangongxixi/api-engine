<?php

namespace j\apiSimple;

use function array_merge;
use function is_array;
use j\error\Exception;
use j\tool\Strings;

/**
 * Class Response
 * @package wxapp\lib
 */
class Response
{
    protected $code = 200;
    protected $data;
    protected $charset;
    protected $isRaw = false;
    protected $headers = [];

    /**
     * @var \Exception
     */
    protected $error;

    function __construct($data = [], $charset = 'utf-8', $isRaw = false) {
        $this->charset = $charset;
        if($data){
            $this->data = $data;
        }
        $this->isRaw = $isRaw;
    }

    function setData($data){
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(){
        return $this->data;
    }

    function setCharset($charset){
        $this->charset = $charset;
    }

    function setError(\Exception $e){
        $this->error = $e;
        $this->data = $e->getMessage();
        $this->code = $e->getCode();
    }

    public function isError(){
        return isset($this->error) && $this->error;
    }

    public function getError() {
        return $this->error;
    }

    protected function toUtf8($data){
        if($this->charset != 'utf-8'){
            return Strings::utf8($data);
        }
        return $data;
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    function setHeader($key, $value = null) {
        if(is_array($key)){
            $this->headers = array_merge($this->headers, $key);
        } else {
            $this->headers[$key] = $value;
        }
        return $this;
    }

    protected function sendHeader(){
        foreach($this->headers as $key => $value){
            header("{$key}:{$value}");
        }
    }

    function send($return = false){
        if(!$this->isRaw){
            if($this->code == 200){
                $data = [
                    'code' => $this->code,
                    'data' => $this->data
                ];
            } else {
                $e = $this->error;
                $eDetail = $e instanceof Exception
                    ? $e->getInfo()
                    : (isset($e->errors) ? $e->errors : []);

                if(isset($eDetail) && !$eDetail){
                    $eDetail = null;
                }

                $data = [
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                    'errors' => $eDetail
                ];
            }
        } else {
            $data = $this->data;
        }

        $json = json_encode($this->toUtf8($data));
        $json = str_replace('"{}"', '{}', $json);

        if(isset($_REQUEST['callback']) && ($callback = $_REQUEST['callback']) && is_string($callback)){
            $this->setHeader('Content-Type', 'application/x-javascript;charset=utf-8');
            $this->sendHeader();
            echo $callback ."({$json});";
        }else{
            $this->setHeader('Content-Type', 'application/json;charset=utf-8');
            $this->sendHeader();
            echo $json;
        }
    }
}
