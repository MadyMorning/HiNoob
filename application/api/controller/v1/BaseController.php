<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;

/**
 * 公有Controller
 */
class BaseController extends Controller
{
  /**
   * 前置方法(用户及更高级别可访问)
   * @return 返回boolean值或错误信息
   */
  protected function userAndhigher()
  {
    $permission = Token::getTokenValue('permission');
    if ($permission < PermissionEnum::user) {
      throw new PermissionException('权限不足');
    }
    return true;
  }

  /**
   * 前置方法(仅允许用户访问)
   * @return 返回boolean值或错误信息
   */
  protected function onlyUser()
  {
    $permission = Token::getTokenValue('permission');
    if ($permission != PermissionEnum::user) {
      throw new PermissionException('权限不足');
    }
    return true;
  }

}
