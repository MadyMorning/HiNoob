<?php
namespace exception;
use think\Exception;


/**
 * 公共异常处理类
 */
class BaseException extends Exception
{
  // 状态码
  public $code;
  // 错误信息
  public $message;
  // 自定义错误码
  public $error_code;
}
