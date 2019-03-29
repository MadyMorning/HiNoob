<?php

namespace app\api\model;

/**
 * banner子项 Model
 */
class BannerItemModel extends BaseModel
{
  protected $hidden = ['id', 'delete_time', 'update_time', 'img_id', 'banner_id'];

  /**
   * 关联 ImageModel 模型
   */
  public function Image()
  {
    return $this->belongsTo('ImageModel', 'img_id', 'id');
  }
}
