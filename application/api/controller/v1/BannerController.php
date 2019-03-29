<?php

namespace app\api\controller\v1;

use app\api\validate\IDMustPositiveInteger;
use app\api\model\BannerModel;
use exception\ResourceException;

/**
 * Banner Controller
 */
class BannerController extends BaseController
{
  /**
   * Banner
   * @param  string $id 要获取的Banner ID
   * @return object     返回JSON格式数据
   */
  public function getBanner($id)
  {
    // 合法性验证
    (new IDMustPositiveInteger())->gocheck();

    // 有效性验证
    $bannerInfo = BannerModel::with('BannerItem,BannerItem.Image')->find($id);
    // 若无效，抛出异常
    if (!$bannerInfo) {
      throw new ResourceException('无效的ID');
    }
    return \json($bannerInfo);
  }
}
