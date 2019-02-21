<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\api\validate\TokenValidate;
use app\api\service\UserToken;

/**
 * Token Controller
 */
class TokenController extends BaseController
{
  /**
   * 获取Token
   * @param  string $code 客户端发送来的code
   * @return [type]       [description]
   */
  public function getToken($code)
  {
    // 合法性验证
    (new TokenValidate())->gocheck();

    $ut = new UserToken($code);
    $token = $ut->get();
    return \json(['token' => $token]);
  }
}
