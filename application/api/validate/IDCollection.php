<?php
namespace app\api\validate;

/**
 * ID必须为正整数或以逗号分隔开的正整数集
 */
class IDCollection extends BaseValidate
{
  protected $rule = [
    'ids' => 'require|isCollection'
  ];

  public function isCollection($value)
  {
    $data = explode(',', $value);

    foreach ($data as $v) {
      if (!$this->isPositiveInteger($v)) {
        return 'ID必须为正整数或以逗号分隔开的正整数集';
      }
    }

    return true;
  }
}
