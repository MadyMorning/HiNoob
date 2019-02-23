<?php
namespace app\api\validate;
use exception\ParameterException;

/**
 * 提交订单
 */
class OrderValidate extends BaseValidate
{
  protected $rule = [
    'products' => 'checkProducts',
    'address_id' => 'require|isPositiveInteger'
  ];

  protected $message = [
    'address_id' => '请选择地址'
  ];

  // 子验证规则
  protected $singleRule = [
    'product_id' => 'require|isPositiveInteger',
    'count'      => 'require|isPositiveInteger',
  ];

  /**
   * 验证客户端发送来的信息集合
   * @param  array $value 要验证的信息
   * @return boolean
   */
  protected function checkProducts($value)
  {
    if (!\is_array($value) || !$value) {
      throw new ParameterException('商品参数错误');
    }

    foreach ($value as $v) {
      $this->checkProduct($v);
    }

    return true;
  }

  /**
   * 验证商品
   * @param  array $value 要验证的商品
   */
  protected function checkProduct($value)
  {
    $validate = new BaseValidate($this->singleRule);
    $result = $validate->check($value);
    if (!$result) {
      throw new ParameterException('请将信息补充完整');
    }
  }
}
