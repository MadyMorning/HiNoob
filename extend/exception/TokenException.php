<?php
namespace exception;

/**
 * Token错误处理
 */
class TokenException extends BaseException
{
  // 状态码
  public $code = 401;
  // 错误信息
  // public $message = '';
  // 自定义错误码
  public $error_code = 10005;
}
