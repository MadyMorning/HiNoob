<?php
namespace app\api\service;
use app\api\model\ProductModel;
use exception\RequestFailedException;
use exception\ResourceException;

/**
 * 订单service
 */
class OrderService
{
  protected $order_products;  //客户端发送来的商品信息
  protected $products;   //数据库中的商品信息
  protected $uid;   //用户ID


  public function placeOrder($uid, $order_products)
  {
    $this->order_products = $order_products;
    $this->uid            = $uid;
    $this->products       = $this->getProductByOrder($order_products);

    $status = $this->getOrderStatus();
    // 若库存量检测未通过
    if (!$status['pass']) {
      $status['order_id'] = -1;
      return $status;
    }

    // 创建订单
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
      $products = ProductModel::with('Image')->select($order_PID)->hidden('summary')->toArray();
      return $products;
    } catch (\Exception $e) {
      throw new RequestFailedException($e->getMessage());
    }
  }

  /**
   * 获取订单状态
   * @return array 返回订单详细信息
   */
  private function getOrderStatus()
  {
    $status = [
      'pass'         => true, //库存量检测是否通过
      'orderPrice'   => 0,    //订单总价格
      'pStatusArray' => []    //商品详细信息
    ];

    foreach ($this->order_products as $value) {
      $pStatus = $this->getProductStatus($value['product_id'], $value['count'], $this->products);

      if (!$pStatus['haveStock']) {
        $status['pass'] = false;
      }
      $status['orderPrice'] += $pStatus['eachPrice'];
      array_push($status['pStatusArray'], $pStatus);
    }

    return $status;
  }

  /**
   * 获取商品状态
   * @param  string $PID     订单中商品ID
   * @param  string $count   订单中商品数量
   * @param  array $product  数据库中商品信息
   * @return array           返回商品状态
   */
  private function getProductStatus($oPID, $oCount, $products)
  {
    $pStatus = [
      'id'        => null,   //商品ID
      'haveStock' => false,  //商品是否有库存
      'count'     => 0,      //商品购买数量
      'name'      => '',     //商品名称
      'eachPrice' => 0       //每一种商品的总价
    ];

    // 将从客户端发送来的商品ID与数据库中商品ID对应，并保存
    for ($i=0; $i < count($products); $i++) {
      if ($PID == $products[$i]['id']) {
        $pIndex = $i;
      }
    }
    // 若无对应，抛出异常
    if (!isset($pIndex)) {
      throw new ResourceException('创建订单失败，订单中含有不存在或已失效的商品！');
    }

    $product              = $products[$pIndex];
    $pStatus['id']        = $product['id'];
    $pStatus['haveStock'] = ($product['stock'] - $oCount >= 0);
    $pStatus['count']     = $oCount;
    $pStatus['name']      = $product['name'];
    $pStatus['eachPrice'] = $product['price'] * $oCount;

    return $pStatus;
  }
}
