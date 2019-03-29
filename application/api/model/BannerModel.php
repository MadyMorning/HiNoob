<?php

namespace app\api\model;

/**
 * Banner Model
 */
class BannerModel extends BaseModel
{
  protected $hidden = ['delete_time', 'update_time'];
  
  /**
   * 关联 BannerItemModel 模型
   */
  public function BannerItem()
  {
    return $this->hasMany('BannerItemModel', 'banner_id', 'id');
  }
}
