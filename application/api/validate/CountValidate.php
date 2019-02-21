<?php
namespace app\api\validate;

/**
 * 最近新品数量验证
 */
class CountValidate extends BaseValidate
{
  protected $rule = [
    'count|数量' => 'between:1,20|checkCount'
  ];

  public function checkCount($value)
  {
    if ($this->isPositiveInteger($value)) {
      return true;
    }

    return '数量必须为正整数';
  }
}
