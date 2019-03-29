<?php
namespace app\api\service;

use think\Log;
use app\api\model\OrderModel;
use enum\OrderStatusEnum;
use app\api\model\ProductModel;
use think\Db;

require_once ROOT_PATH . DS ."public/plugin/WxpayAPI/WxPay.Api.php";
require_once ROOT_PATH . DS .'public/plugin/WxpayAPI/WxPay.Notify.php';
// require_once "WxPay.Config.php";

class PayNotifyCallBack extends \WxPayNotify
{

  /**
   * 重写回调处理函数
   * 
	 * @param \WxPayNotifyResults $data 回调解释出的参数
	 * @param \WxPayConfigInterface $config
	 * @param string $msg 如果回调处理失败，可以将错误信息输出到该方法
   * 
	 * @return boolean true回调出来完成不需要继续回调，false回调处理未完成需要继续回调
	 */
  public function NotifyProcess($objData, $config, &$msg)
  {
    $data = $objData->GetValues();
    //TODO 1、进行参数校验
    if(!array_key_exists("return_code", $data) || (array_key_exists("return_code", $data) && $data['return_code'] != "SUCCESS"))
     {
      //TODO失败,不是支付成功的通知
      //如果有需要可以做失败时候的一些清理处理，并且做一些监控
      $msg = "异常异常";
      // return false;
      return true;
    }
    if(!array_key_exists("transaction_id", $data)){ 
      $msg = "输入参数不正确";
      return false;
    }

    //TODO 2、进行签名验证
    try {
      $checkResult = $objData->CheckSign($config);
      if($checkResult == false){ 
        //签名错误
        $msg = "签名错误...";
        // Log::ERROR("签名错误...");
        return false;
      }
    } catch(Exception $e) {
      // Log::ERROR(json_encode($e));
      Log::record(json_encode($e), Log::ERROR);
      return false;
    }

    // //查询订单，判断订单真实性
    // if(!$this->Queryorder($data["transaction_id"])){ 
    //   $msg = "订单查询失败";
    //   return false;
    // }

    //查询订单，判断订单是否已支付
    // 开启事务
    Db::startTrans();
    try {
      $orderInfo = OrderModel::where('order_no', $data['out_trade_no'])->find();
      if ($orderInfo->status == OrderStatusEnum::UNPAID) {
        // 检测库存量
        $orderService = new OrderService();
        $status = $orderService->checkOrderStock($orderInfo->id);
        if ($status['pass']) { //如果有库存
          // 更新订单状态
          $this->updateOrderStatus($orderInfo->id, true);
          // 减少库存
          $this->reduceInventory($status['pStatusArray']);
        }else { //如果无库存
          // 更新订单状态
          $this->updateOrderStatus($orderInfo->id, false);
        }
      }
      // 提交事务
      Db::commit();
      return true; 
    } catch (\Exception $e) {
      // 回滚事务
      DB::rollback();
      Log::record($e, Log::ERROR);
      return false;
    }
  }

  /**
   * 更新订单状态
   *
   * @param   integer  $orderID  订单ID
   * @param   boolean  $pass     库存状态
   */
  private function updateOrderStatus($orderID, $pass)
  {
    $status = $pass ? (OrderStatusEnum::PAID) : (OrderStatusEnum::PAID_BUT_OUT_OF);

    OrderModel::update([
      'id' => $orderID,
      'status' => $status
    ]);
  }

  /**
   * 减少库存
   *
   * @param   array  $pStatusArray  订单中的商品快照
   */
  private function reduceInventory($pStatusArray)
  {
    foreach ($pStatusArray as $value) {
      ProductModel::where('id', $value['id'])->setDec('stock', $value['count']);
    }
  }
}

