<?php
namespace exception;
/**
 * 缓存通用错误处理
 */
class CacheException extends BaseException
{
  // 状态码
  public $code = 404;
  // 错误信息
  // public $message = '';
  // 自定义错误码
  public $error_code = 10003;
}
