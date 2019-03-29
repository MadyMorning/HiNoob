<?php

namespace app\api\controller\v1;
use app\api\validate\IDMustPositiveInteger;
use app\api\service\PayService;
use app\api\service\PayNotifyCallBack;
use app\api\service\WxPayConfig;

/**
 * 支付Controller
 */
class PayController extends BaseController
{
  /**
   * 获取预订单
   * @param  string $id 订单ID
   * @return object     返回JSON格式数据
   */
  public function getPreOrder($id)
  {
    // 权限验证
    $this->onlyUser();
    // 合法性验证
    (new IDMustPositiveInteger())->gocheck();

    $payservice = new PayService($id);
    $PayParams = $payservice->pay();
    return \json($PayParams);
  }

  /**
   * 微信支付回调函数
   */
  public function notify()
  {
    $config = new WxPayConfig();
    $notify = new PayNotifyCallBack();
    $notify->Handle($config);
  }
}
