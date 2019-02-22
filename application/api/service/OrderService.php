<?php
namespace app\api\service;
use app\api\model\ProductModel;
use exception\RequestFailedException;
/**
 * 订单service
 */
class OrderService
{
  protected $order_products;  //客户端发送来的商品信息
  protected $product;   //数据库中的商品信息
  protected $uid;   //用户ID


  public function placeOrder($uid, $order_products)
  {
    $this->order_products = $order_products;
    $this->uid = $uid;
    $this->product = $this->getProductByOrder($order_products);

    // foreach ($order_products as $value) {
    //   $this->product = ProductModel::find($value['product_id']);
    //   if ($this->product->stock - $value['count'] < 0) {
    //     throw new RequestFailedException('所选商品库存量不足');
    //   }
    // }


  }

  /**
   * 根据客户端提交的商品信息获取数据库中真实商品信息
   * @param  array $order_products 客户端提交的商品信息
   * @return array                 返回真实商品信息
   */
  private function getProductByOrder($order_products)
  {
    // 循环遍历出商品ID
    $order_PID = [];
    foreach ($order_products as $value) {
      array_push($order_PID, $value['product_id']);
    }

    // 根据商品ID查询数据
    try {
      $product = ProductModel::with('Image')->select($order_PID)->hidden('summary')->toArray();
      return $product;
    } catch (\Exception $e) {
      throw new RequestFailedException($e->getMessage());
    }
  }

  private function getOrderStatus()
  {
    $status = [
      'pass' => true,       //库存量检测是否通过
      'orderPrice' => 0,    //总价格
      'pStatusArray' => []  //商品详细信息
    ];
  }
}
