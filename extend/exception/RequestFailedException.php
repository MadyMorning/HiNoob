<?php
namespace exception;
/**
 * 通用 请求失败 错误
 */
class RequestFailedException extends BaseException
{
  // 状态码
  public $code = 405;
  // 错误信息
  // public $message = '无效的ID';
  // 自定义错误码
  public $error_code = 10004;

}
