<?php
namespace app\api\service;
use exception\ParameterException;
use app\api\model\OrderModel;

/**
 * 支付
 */
class PayService
{
  private $orderID;
  private $orderNO;

  function __construct($orderID)
  {
    if (!$orderID) {
      throw new ParameterException('订单号不允许为空！');
    }

    $this->orderID = $orderID;
  }

  /**
   * 支付
   * @return [type] [description]
   */
  private function pay()
  {
    $this->checkOrder();
  }

  private function checkOrder()
  {
    $orderInfo = OrderModel::find($this->orderID);
    // 判断该订单是否存在
    if (!$orderInfo) {
      throw new ParameterException('无效的ID');
    }

    // 判断订单和用户是否匹配
    $userToken = \request()->header('token');
    $user_id = \cache($userToken);
    if ($orderInfo->user_id != $user_id) {
      throw new ParameterException('订单和用户不匹配！');
    }

    // 判断订单是否已支付
    if ($orderInfo->status != 1) {
      throw new ParameterException('订单已经支付过，请勿重复支付！');
    }
  }
}
