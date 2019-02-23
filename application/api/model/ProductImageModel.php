<?php

namespace app\api\model;

/**
 * 商品详情图片Model
 */
class ProductImageModel extends BaseModel
{
  protected $hidden = ['id', 'product_id', 'delete_time', 'update_time', 'img_id'];

  /**
   * 关联 ImageModel 模型
   */
  public function Image()
  {
    return $this->belongsTo('ImageModel', 'img_id', 'id');
  }
}
