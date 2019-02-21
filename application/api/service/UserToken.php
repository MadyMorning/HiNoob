<?php
namespace app\api\service;
use exception\WeiCateException;
use exception\CacheException;
use app\api\model\UserModel;
use enum\PermissionEnum;

/**
 * 获取用户 Token
 */
class UserToken extends Token
{
  private $code;
  private $appid;
  private $appsecret;
  private $login_url;

  /**
   * 初始化操作
   */
  public function __construct($code)
  {
    $this->code = $code;
    $this->appid = \config('wx_config.appid');
    $this->appsecret = \config('wx_config.appsecret');
    $this->login_url = sprintf(\config('wx_config.login_url'), $this->appid, $this->appsecret, $this->code);
  }

  /**
   * 获取Token
   * @return array       返回openid和session_key
   */
  public function get()
  {
    $result = \curl_request($this->login_url);
    $result = json_decode($result,true);
    if ($result) {
      if (array_key_exists('errcode', $result)) {
        throw new WeiCateException($result['errmsg']);
      } else {
        return $this->grantToken($result);
      }
    } else {
      throw new \Exception('获取openid和session_key失败，微信内部错误');
    }
  }

  /**
   * 发放Token
   * @param  array $result openid和session_key
   * @return [type]         [description]
   */
  public function grantToken($result)
  {
    $openid = $result['openid'];
    $userInfo = UserModel::where('openid', $openid)->find();
    if (!$userInfo) {
      $user = UserModel::create(['openid' => $openid]);
      $uid = $user->id;
    }else {
      $uid = $userInfo->id;
    }

    $cacheValue = $this->prepareCacheValue($result, $uid);
    return $this->saveToCache($cacheValue);
  }

  /**
   * 设置缓存值
   * @param  array $result  openid和session_key
   * @param  string $uid    用户ID
   * @return array          返回要保存到缓存的数据
   */
  private function prepareCacheValue($result, $uid)
  {
    $cacheValue = $result;
    $cacheValue['uid'] = $uid;
    $cacheValue['permission'] = PermissionEnum::user;

    return $cacheValue;
  }

  /**
   * 保存到缓存
   * @param  array $cacheValue 要保存到缓存的值
   * @return string            返回生成的key
   */
  private function saveToCache($cacheValue)
  {
    $key = self::generateToken();
    $value = json_encode($cacheValue);
    $expired_time = \config('config.expired_time');

    $cache = \cache($key, $value, $expired_time);
    if (!$cache) {
      throw new CacheException('服务器缓存异常');
    }
    return $key;
  }
}
