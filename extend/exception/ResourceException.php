<?php
namespace exception;
/**
 * 通用 资源不存在 错误
 */
class ResourceException extends BaseException
{
  // 状态码
  public $code = 404;
  // 错误信息
  // public $message = '';
  // 自定义错误码
  public $error_code = 10002;

}
