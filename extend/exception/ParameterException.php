<?php
namespace exception;

/**
 * 通用参数错误
 */
class ParameterException extends BaseException
{
  // 状态码
  public $code = 400;
  // 错误信息
  // public $message = '';
  // 自定义错误码
  public $error_code = 10001;
}
