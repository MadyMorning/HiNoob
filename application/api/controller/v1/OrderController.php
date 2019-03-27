<?php

namespace app\api\controller\v1;

use think\Request;
use app\api\service\Token;
use app\api\service\OrderService;
use app\api\validate\OrderValidate;
/**
 * 订单Controller
 */
class OrderController extends BaseController
{
  /**
   * 1.用户点击提交订单，向API提交商品相关信息
   * 2.API接收到数据，进行库存量检测
   * 3.提交订单成功，数据库库存量减去相应数量，将订单状态置为未支付，
   * 4.用户付款成功，将订单状态置为已支付，付款失败，返回失败消息
   * 5.若超时(30mins)未支付，将订单状态置为已取消，且将库存量恢复；
   * 6.用户付款失败，不取消订单；只有在用户主动取消或超时(30mins)未处理时才取消订单
   */

  /**
   * 提交订单
   * @return object    返回订单信息
   */
  public function submitOrders()
  {
    $this->onlyUser();
    // 合法性验证
    (new OrderValidate())->gocheck();

    // $order_products = \input('post.products/a');
    $order_products = \request()->post();
    $uid = Token::getTokenUID();
    $orderService = new OrderService();
    $result = $orderService->placeOrder($uid, $order_products);

    return \json($result);
  }
}
