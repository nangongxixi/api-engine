<?php

namespace j\apiSimple\exceptions;

/**
 * Class InvalidApiException
 * @package j\apiSimple\exceptions
 */
class InvalidApiException extends BaseException{

    protected $code = self::API;

    protected $message = "无效api";

}