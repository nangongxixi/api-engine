<?php
#NotFoundException.php created by stcer@jz at 2017/7/29
namespace j\apiSimple\exceptions;

/**
 * Class NotFoundException
 * @package j\apiSimple\exceptions
 */
class NotFoundException extends BaseException{

    protected $code = self::NOT_FOUND;

    protected $message = "Info not found";

}