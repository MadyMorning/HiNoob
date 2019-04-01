<?php

namespace app\api\controller\v1;

use app\api\service\Token;
use app\api\service\OrderService;
use app\api\validate\OrderValidate;
use app\api\validate\PagingParmeterValidate;
use app\api\model\OrderModel;
use app\api\validate\IDMustPositiveInteger;
use exception\ResourceException;
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

  /**
   * 获取用户历史订单（分页）
   *
   * @param   integer  $page  当前页码 默认为 1
   * @param   integer  $size  每页展示的数据 默认为 10
   *
   * @return  object         返回用户历史订单分页数据
   */
  public function getHistoryOrders($page = 1, $size = 10)
  {
    $this->userAndhigher();
    (new PagingParmeterValidate())->gocheck();

    $uid = Token::getTokenUID();
    $paginateOrder = OrderModel::where('user_id', $uid)->order('create_time', 'desc')->paginate($size, true, ['page' => $page]);
    if ($paginateOrder->isEmpty()) {
      return \json([
        'data' => [],
        'current_page' => $paginateOrder->getCurrentPage() //当前页码
      ]);
    }
    return json($paginateOrder->toArray());
  }
  
  /**
   * 获取用户历史订单（全部）
   *
   * @return  object         返回用户历史订单数据
   */
  public function getAllHistoryOrders()
  {
    $this->userAndhigher();
    (new PagingParmeterValidate())->gocheck();

    $uid = Token::getTokenUID();
    $OrderInfo = OrderModel::where('user_id', $uid)->order('create_time', 'desc')->select();
    if (!$OrderInfo) {
      return \json($OrderInfo);
    }
    return json(\collection($OrderInfo)->hidden(['user_id', 'delete_time', 'prepay_id', 'snap_address', 'snap_items'])->toArray());
  }

  /**
   * 获取订单详情
   *
   * @param   string  $id  订单ID
   *
   * @return  object    返回订单信息
   */
  public function getOrderDetail($id)
  {
    $this->userAndhigher();
    (new IDMustPositiveInteger())->gocheck();

    $orderDetail = OrderModel::find($id);
    if (!$orderDetail) {
      throw new ResourceException('订单不存在');
    }

    return \json($orderDetail->hidden(['prepay_id']));
  }
}
