<?php

namespace app\api\controller\v1;

use think\Request;
use app\api\validate\IDCollection;
use app\api\validate\IDMustPositiveInteger;
use app\api\model\ThemeModel;
use exception\ResourceException;

/**
 * 专题Controller
 */
class ThemeController extends BaseController
{
  /**
   * 获取专题列表
   * @param  string $ids 以逗号分隔的正整数集
   * @return object      返回JSON格式数据
   */
  public function getTheme($ids)
  {
    // 合法性验证
    (new IDCollection())->gocheck();

    // $themeInfo = ThemeModel::with('topicImg,headImg,product,product.img,product.category')->select($ids);
    $themeInfo = ThemeModel::with('topicImg,headImg')->select($ids);
    // 有效性验证
    if (!$themeInfo) {
      throw new ResourceException('无效的ID');
    }

    return \json($themeInfo);
  }

  /**
   * 获取专题详情
   * @param  string $id 专题ID
   * @return object     返回JSON格式数据
   */
  public function getThemeDetails($id)
  {
    // 合法性验证
    (new IDMustPositiveInteger())->gocheck();

    $themeDetailsInfo = ThemeModel::with(['topicImg','headImg','product','product.Image'])->find($id);
    // 有效性验证
    if (!$themeDetailsInfo) {
      throw new ResourceException('无效的ID');
    }

    return \json($themeDetailsInfo);
  }
}
