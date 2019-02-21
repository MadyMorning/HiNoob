<?php
namespace app\api\validate;

/**
 *  ID必须为正整数
 */
class IDMustPositiveInteger extends BaseValidate
{
  protected $rule = [
    'id' => 'require|checkID'
  ];

  /**
   * ID必须为正整数
   * @param  string  $value 传递过来的ID值
   * @return 返回boolean值或错误信息
   */
  protected function checkID($value)
  {
    if ($this->isPositiveInteger($value)) {
      return true;
    }
    return 'ID必须为正整数';
  }
}
