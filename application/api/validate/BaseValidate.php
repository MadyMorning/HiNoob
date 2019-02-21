<?php
namespace app\api\validate;
use think\Validate;
use exception\ParameterException;

/**
 * 验证器基类
 */
class BaseValidate extends Validate
{
  /**
   * 拦截器
   * @return 返回boolean值或错误信息
   */
  public function gocheck()
  {
    $data = \request()->param();

    $request = $this->check($data);

    if (!$request) {
      throw new ParameterException($this->error);
    }

    return true;
  }

  /**
   * ID必须为正整数
   * @param  string  $value 传递过来的ID值
   * @return 返回boolean值或错误信息
   */
  protected function isPositiveInteger($value)
  {
    if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
      return true;
    }

    return false;
  }
}
