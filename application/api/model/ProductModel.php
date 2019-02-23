<?php

namespace app\api\model;

/**
 * 商品Model
 */
class ProductModel extends BaseModel
{
  protected $hidden = ['delete_time', 'update_time', 'main_img_url', 'from', 'create_time', 'pivot', 'category_id', 'img_id'];

  /**
   * 关联 ImageModel 模型
   */
  public function Image()
  {
    return $this->belongsTo('ImageModel', 'img_id', 'id');
  }

  /**
   * 关联 ProductImageModel 模型
   */
  public function ProductImage()
  {
    return $this->hasMany('ProductImageModel', 'product_id', 'id');
  }

  /**
   * 关联 ProductPropertyModel 模型
   */
  public function ProductProperty()
  {
    return $this->hasMany('ProductPropertyModel', 'product_id', 'id');
  }
}
