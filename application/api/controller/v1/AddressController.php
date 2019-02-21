<?php

namespace app\api\controller\v1;

use think\Request;
use app\api\validate\CreateAddressValidate;

/**
 * 地址Controller
 */
class AddressController extends BaseController
{

  public function createAddress()
  {
    (new CreateAddressValidate())->gocheck();
  }

  public function updateAddress($value='')
  {
    // code...
  }
}
