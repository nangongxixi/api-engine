<?php

namespace j\apiSimple\exceptions;

use Exception;

/**
 * Class BaseException
 * @package j\apiSimple\exceptions
 */
class BaseException extends Exception{

    const UNKNOWN = 4000; // 参数错误
    const Arguments = 4001; // 参数错误
    const API = 4003; // 无效API
    const UAuthentication = 403; // 验证失败
    const USER_LOCK = 4031; // 会员锁定
    const NOT_FOUND = 404; // 信息查询失败
    const Validate = 5001; // 数据验证失败
    const SIGN = 5002; // 错误签名
    const LOGIN_FAIL = 5004; // 登录错误
    const AuthorizationFail = 5006; // 未登录

    protected $info = [];

    /**
     * BaseException constructor.
     * @param string $message
     * @param int $code
     * @param array $info
     * @param Exception|null $previous
     */
    public function __construct(
        $message = "", $code = 0,
        $info = [], Exception $previous = null
    ) {
        if(!$message && isset($this->message)){
            $message = $this->message;
        }
        Exception::__construct($message, $code, $previous);
        $this->setInfo($info);
    }


    /**
     * @param mixed $info
     */
    public function setInfo($info) {
        $this->info = $info;
    }

    /**
     * @return mixed
     */
    public function getInfo() {
        return $this->info;
    }
}