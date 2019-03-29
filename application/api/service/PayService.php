<?php
namespace app\api\service;
use app\api\model\OrderModel;
use exception\ParameterException;
use exception\RequestFailedException;
use enum\OrderStatusEnum;
use think\Log;

require_once ROOT_PATH . DS .'public/plugin/WxpayAPI/WxPay.Api.php';

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
   * 
   * @return  array        返回支付参数
   */
  public function pay()
  {
    // 验证订单是否可以支付
    $this->checkOrder();
    // 库存量检测
    $orderService = new OrderService();
    $status = $orderService->checkOrderStock($this->orderID);
    if (!$status['pass']) {
      return $status;
    }

    return $this->makeWXPreOrder($status['orderPrice']);
  }

  /**
   * 验证订单是否可以支付
   */
  private function checkOrder()
  {
    $orderInfo = OrderModel::find($this->orderID);
    // 判断该订单是否存在
    if (!$orderInfo) {
      throw new ParameterException('订单不存在');
    }

    // 判断订单和用户是否匹配
    $user_id = Token::getTokenUID();
    if ($orderInfo->user_id != $user_id) {
      throw new ParameterException('订单和用户不匹配！');
    }

    // 判断订单是否已支付
    if ($orderInfo->status != OrderStatusEnum::UNPAID) {
      if ($orderInfo->status == OrderStatusEnum::CANCEL) {
        throw new RequestFailedException('订单已经取消，无法支付');
      }

      throw new RequestFailedException('订单已经支付过，请勿重复支付！');
    }

    $this->orderNO = $orderInfo->order_no;
  }

  /**
   * 生成预订单
   *
   * @param   string  $totalPrice  订单总价格
   * @return  array              返回支付参数
   */
  private function makeWXPreOrder($totalPrice)
  {
    $openid = Token::getTokenValue('openid');
    $wxOrderData = new \WxPayUnifiedOrder();
    $wxOrderData->SetOpenid($openid);
    $wxOrderData->SetTrade_type('JSAPI');
    $wxOrderData->SetOut_trade_no($this->orderNO);
    $wxOrderData->SetTotal_fee($totalPrice * 100);
    $wxOrderData->SetBody('一家铺子');
    $wxOrderData->SetAttach('一家铺子');
    $wxOrderData->SetTime_start(date("YmdHis"));
    $wxOrderData->SetTime_expire(date("YmdHis", time() + 600));
    $wxOrderData->SetNotify_url(\config('wx_config.pay_callback_url'));

    return $this->getPaysignature($wxOrderData);
  }
   
  /**
   * 获取支付参数
   *
   * @param   object  $wxOrderData  预订单信息
   *
   * @return  array                 返回支付参数
   */
  private function getPaysignature($wxOrderData)
  {
    $config = new WxPayConfig();
    $order = \WxPayApi::unifiedOrder($config, $wxOrderData);
    return $order;
    if ($order['return_code'] != 'SUCCESS' || $order['result_code'] != 'SUCCESS') {
      Log::record($order, 'error');
      Log::record('获取预支付订单失败', 'error');
    }
    $this->savePrepayID($order['prepay_id']);

    $sign = $this->GeneratedSign($order['prepay_id'], $config);
    return $sign;
  }

  /**
   * 将 prepay_id 保存到数据库
   *
   * @param   string  $prepay_id  prepay_id
   */
  private function savePrepayID($prepay_id)
  {
    OrderModel::update([
      'id' => $this->orderID,
      'prepay_id' => $prepay_id
    ]); 
  }

  /**
   * 生成签名
   *
   * @param   string  $prepay_id  prepay_id
   * @param   object  $config     WxPayApi 的对象
   *
   * @return  array              返回签名数据
   */
  private function GeneratedSign($prepay_id, $config)
  {
    $jsapi = new \WxPayJsApiPay();
    $jsapi->SetAppid(\config('wx_config.appid'));
    $timeStamp = time();
    $jsapi->SetTimeStamp("$timeStamp");
    $jsapi->SetNonceStr(\encryption(\WxPayApi::getNonceStr()));
    $jsapi->SetPackage("prepay_id=" . $prepay_id);

    $jsapi->SetPaySign($jsapi->MakeSign($config));
    $parameters = $jsapi->GetValues();
    unset($parameters['app_id']);

    return $parameters;
  }
}
