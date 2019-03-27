<?php

namespace app\api\model;

/**
 * 专题Model
 */
class ThemeModel extends BaseModel
{
  protected $hidden = ['delete_time', 'update_time', 'topic_img_id', 'head_img_id'];

  /**
   * 关联ProductModel模型
   */
  public function product()
  {
    return $this->belongsToMany('ProductModel', 'theme_product', 'product_id', 'theme_id');
  }

  /**
   * 关联 ImageModel 模型
   */
  public function topicImg()
  {
    return $this->belongsTo('ImageModel', 'topic_img_id', 'id');
  }

  /**
   * 关联 ImageModel 模型
   */
  public function headImg()
  {
    return $this->belongsTo('ImageModel', 'head_img_id', 'id');
  }
}
