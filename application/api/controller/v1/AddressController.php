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
use exception\PermissionException;
use exception\SuccessMessage;
use enum\PermissionEnum;

/**
 * 地址Controller
 */
class AddressController extends BaseController
{
  // protected $beforeActionList = [
  //   'front' => ['only'=>'createAddress'],
  // ];

  /**
   * 前置方法
   * @return 返回boolean值或错误信息
   */
  protected function front()
  {
    $permission = Token::getTokenValue('permission');
    if ($permission < PermissionEnum::manage) {
      throw new PermissionException('权限不足');
    }

    return true;
  }

  /**
   * 新增地址
   * @return object 返回提示信息
   */
  public function createAddress()
  {
    $this->front();
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
    try {
      $addressInfo = $userInfo->address()->save($data);
      if ($addressInfo) {
        return \json([
          'message' => '添加成功',
          'error_code' => 0
        ]);
      }
    } catch (\Exception $e) {
      throw new RequestFailedException($e->getMessage());
    }
  }

  /**
   * 更新地址
   * @return object 返回提示信息
   */
  public function updateAddress()
  {
    $this->front();
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

    try {
      if ($addressInfo->save($data)) {
        return \json([
          'message' => '添加成功',
          'error_code' => 0
        ]);
      }
    } catch (\Exception $e) {
      throw new RequestFailedException($e->getMessage());
    }
  }
}
