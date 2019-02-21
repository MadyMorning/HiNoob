<?php
namespace app\api\validate;

/**
 * 创建地址 验证
 */
class UpdateAddressValidate extends BaseValidate
{
  protected $rule = [
      'name'     => 'require',
      'mobile'   => 'require|regex:1[3-9]\d{9}',
      'province' => 'require',
      'city'     => 'require',
      'country'  => 'require',
      'detail'   => 'require'
  ];

  protected $field = [
      'name'     => '名称',
      'mobile'   => '手机号',
      'province' => '省份',
      'city'     => '市',
      'country'  => '区',
      'detail'   => '详细地址'
  ];
}
