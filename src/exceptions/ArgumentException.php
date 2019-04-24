<?php

namespace j\apiSimple\exceptions;

/**
 * Class ArgumentException
 * @package j\apiSimple\exceptions
 */
class ArgumentException extends BaseException{

    protected $code = self::Arguments;

    protected $message = "参数错误";

}