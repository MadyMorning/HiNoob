<?php

namespace app\api\controller\v1;

use app\api\validate\CountValidate;
use app\api\validate\IDMustPositiveInteger;
use app\api\model\ProductModel;
use exception\ParameterException;
use exception\ResourceException;

/**
 * 商品Controller
 */
class ProductController extends BaseController
{
  /**
   * 获取最近新品
   * @param  string $count 要展示的数量，默认为10条
   * @return object        返回JSON格式数据
   */
  public function getRecentNewProducts($count = 10)
  {
    // 合法性验证
    (new CountValidate())->gocheck();

    $productsInfo = ProductModel::with('Image')->order('id', 'desc')->limit($count)->select();
    // 有效性验证
    if (!$productsInfo) {
      throw new ParameterException('无效的count');
    }
    $productsInfo = \collection($productsInfo)->hidden(['summary']);
    return \json($productsInfo);
  }

  /**
   * 获取商品详情
   * @param  string $id 商品ID
   * @return object     返回JSON格式数据
   */
  public function getProductDetails($id)
  {
    // 合法性验证
    (new IDMustPositiveInteger())->gocheck();

    $productsInfo = ProductModel::with(['Image',
    'ProductImage' => function ($query)
      {
        $query->with('Image')->order('order', 'esc');
      },
    'ProductProperty'])->find($id);
    // 有效性验证
    if (!$productsInfo) {
      throw new ResourceException('商品不存在');
    }
    
    return \json($productsInfo);
  }
}
