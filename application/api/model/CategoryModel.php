<?php

namespace app\api\model;

use think\Model;

class CategoryModel extends BaseModel
{
  protected $hidden = ['id', 'delete_time', 'update_time', 'topic_img_id'];

  /**
   * 关联 ImageModel 模型
   */
  public function topicImg()
  {
    return $this->belongsTo('ImageModel', 'topic_img_id', 'id');
  }
}
