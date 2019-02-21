<?php
namespace exception;

/**
 * 微信错误处理
 */
class WeiCateException extends BaseException
{
  // 状态码
  public $code = 200;
  // 错误信息
  // public $message;
  // 自定义错误码
  public $error_code = 999;
}
