<?php

namespace app\api\controller\v1;
use app\api\validate\IDMustPositiveInteger;
use app\api\service\PayService;
use exception\ParameterException;

/**
 * 支付Controller
 */
class PayController extends BaseController
{
  /**
   * 获取预订单
   * @param  string $id 订单ID
   * @return [type]     [description]
   */
  public function getPreOrder($id)
  {
    // 权限验证
    $this->onlyUser();
    // 合法性验证
    (new IDMustPositiveInteger())->gocheck();

    // $payservice = new PayService($orderInfo['order_no']);
    // $payservice->pay($orderInfo['snap_items']);
  }
}
