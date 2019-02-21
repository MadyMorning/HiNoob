<?php
namespace exception;

/**
 * 成功提示信息
 */
class SuccessMessage extends BaseException
{
  // 状态码
  public $code = 201;
  // 错误信息
  // public $message = '';
  // 自定义错误码
  public $error_code = 0;

}
