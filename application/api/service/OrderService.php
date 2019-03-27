<?php
namespace app\api\service;
use app\api\model\OrderModel;
use app\api\model\ProductModel;
use app\api\model\UserAddressModel;
use app\api\model\OrderProductModel;
use exception\RequestFailedException;
use exception\ResourceException;
use think\Db;

/**
 * 订单service
 */
class OrderService
{
  protected $order_products;  //客户端发送来的商品信息
  protected $products;        //数据库中的商品信息
  protected $address_id;       //地址ID
  protected $uid;             //用户ID


  /**
   * 生成订单
   * @param  string $uid           用户ID
   * @param  array $order_products 客户端提交的商品信息
   * @return array                 返回订单信息
   */
  public function placeOrder($uid, $order_products)
  {
    $this->order_products = $order_products['products'];
    $this->address_id     = $order_products['address_id'];
    $this->uid            = $uid;
    $this->products       = $this->getProductByOrder($order_products['products']);

    $status = $this->getOrderStatus();
    // 若库存量检测未通过
    if (!$status['pass']) {
      $status['order_id'] = -1;
      return $status;
    }

    // 生成订单快照
    $orderSnap = $this->snapOrder($status);

    // 创建订单
    $result = $this->createOrder($orderSnap);
    return $result;
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
      $products = ProductModel::with('Image')->select($order_PID);
      $products_array = [];
      foreach ($products as $key => $product) {
        $products_array[$key] = $product->toArray();
      }
      // $products = $products->toArray();
      // $products = json_decode($products, true);
      return $products_array;
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
      'totalCount'   => 0,    //订单商品总数量
      'pStatusArray' => []    //商品详细信息
    ];

    foreach ($this->order_products as $value) {
      $pStatus = $this->getProductStatus($value['product_id'], $value['count'], $this->products);

      if (!$pStatus['haveStock']) {
        $status['pass'] = false;
      }
      $status['orderPrice'] += $pStatus['eachPrice'];
      $status['totalCount'] += $pStatus['count'];
      array_push($status['pStatusArray'], $pStatus);
    }

    return $status;
  }

  /**
   * 获取商品状态
   * @param  string $oPID     订单中商品ID
   * @param  string $oCount   订单中商品数量
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
      if ($oPID == $products[$i]['id']) {
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

  /**
   * 生成订单快照
   * @param  array $status 订单状态
   * @return array         返回订单快照信息
   */
  private function snapOrder($status)
  {
    $snap = [
      'orderPrice'  => 0,  //订单总价格
      'totalCount'  => 0,  //订单总数量
      'pStatus'     => [], //商品信息
      'snapName'    => '', //快照名称
      'snapImg'     => '', //快照图片
      'snapAddress' => '', //快照地址
    ];

    $snap['orderPrice']  = $status['orderPrice'];
    $snap['totalCount']  = $status['totalCount'];
    $snap['pStatus']     = $status['pStatusArray'];
    $snap['snapName']    = $this->products[0]['name'];
    $snap['snapImg']     = $this->products[0]['image']['url'];
    $snap['snapAddress'] = json_encode($this->getSnapAddresss());

    return $snap;
  }

  /**
   * 获取快照地址
   * @return array 返回地址信息
   */
  private function getSnapAddresss()
  {
    $addressInfo = UserAddressModel::find($this->address_id);
    if (!$addressInfo) {
      throw new ResourceException('用户收货地址不存在，下单失败');
    }

    return $addressInfo->toArray();
  }

  /**
   * 创建订单
   * @return $orderSnap 订单快照信息
   */
  private function createOrder($orderSnap)
  {
    $order = [];
    $order['order_no'] = self::GenerateOrderNumber();
    $order['user_id']  = $this->uid;
    $order['total_price'] = $orderSnap['orderPrice'];
    $order['status'] = 1;
    $order['snap_img'] = $orderSnap['snapImg'];
    $order['snap_name'] = $orderSnap['snapName'];
    $order['total_count'] = $orderSnap['totalCount'];
    $order['snap_items'] = json_encode($orderSnap['pStatus']);
    $order['snap_address'] = $orderSnap['snapAddress'];

    // 开启事务
    Db::startTrans();
    try {
      $result = OrderModel::create($order);

      // 在 order 和 product 的中间表中插入数据
      foreach ($this->order_products as &$value) {
        $value['order_id'] = $result->id;
      }
      $order_product = new OrderProductModel();
      $order_product->saveAll($this->order_products);

      // 提交事务
      Db::commit();

      return [
        'orderID' => $result->id,
        'orderNO' => $result->order_no,
        'createTime' => $result->create_time,
        'status' => true
      ];
    } catch (\Exception $e) {
      // 回滚事务
      Db::rollback();
      throw new RequestFailedException('创建订单失败:' . $e->getMessage());
    }
  }

  // private function RefreshInventory()
  // {
  //   try {
  //     $this->products
  //     $this->order_products
  //     foreach ($this->products as $product) {
  //       $product['id']
  //
  //     }
  //   } catch (\Exception $e) {
  //
  //   }
  //
  // }

  /**
   * 生成订单号
   */
  public static function GenerateOrderNumber()
  {
    $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');
    $orderSn = $yCode[intval(date('Y')) - 2017] . strtoupper(dechex(date('m')))
               . date('d') . substr(time(), -5) . substr(microtime(), 2, 5)
               . sprintf('%02d', rand(0, 99));
    return $orderSn;
  }

  /**
   * 对外商品库存检测
   * @param  string $orderID 订单ID
   * @return array          返回订单状态
   */
  public function checkOrderStock($orderID)
  {
    $order_products = OrderProductModel::where('order_id', $orderID)->select();
    $this->order_products = $order_products;
    $this->products = $this->getProductByOrder($order_products);
    $status = $this->getOrderStatus();
    return $status;
  }
}
