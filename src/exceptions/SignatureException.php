<?php

namespace j\apiSimple\exceptions;

/**
 * Class SignatureException
 * @package j\apiSimple\exceptions
 */
class SignatureException extends BaseException{

    protected $code = self::SIGN;

    protected $message = "签名错误";

}