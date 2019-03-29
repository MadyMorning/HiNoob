<?php

namespace app\api\model;

/**
 * 图片Model
 */
class ImageModel extends BaseModel
{
  protected $hidden = ['id', 'delete_time', 'update_time', 'from'];

  /**
   * 获取器（url 字段）
   *
   * @param   string  $value  url 字段值
   * @param   array  $data   整体数据
   *
   * @return  string          返回处理后的字段值
   */
  public function getUrlAttr($value, $data)
  {
    if ($data['from'] == 1) {
      return \config('config.image_prefix').$value;
    }

    return $value;
  }
}
