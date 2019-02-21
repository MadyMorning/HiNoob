<?php

namespace app\api\model;

/**
 * 图片Model
 */
class ImageModel extends BaseModel
{
  protected $hidden = ['id', 'delete_time', 'update_time', 'from'];

  public function getUrlAttr($value, $data)
  {
    if ($data['from'] == 1) {
      return \config('config.image_prefix').$value;
    }

    return $value;
  }
}
