<?php

namespace app\api\model;

/**
 * 商品属性Model
 */
class ProductPropertyModel extends BaseModel
{
  protected $hidden = ['id','product_id','delete_time', 'update_time'];
}
