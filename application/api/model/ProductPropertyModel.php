<?php

namespace app\api\model;

use think\Model;

class ProductPropertyModel extends Model
{
  protected $hidden = ['id','product_id','delete_time', 'update_time'];
}
