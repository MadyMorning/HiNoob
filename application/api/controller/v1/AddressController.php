<?php

namespace app\api\controller\v1;

use think\Request;
use app\api\validate\CreateAddressValidate;
use app\api\validate\UpdateAddressValidate;
use app\api\service\Token;
use app\api\model\UserModel;
use app\api\model\UserAddressModel;
use exception\ResourceException;
use exception\RequestFailedException;
use exception\SuccessMessage;

/**
 * 地址Controller
 */
class AddressController extends BaseController
{

  /**
   * 新增地址
   * @return object 返回提示信息
   */
  public function createAddress()
  {
    // 合法性验证
    $validate = new CreateAddressValidate();
    $validate->gocheck();

    // 有效性验证
    $uid = Token::getTokenUID();
    $userInfo = UserModel::with('address')->find($uid);
    if (!$userInfo) {
      throw new ResourceException('用户不存在');
    }

    $data = $validate->getDataByRule(\request()->post());
    $userArray = $userInfo->toArray();
    foreach ($userArray['address'] as $value) {
      if(!array_diff_assoc($data, $value)){
        throw new RequestFailedException('该信息已存在，不可重复添加');
      }
    }

    // 关联新增
    $addressInfo = $userInfo->address()->save($data);
    if ($addressInfo) {
      throw new SuccessMessage('添加成功');
    }
    throw new RequestFailedException($addressInfo->error);
  }


  public function updateAddress()
  {
    // 合法性验证
    $validate = new UpdateAddressValidate();
    $validate->gocheck();

    // 有效性验证
    $uid = Token::getTokenUID();
    $userInfo = UserModel::find($uid);
    if (!$userInfo) {
      throw new ResourceException('用户不存在');
    }

    $data = $validate->getDataByRule(\request()->put());
    $addressInfo = UserAddressModel::find($data['id']);
    $data = array_diff_assoc($data, $addressInfo->toArray());
    if (!$data) {
      throw new RequestFailedException('没有要更新的信息');
    }

    if ($addressInfo->save($data)) {
      throw new SuccessMessage('更新成功');
    }
    throw new RequestFailedException($addressInfo->error);
  }
}
