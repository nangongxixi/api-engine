<?php

namespace j\apiSimple\exceptions;

/**
 * Class SignatureException
 * @package j\apiSimple\exceptions
 */
class LoginException extends BaseException{

    protected $code = self::LOGIN_FAIL;

    protected $message = "Login fail";

}