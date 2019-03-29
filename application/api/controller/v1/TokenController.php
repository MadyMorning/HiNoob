<?php

namespace app\api\controller\v1;

use app\api\validate\CodeValidate;
use app\api\service\UserToken;
use app\api\service\Token;

/**
 * Token Controller
 */
class TokenController extends BaseController
{
  /**
   * 获取Token
   * @param  string $code 客户端发送来的code
   * @return object       返回生成的Token
   */
  public function getToken($code)
  {
    // 合法性验证
    (new CodeValidate())->gocheck();

    $ut = new UserToken($code);
    $token = $ut->get();
    return \json(['token' => $token]);
  }

  /**
   * 验证Token
   *
   * @param   string  $token  前台发送来的Token
   * @return object           返回JSON格式数据
   */
  public function verifyToken($token = '')
  {
    $isToken = Token::verifyToken($token);
    return \json(['isToken' => $isToken]);
  }
}
