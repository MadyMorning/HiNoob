<?php
namespace app\api\validate;

/**
 * 提交订单
 */
class OrderValidate extends BaseValidate
{
  protected $rule = [
    'products' => 'checkProducts'
  ];

  // 子验证规则
  protected $singleRule = [
    'product_id' => 'require|isPositiveInteger',
    'count' => 'require|isPositiveInteger'
  ];

  /**
   * 验证商品集
   * @param  array $value 要验证的商品集
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
    $validate->check($value);
    if (!$validate) {
      throw new ParameterException('商品参数错误');
    }
  }
}
