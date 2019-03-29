<?php
namespace exception;

/**
 * 通用 权限不足 错误
 */
class PermissionException extends BaseException
{
  // 状态码
  public $code = 403;
  // 错误信息
  // public $message = '';
  // 自定义错误码
  public $error_code = 10005;

}
