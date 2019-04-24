<?php

namespace j\apiSimple\exceptions;

/**
 * Class SignatureException
 * @package j\apiSimple\exceptions
 */
class AuthorizationException extends BaseException{

    protected $code = self::AuthorizationFail;

    protected $message = "Authorization fail";

}