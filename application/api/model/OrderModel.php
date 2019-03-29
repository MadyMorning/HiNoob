<?php

namespace app\api\model;

/**
 * 订单Model
 */
class OrderModel extends BaseModel
{
  protected $hidden = ['delete_time', 'update_time'];

  /**
   * 获取器（snap_items 字段）
   */
  public function getSnapItemsAttr($value)
  {
    return json_decode($value);
  }

  /**
   * 获取器（snap_address 字段）
   */
  public function getSnapAddressAttr($value)
  {
    return json_decode($value);
  }
}
