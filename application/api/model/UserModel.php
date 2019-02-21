<?php

namespace app\api\model;

use think\Model;

/**
 * 用户Model
 */
class UserModel extends BaseModel
{
  /**
   * 关联 UserAddressModel 模型
   */
  public function address()
  {
    return $this->hasMany('UserAddressModel', 'user_id', 'id');
  }
}
