<?php

namespace app\api\model;

use think\Model;

/**
 * 地址Model
 */
class UserAddressModel extends BaseModel
{
  protected $hidden = ['delete_time', 'update_time', 'user_id'];
}
