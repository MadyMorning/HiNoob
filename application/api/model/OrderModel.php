<?php

namespace app\api\model;

/**
 * 订单Model
 */
class OrderModel extends BaseModel
{
  protected $hidden = ['delete_time', 'create_time', 'update_time'];
}
