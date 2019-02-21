<?php

namespace app\api\controller\v1;

use think\Request;
use app\api\model\ProductModel;
use app\api\model\CategoryModel;
use app\api\validate\IDMustPositiveInteger;
use exception\ResourceException;

/**
 * 分类Controller
 */
class CategoryController extends BaseController
{
  /**
   * 获取分类列表
   * @return object 返回JSON格式数据
   */
  public function getCategoryList()
  {
    $categoryInfo = CategoryModel::with('topicImg')->select();
    return \json($categoryInfo);
  }

  /**
   * 获取分类下商品
   * @param  string $id 分类ID
   * @return object     返回JSON格式数据
   */
  public function getProduct($id)
  {
    // 合法性验证
    (new IDMustPositiveInteger())->gocheck();

    $productsInfo = ProductModel::with('Image')->where('category_id', $id)->select();
    // 有效性验证
    if (!$productsInfo) {
      throw new ResourceException('要查询的商品不存在');
    }

    return \json($productsInfo);
  }
}
