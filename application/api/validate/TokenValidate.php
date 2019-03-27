<?php
namespace app\api\validate;

/**
 * Token是否为空
 */
class TokenValidate extends BaseValidate
{
  protected $rule = [
    'token' => 'require'
  ];

}
