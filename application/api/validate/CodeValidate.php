<?php
namespace app\api\validate;

/**
 * Code是否为空
 */
class CodeValidate extends BaseValidate
{
  protected $rule = [
    'code' => 'require'
  ];

}
