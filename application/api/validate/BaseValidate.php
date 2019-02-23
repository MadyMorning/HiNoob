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

    $result = $this->check($data);

    if (!$result) {
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

  /**
   * 根据验证规则获取所需数据
   * @param  array $array 客户端传递来的参数
   * @return array        返回所需数据
   */
  public function getDataByRule($array)
  {
    if (array_key_exists('user_id', $array) || array_key_exists('uid', $array)) {
      throw new ParameterException('参数中包含非法参数user_id或uid');
    }

    $parameter_array = [];
    foreach ($this->rule as $key => $value) {
      $parameter_array[$key] = $array[$key];
    }

    return $parameter_array;
  }
}
